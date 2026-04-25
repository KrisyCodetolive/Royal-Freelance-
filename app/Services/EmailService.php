<?php

namespace App\Services;

use App\Enums\EmailSequenceTrigger;
use App\Models\EmailSequence;
use App\Models\EmailSequenceEmail;
use App\Models\EmailSequenceEmailSend;
use App\Models\EmailSequenceSubscription;
use App\Models\Lead;
use App\Models\Tenant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailService
{
    /**
     * Process all active email sequences and send pending emails
     */
    public function processSequences(): int
    {
        $totalSent = 0;
        
        // Get all active subscriptions that need emails (no tenant scope: runs as background job)
        $subscriptions = EmailSequenceSubscription::withoutGlobalScopes()
            ->with(['emailSequence' => fn($q) => $q->withoutGlobalScopes(),
                    'emailSequence.emails' => fn($q) => $q->withoutGlobalScopes(),
                    'lead' => fn($q) => $q->withoutGlobalScopes()])
            ->whereHas('emailSequence', function ($query) {
                $query->withoutGlobalScopes()->where('status', 'active');
            })
            ->where('is_active', true)
            ->whereNull('completed_at')
            ->whereNull('unsubscribed_at')
            ->get();

        foreach ($subscriptions as $subscription) {
            $emailsToSend = $this->getEmailsToSend($subscription);
            
            foreach ($emailsToSend as $email) {
                if ($this->sendSequenceEmail($subscription, $email)) {
                    $totalSent++;
                }
            }
            
            // Check if sequence is completed
            if ($this->isSequenceCompleted($subscription)) {
                $subscription->markCompleted();
            }
        }

        Log::info('Email sequences processed', ['emails_sent' => $totalSent]);
        
        return $totalSent;
    }

    /**
     * Subscribe a lead to an email sequence
     */
    public function subscribeLead(Lead $lead, EmailSequence $sequence): EmailSequenceSubscription
    {
        // Check if already subscribed and not yet completed
        $existing = $sequence->subscriptions()
            ->where('lead_id', $lead->id)
            ->where('is_active', true)
            ->whereNull('completed_at')
            ->first();

        if ($existing) {
            return $existing;
        }

        // Create new subscription
        $subscription = $sequence->subscribeLead($lead);

        Log::info('Lead subscribed to sequence', [
            'lead_id' => $lead->id,
            'sequence_id' => $sequence->id,
        ]);

        return $subscription;
    }

    /**
     * Check if a lead should be subscribed to sequences based on triggers
     */
    public function checkAndSubscribeLeadToSequences(Lead $lead, string $trigger, array $context = []): int
    {
        $subscribed = 0;
        
        // Get sequences for this tenant with the specified trigger.
        // A sequence scoped to a funnel only applies to leads from that funnel.
        // A sequence with no funnel_id is global and applies to all tenant leads.
        $sequences = EmailSequence::query()
            ->where('tenant_id', $lead->tenant_id)
            ->where('status', 'active')
            ->where('trigger', $trigger)
            ->where(function ($query) use ($lead) {
                $query->whereNull('funnel_id')
                    ->orWhere('funnel_id', $lead->funnel_id);
            })
            ->get();

        foreach ($sequences as $sequence) {
            // Check if lead should be subscribed based on conditions
            if ($sequence->checkTriggerConditions($lead)) {
                $this->subscribeLead($lead, $sequence);
                $subscribed++;
            }
        }

        return $subscribed;
    }

    /**
     * Get emails that should be sent for a subscription
     */
    protected function getEmailsToSend(EmailSequenceSubscription $subscription): Collection
    {
        return $subscription->emailSequence
            ->emails()
            ->withoutGlobalScopes()
            ->active()
            ->whereDoesntHave('sends', function ($query) use ($subscription) {
                $query->withoutGlobalScopes()->where('subscription_id', $subscription->id);
            })
            ->get()
            ->filter(function ($email) use ($subscription) {
                $sendTime = $subscription->subscribed_at->addHours($email->send_after_hours);
                return now() >= $sendTime;
            });
    }

    /**
     * Send a specific email from a sequence
     */
    protected function sendSequenceEmail(EmailSequenceSubscription $subscription, EmailSequenceEmail $email): bool
    {
        try {
            $lead = $subscription->lead;
            
            // Create email send record
            $emailSend = EmailSequenceEmailSend::create([
                'tenant_id' => $subscription->tenant_id,
                'subscription_id' => $subscription->id,
                'email_sequence_email_id' => $email->id,
                'lead_id' => $lead->id,
                'subject' => $email->getPersonalizedSubject($lead),
                'content' => $email->getPersonalizedContent($lead),
            ]);

            // Send the email (you'll need to implement your email provider)
            $this->sendEmail(
                $lead->email,
                $emailSend->subject,
                $emailSend->getFinalContent()
            );

            // Mark as sent
            $emailSend->markAsSent();

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send sequence email', [
                'subscription_id' => $subscription->id,
                'email_id' => $email->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Check if a subscription has completed its sequence
     */
    protected function isSequenceCompleted(EmailSequenceSubscription $subscription): bool
    {
        $totalEmails = $subscription->emailSequence->emails()->active()->count();
        $sentEmails = $subscription->emailSends()->whereNotNull('sent_at')->count();
        
        return $sentEmails >= $totalEmails;
    }

    /**
     * Send an email (implement with your preferred email provider)
     */
    protected function sendEmail(string $to, string $subject, string $content): void
    {
        // TODO: Implement with your email provider (SendGrid, Mailgun, etc.)
        // For now, we'll use Laravel's Mail facade
        
        Mail::html($content, function ($message) use ($to, $subject) {
            $message->to($to)
                   ->subject($subject);
        });
    }

    /**
     * Create default email sequences for a tenant
     */
    public function createDefaultSequences(Tenant $tenant): void
    {
        $this->createWelcomeSequence($tenant);
        $this->createNurturingSequence($tenant);
        $this->createReengagementSequence($tenant);
    }

    /**
     * Create a welcome email sequence
     */
    protected function createWelcomeSequence(Tenant $tenant): EmailSequence
    {
        $sequence = EmailSequence::create([
            'tenant_id' => $tenant->id,
            'name' => 'Séquence de Bienvenue',
            'description' => 'Emails automatiques envoyés aux nouveaux prospects',
            'trigger' => EmailSequenceTrigger::FORM_SUBMIT,
            'status' => 'active',
        ]);

        // Email 1: Immédiat - Bienvenue
        EmailSequenceEmail::create([
            'tenant_id' => $tenant->id,
            'email_sequence_id' => $sequence->id,
            'subject' => 'Bienvenue {first_name} ! Votre cadeau vous attend...',
            'content' => $this->getWelcomeEmailTemplate(),
            'send_after_hours' => 0,
            'is_active' => true,
        ]);

        // Email 2: 24h - Contenu de valeur
        EmailSequenceEmail::create([
            'tenant_id' => $tenant->id,
            'email_sequence_id' => $sequence->id,
            'subject' => '{first_name}, voici vos 3 premiers secrets...',
            'content' => $this->getValueEmailTemplate(),
            'send_after_hours' => 24,
            'is_active' => true,
        ]);

        // Email 3: 72h - Social proof
        EmailSequenceEmail::create([
            'tenant_id' => $tenant->id,
            'email_sequence_id' => $sequence->id,
            'subject' => 'Ce que disent nos 15 000+ membres...',
            'content' => $this->getSocialProofEmailTemplate(),
            'send_after_hours' => 72,
            'is_active' => true,
        ]);

        return $sequence;
    }

    /**
     * Create a nurturing sequence for warm leads
     */
    protected function createNurturingSequence(Tenant $tenant): EmailSequence
    {
        $sequence = EmailSequence::create([
            'tenant_id' => $tenant->id,
            'name' => 'Nurturing Prospects Chauds',
            'description' => 'Séquence pour convertir les prospects engagés',
            'trigger' => EmailSequenceTrigger::SCORE_THRESHOLD,
            'trigger_conditions' => ['min_score' => 25],
            'status' => 'active',
        ]);

        // Email 1: Immédiat - Offre spéciale
        EmailSequenceEmail::create([
            'tenant_id' => $tenant->id,
            'email_sequence_id' => $sequence->id,
            'subject' => '{first_name}, offre exclusive pour vous !',
            'content' => $this->getOfferEmailTemplate(),
            'send_after_hours' => 0,
            'is_active' => true,
        ]);

        // Email 2: 48h - Urgence
        EmailSequenceEmail::create([
            'tenant_id' => $tenant->id,
            'email_sequence_id' => $sequence->id,
            'subject' => 'Plus que 24h pour profiter de -50%...',
            'content' => $this->getUrgencyEmailTemplate(),
            'send_after_hours' => 48,
            'is_active' => true,
        ]);

        return $sequence;
    }

    /**
     * Create reengagement sequence for inactive leads
     */
    protected function createReengagementSequence(Tenant $tenant): EmailSequence
    {
        return EmailSequence::create([
            'tenant_id' => $tenant->id,
            'name' => 'Réactivation Prospects Inactifs',
            'description' => 'Réengager les prospects inactifs depuis 7 jours',
            'trigger' => EmailSequenceTrigger::INACTIVITY,
            'trigger_conditions' => ['days' => 7],
            'status' => 'active',
        ]);
    }

    // Email templates
    protected function getWelcomeEmailTemplate(): string
    {
        return '<h1>Bienvenue {first_name} !</h1>
            <p>Merci de faire confiance à <strong>{tenant_name}</strong>.</p>
            <p>Vous venez de faire le premier pas vers <strong>[VOTRE RÉSULTAT]</strong>.</p>
            <p>Dans les prochaines heures, vous allez recevoir :</p>
            <ul>
                <li>✅ Vos 3 premiers secrets exclusifs</li>
                <li>✅ Nos meilleures stratégies</li>
                <li>✅ Des études de cas réels</li>
            </ul>
            <p>À très bientôt,<br>L\'équipe {tenant_name}</p>';
    }

    protected function getValueEmailTemplate(): string
    {
        return '<h1>Vos 3 secrets exclusifs</h1>
            <p>Bonjour {first_name},</p>
            <p>Comme promis, voici vos 3 premiers secrets pour <strong>[RÉSULTAT]</strong> :</p>
            <blockquote>
                <h3>🔥 Secret #1 : [TITRE SECRET]</h3>
                <p>[Description du secret avec valeur concrète]</p>
            </blockquote>
            <p>Ces techniques ont déjà aidé plus de 15 000 personnes...</p>
            <p>Demain, je vous révèle le secret #2 encore plus puissant !</p>
            <p>À demain,<br>L\'équipe {tenant_name}</p>';
    }

    protected function getSocialProofEmailTemplate(): string
    {
        return '<h1>Ils ont réussi, pourquoi pas vous ?</h1>
            <p>Salut {first_name},</p>
            <p>Nos membres partagent leurs résultats :</p>
            <blockquote>
                "En 30 jours, j\'ai obtenu [RÉSULTAT CONCRET]. Merci !" - Marie, 34 ans
            </blockquote>
            <blockquote>
                "Incroyable ! J\'ai dépassé mes objectifs." - Thomas, 28 ans
            </blockquote>
            <p>Vous aussi, vous pouvez obtenir ces résultats.</p>
            <p><a href="#" class="btn">Découvrir la méthode complète</a></p>';
    }

    protected function getOfferEmailTemplate(): string
    {
        return '<h1>🎯 Offre Exclusive pour vous</h1>
            <p>Bonjour {first_name},</p>
            <p>Vous avez montré un vrai engagement, et ça mérite une récompense !</p>
            <blockquote>
                <h2>-50% sur notre Formation Premium</h2>
                <p>Seulement pour vous !</p>
                <p><strong><strike>197€</strike> → 97€</strong></p>
            </blockquote>
            <p><a href="#" class="btn">🔥 J\'en profite maintenant</a></p>
            <p><small>Offre valable 48h seulement</small></p>';
    }

    protected function getUrgencyEmailTemplate(): string
    {
        return '<h1>⏰ Plus que 24h !</h1>
            <p>Bonjour {first_name},</p>
            <p><strong>Votre offre à -50% expire dans 24h...</strong></p>
            <p>Ne laissez pas passer cette opportunité unique !</p>
            <blockquote>
                <h2>Formation Premium : 97€ au lieu de 197€</h2>
                <p>Offre expire le [DATE] à minuit</p>
            </blockquote>
            <p><a href="#" class="btn">⚡ Réserver ma place maintenant</a></p>
            <p>Après demain, le prix repassera à 197€...</p>';
    }
}
