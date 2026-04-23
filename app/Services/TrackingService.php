<?php

namespace App\Services;

use App\Enums\EventType;
use App\Enums\LeadStatus;
use App\Enums\EmailSequenceTrigger;
use App\Models\Event;
use App\Models\Funnel;
use App\Models\Lead;
use App\Models\Page;
use App\Models\Tag;
use App\Models\User;
use App\Services\UserAgentService;
use Illuminate\Support\Str;

class TrackingService
{
    protected ScoringService $scoringService;
    protected AlertService $alertService;
    protected EmailService $emailService;

    public function __construct(
        ScoringService $scoringService, 
        AlertService $alertService,
        EmailService $emailService
    ) {
        $this->scoringService = $scoringService;
        $this->alertService = $alertService;
        $this->emailService = $emailService;
    }

    /**
     * Track a visitor: handle cookie identification, create anonymous lead if needed, update geo, and track page view.
     */
    public function trackVisitor(Funnel $funnel, Page $page): Lead
    {
        // 0. Capture de l'attribution (Lead Attribution)
        if ($ref = request()->get('ref')) {
            \Illuminate\Support\Facades\Cookie::queue('rlm_ref', $ref, 60 * 24 * 30);
            session(['rlm_ref' => $ref]);
        }

        // 1. Identification / Tracking du Visiteur via Cookie
        $visitorUuid = \Illuminate\Support\Facades\Cookie::get('rlm_visitor');
        $lead = null;

        if ($visitorUuid) {
            $lead = Lead::where('uuid', $visitorUuid)->first();
        }

        // Si nouveau visiteur (ou cookie perdu), on crée un "Lead Anonyme"
        if (!$lead) {
            // Parse user agent pour extraire device, browser, OS
            $userAgentData = UserAgentService::parse(request()->userAgent());

            $lead = Lead::create([
                'uuid' => (string) \Illuminate\Support\Str::uuid(),
                'tenant_id' => $funnel->tenant_id,
                'funnel_id' => $funnel->id,
                'status' => \App\Enums\LeadStatus::COLD, // Visiteur froid
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'device_type' => $userAgentData['device_type'],
                'browser' => $userAgentData['browser'],
                'browser_version' => $userAgentData['browser_version'],
                'os' => $userAgentData['os'],
                'os_version' => $userAgentData['os_version'],
                'language' => UserAgentService::detectLanguage(request()->header('Accept-Language')),
                'source' => request()->get('utm_source'),
                'medium' => request()->get('utm_medium'),
                'campaign' => request()->get('utm_campaign'),
                'referrer' => request()->header('referer'),
                'last_activity_at' => now(),
            ]);

            // On pose un cookie pour le reconnaître (durée 1 an)
            \Illuminate\Support\Facades\Cookie::queue('rlm_visitor', $lead->uuid, 60 * 24 * 365);

            try {
                app(\App\Services\GeolocationService::class)->updateLeadLocation($lead, request()->ip());
            } catch (\Exception $e) {
                // Log silent
            }
        }

        // 2. Enregistrement de la vue de page (Event)
        $this->trackPageView($lead, $page);

        // 3. Gestion des Visiteurs Récurrents (Popup & Statut)
        $this->handleRepeatVisitor($lead);

        return $lead;
    }

    /**
     * Handle logic for repeat visitors (2+ visits): trigger capture popup
     * Note: Status upgrade is now handled by assignFunnelProgressionTag()
     */
    protected function handleRepeatVisitor(Lead $lead): void
    {
        if ($lead->email || in_array($lead->status, [LeadStatus::CLIENT, LeadStatus::MEMBER])) {
            return;
        }

        $visitCount = $lead->events()->where('type', EventType::PAGE_VIEW)->count();

        if ($visitCount >= 2) {
            $lead->setAttribute('show_capture_popup', true);
        }
    }

    /**
     * Track a page view
     */
    public function trackPageView(Lead $lead, Page $page): Event
    {
        $event = $this->createEvent($lead, EventType::PAGE_VIEW, [
            'page_id' => $page->id,
            'page_title' => $page->title,
            'page_type' => $page->type->value,
        ]);

        $page->incrementViews();

        // Auto-tagging basé sur la progression dans le tunnel
        $this->assignFunnelProgressionTag($lead, $page);

        $this->emailService->checkAndSubscribeLeadToSequences($lead, EmailSequenceTrigger::PAGE_VIEW->value);

        return $event;
    }

    /**
     * Track form submission
     */
    public function trackFormSubmit(Lead $lead, Page $page, array $formData = []): Event
    {
        $event = $this->createEvent($lead, EventType::FORM_SUBMIT, [
            'page_id' => $page->id,
            'form_data' => $formData,
        ]);

        $page->incrementSubmissions();
        $this->addAutoTag($lead, 'inscription');

        return $event;
    }

    /**
     * Track video progress
     */
    public function trackVideoProgress(Lead $lead, Page $page, int $percentage, ?string $videoId = null): Event
    {
        $type = match (true) {
            $percentage >= 100 => EventType::VIDEO_100,
            $percentage >= 75 => EventType::VIDEO_75,
            $percentage >= 50 => EventType::VIDEO_50,
            $percentage >= 25 => EventType::VIDEO_25,
            default => EventType::VIDEO_PLAY,
        };

        $event = $this->createEvent($lead, $type, [
            'page_id' => $page->id,
            'percentage' => $percentage,
            'video_id' => $videoId,
        ]);

        if ($percentage >= 100) {
            $this->addAutoTag($lead, "video_complete_{$page->sort_order}");
        }

        $this->alertService->checkEventAlerts($lead, $type);

        return $event;
    }

    /**
     * Track CTA click
     */
    public function trackCtaClick(Lead $lead, Page $page, ?string $buttonId = null): Event
    {
        $event = $this->createEvent($lead, EventType::CTA_CLICK, [
            'page_id' => $page->id,
            'button_id' => $buttonId,
        ]);

        // Si le CTA est cliqué sur la dernière page (page 4), marquer comme Client
        if ($page->isLastPage()) {
            $this->markAsClient($lead, $page);
        }

        return $event;
    }

    /**
     * Track WhatsApp click
     */
    public function trackWhatsAppClick(Lead $lead, Page $page): Event
    {
        $event = $this->createEvent($lead, EventType::WHATSAPP_CLICK, [
            'page_id' => $page->id,
        ]);

        $this->addAutoTag($lead, 'clic_whatsapp');
        $this->alertService->checkEventAlerts($lead, EventType::WHATSAPP_CLICK);

        return $event;
    }

    /**
     * Track payment click
     */
    public function trackPaymentClick(Lead $lead, Page $page): Event
    {
        $event = $this->createEvent($lead, EventType::PAYMENT_CLICK, [
            'page_id' => $page->id,
        ]);

        $this->addAutoTag($lead, 'clic_paiement');

        return $event;
    }

    /**
     * Track conversion
     */
    public function trackConversion(Lead $lead, array $conversionData = []): Event
    {
        $event = $this->createEvent($lead, EventType::CONVERSION, $conversionData);

        $lead->update([
            'status' => LeadStatus::CLIENT,
            'converted_at' => now(),
        ]);

        $this->addAutoTag($lead, 'client');
        $lead->funnel?->updateStats();

        if ($lead->brought_by) {
            $lead->funnel?->updateStatsForCommercial(User::find($lead->brought_by));
        }

        return $event;
    }

    /**
     * Create a lead from form submission
     */
    public function createLeadFromForm(Funnel $funnel, array $formData, ?User $broughtBy = null): Lead
    {
        // 0. Résolution de l'attribution
        if (!$broughtBy) {
            $refId = session('rlm_ref') ?? request()->cookie('rlm_ref');
            if ($refId) {
                $broughtBy = User::find($refId);
            }
        }

        // 1. Tenter de récupérer le lead existant (visiteur anonyme) via le cookie
        $visitorUuid = request()->cookie('rlm_visitor');
        $lead = null;
        if ($visitorUuid) {
            $lead = Lead::where('uuid', $visitorUuid)->first();
        }

        // 2. Détection intelligente des champs si les clés standards manquent
        $email = $formData['email'] ?? null;
        $phone = $formData['phone'] ?? null;
        $firstName = $formData['first_name'] ?? $formData['name'] ?? null;
        $lastName = $formData['last_name'] ?? null;
        $country = $formData['country'] ?? null;
        $city = $formData['city'] ?? null;

        if ($lead) {
            // Si le lead existant a déjà un email différent, on considère que c'est une nouvelle personne
            // (cas des ordinateurs partagés)
            if ($lead->email && $email && strtolower($lead->email) !== strtolower($email)) {
                \Log::info('🔄 [LEAD CAPTURE] Email différent détecté, création d\'un nouveau profil pour cet email', [
                    'old_email' => $lead->email,
                    'new_email' => $email
                ]);
                $lead = null; // Forcer la création d'un nouveau lead
            }
        }

        if (!$email || !$phone || !$firstName) {
            \Log::info('🔍 [LEAD SMART DETECTION] Tentative sur les champs', ['keys' => array_keys($formData)]);
            foreach ($formData as $key => $value) {
                if (empty($value) || is_array($value))
                    continue;
                $lowKey = strtolower($key);

                // Email detection
                if (!$email && (str_contains($lowKey, 'email') || str_contains($lowKey, 'mail') || filter_var($value, FILTER_VALIDATE_EMAIL))) {
                    $email = $value;
                    \Log::info('📧 [LEAD SMART DETECTION] Email trouvé', ['key' => $key, 'val' => $value]);
                }
                // Phone detection (On évite de prendre l'email pour le tel)
                if (!$phone && !filter_var($value, FILTER_VALIDATE_EMAIL) && (str_contains($lowKey, 'phone') || str_contains($lowKey, 'tel') || str_contains($lowKey, 'tél') || str_contains($lowKey, 'whatsapp') || str_contains($lowKey, 'mobile') || str_contains($lowKey, 'contact'))) {
                    $phone = $value;
                    \Log::info('📞 [LEAD SMART DETECTION] Téléphone trouvé', ['key' => $key, 'val' => $value]);
                }
                // Name detection
                if (!$firstName && (str_contains($lowKey, 'name') || str_contains($lowKey, 'nom') || str_contains($lowKey, 'prenom'))) {
                    $firstName = $value;
                    \Log::info('👤 [LEAD SMART DETECTION] Nom trouvé', ['key' => $key, 'val' => $value]);
                }
            }
        }

        // Parse user agent pour extraire device, browser, OS
        $userAgentData = UserAgentService::parse(request()->userAgent());

        $data = [
            'tenant_id' => $funnel->tenant_id,
            'funnel_id' => $funnel->id,
            'assigned_to' => $funnel->assigned_to,
            'brought_by' => $broughtBy?->id,
            'email' => $email,
            'phone' => $phone,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'source' => $formData['utm_source'] ?? request()->get('utm_source') ?? $lead?->source,
            'medium' => $formData['utm_medium'] ?? request()->get('utm_medium') ?? $lead?->medium,
            'campaign' => $formData['utm_campaign'] ?? request()->get('utm_campaign') ?? $lead?->campaign,
            'referrer' => request()->header('referer') ?? $lead?->referrer,
            'form_data' => $formData,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'device_type' => $userAgentData['device_type'],
            'browser' => $userAgentData['browser'],
            'browser_version' => $userAgentData['browser_version'],
            'os' => $userAgentData['os'],
            'os_version' => $userAgentData['os_version'],
            'language' => UserAgentService::detectLanguage(request()->header('Accept-Language')),
            'country' => $country ?? $lead?->country,
            'city' => $city ?? $lead?->city,
            'custom_fields' => array_merge($lead?->custom_fields ?? [], $formData['custom_fields'] ?? []),
            'status' => LeadStatus::COLD,
            'last_activity_at' => now(),
        ];

        if ($lead) {
            \Log::info('💾 [LEAD CAPTURE] Mise à jour lead existant', ['id' => $lead->id, 'uuid' => $lead->uuid, 'name' => $firstName]);
            // Mise à jour du lead existant
            $lead->update($data);
        } else {
            // Création d'un nouveau lead si non trouvé ou si identité différente
            $data['uuid'] = Str::uuid();
            $data['score'] = 0;
            $lead = Lead::create($data);
            \Log::info('🆕 [LEAD CAPTURE] Nouveau lead créé', ['id' => $lead->id, 'uuid' => $lead->uuid, 'name' => $firstName]);

            // Poser le cookie pour la suite
            \Illuminate\Support\Facades\Cookie::queue('rlm_visitor', $lead->uuid, 60 * 24 * 365);
        }

        // Mise à jour des Stats Commerciales (Table Pivot funnel_user)
        if ($broughtBy) {
            try {
                \Illuminate\Support\Facades\DB::table('funnel_user')
                    ->updateOrInsert(
                        ['funnel_id' => $funnel->id, 'user_id' => $broughtBy->id],
                        ['updated_at' => now()]
                    );

                \Illuminate\Support\Facades\DB::table('funnel_user')
                    ->where('funnel_id', $funnel->id)
                    ->where('user_id', $broughtBy->id)
                    ->increment('leads_count');

            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Failed to increment commercial stats: " . $e->getMessage());
            }
        }

        if ($source = $lead->source) {
            $this->addAutoTag($lead, "source_{$source}");
        }

        $this->addAutoTag($lead, "funnel_{$funnel->slug}");
        $this->alertService->createNewRegistrationAlert($lead);

        if ($capturePage = $funnel->pages()->where('type', 'capture')->first()) {
            $this->trackFormSubmit($lead, $capturePage, $formData);
        }

        return $lead;
    }

    /**
     * Create an event
     */
    protected function createEvent(Lead $lead, EventType $type, array $data = []): Event
    {
        // Parse user agent pour chaque événement
        $userAgentData = UserAgentService::parse(request()->userAgent());

        $event = Event::create([
            'lead_id' => $lead->id,
            'page_id' => $data['page_id'] ?? null,
            'type' => $type,
            'data' => $data,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'device_type' => $userAgentData['device_type'],
            'browser' => $userAgentData['browser'],
            'os' => $userAgentData['os'],
            'time_spent_seconds' => $data['time_spent_seconds'] ?? null,
            'scroll_depth_percentage' => $data['scroll_depth_percentage'] ?? null,
        ]);

        $lead->update(['last_activity_at' => now()]);

        return $event;
    }

    /**
     * Assign funnel progression tag based on page position.
     * Page 1 = Froid, Page 2 = Tiède, Page 3 = Chaud, Page 4 = Ultra Chaud
     */
    protected function assignFunnelProgressionTag(Lead $lead, Page $page): void
    {
        $position = $page->getPositionInFunnel();

        // Mapping position → tag + statut
        $progressionMap = [
            1 => ['tag' => 'Froid',       'slug' => 'froid',       'color' => '#3B82F6', 'status' => LeadStatus::COLD],
            2 => ['tag' => 'Tiède',       'slug' => 'tiede',       'color' => '#F59E0B', 'status' => LeadStatus::WARM],
            3 => ['tag' => 'Chaud',       'slug' => 'chaud',       'color' => '#EF4444', 'status' => LeadStatus::HOT],
            4 => ['tag' => 'Ultra Chaud', 'slug' => 'ultra-chaud', 'color' => '#8B5CF6', 'status' => LeadStatus::ULTRA_HOT],
        ];

        // Si la position n'est pas dans notre map (tunnel avec plus de 4 pages), ignorer
        if (!isset($progressionMap[$position])) {
            return;
        }

        $current = $progressionMap[$position];

        // Ne pas rétrograder : si le lead a déjà un statut supérieur, ne pas changer
        if ($lead->status && $lead->status->priority() >= $current['status']->priority()) {
            // Quand même ajouter le tag de la page visitée pour le tracking
            $this->addProgressionTag($lead, $current);
            return;
        }

        // Retirer les anciens tags de progression (on ne garde que le plus avancé)
        $allProgressionSlugs = array_column($progressionMap, 'slug');
        $existingProgressionTags = Tag::where('tenant_id', $lead->tenant_id)
            ->whereIn('slug', $allProgressionSlugs)
            ->where('is_auto', true)
            ->pluck('id');

        if ($existingProgressionTags->isNotEmpty()) {
            $lead->tags()->detach($existingProgressionTags);
        }

        // Ajouter le nouveau tag de progression
        $this->addProgressionTag($lead, $current);

        // Mettre à jour le statut du lead
        $lead->update(['status' => $current['status']]);

        \Log::info('🏷️ [FUNNEL PROGRESSION] Tag et statut mis à jour', [
            'lead_id' => $lead->id,
            'page_position' => $position,
            'tag' => $current['tag'],
            'new_status' => $current['status']->value,
            'funnel_id' => $page->funnel_id,
        ]);
    }

    /**
     * Add a progression tag with proper name, color, and auto_trigger.
     */
    protected function addProgressionTag(Lead $lead, array $tagData): void
    {
        $tag = Tag::firstOrCreate(
            [
                'tenant_id' => $lead->tenant_id,
                'slug' => $tagData['slug'],
            ],
            [
                'name' => $tagData['tag'],
                'color' => $tagData['color'],
                'is_auto' => true,
                'auto_trigger' => 'funnel_page_' . $tagData['slug'],
            ]
        );

        if (!$lead->tags->contains($tag->id)) {
            $lead->addTag($tag);
            $this->emailService->checkAndSubscribeLeadToSequences($lead, EmailSequenceTrigger::TAG_ASSIGNED->value);
        }
    }

    /**
     * Mark a lead as Client when they click the CTA on the last page ("Rejoindre")
     */
    protected function markAsClient(Lead $lead, Page $page): void
    {
        // Ne pas re-convertir un client déjà converti
        if ($lead->status === LeadStatus::CLIENT || $lead->converted_at) {
            return;
        }

        // Retirer les tags de progression
        $progressionSlugs = ['froid', 'tiede', 'chaud', 'ultra-chaud'];
        $progressionTagIds = Tag::where('tenant_id', $lead->tenant_id)
            ->whereIn('slug', $progressionSlugs)
            ->where('is_auto', true)
            ->pluck('id');

        if ($progressionTagIds->isNotEmpty()) {
            $lead->tags()->detach($progressionTagIds);
        }

        // Ajouter le tag "Client"
        $clientTag = Tag::firstOrCreate(
            [
                'tenant_id' => $lead->tenant_id,
                'slug' => 'client',
            ],
            [
                'name' => 'Client',
                'color' => '#10B981',
                'is_auto' => true,
                'auto_trigger' => 'funnel_cta_last_page',
            ]
        );

        $lead->addTag($clientTag);

        // Mettre à jour le statut
        $lead->update([
            'status' => LeadStatus::CLIENT,
            'converted_at' => now(),
        ]);

        // Mettre à jour les stats
        $lead->funnel?->updateStats();

        if ($lead->brought_by) {
            $lead->funnel?->updateStatsForCommercial(User::find($lead->brought_by));
        }

        \Log::info('🎉 [FUNNEL PROGRESSION] Lead converti en CLIENT via CTA dernière page', [
            'lead_id' => $lead->id,
            'lead_name' => $lead->getDisplayName(),
            'page_id' => $page->id,
            'funnel_id' => $page->funnel_id,
        ]);
    }

    /**
     * Add auto tag to lead
     */
    protected function addAutoTag(Lead $lead, string $tagSlug): void
    {
        $tag = Tag::firstOrCreate(
            [
                'tenant_id' => $lead->tenant_id,
                'slug' => Str::slug($tagSlug),
            ],
            [
                'name' => $tagSlug,
                'is_auto' => true,
            ]
        );

        $lead->addTag($tag);
    }

    /**
     * Get funnel analytics
     */
    public function getFunnelAnalytics(Funnel $funnel, ?string $period = 'month'): array
    {
        $startDate = match ($period) {
            'day' => now()->startOfDay(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'year' => now()->startOfYear(),
            default => now()->startOfMonth(),
        };

        $events = Event::whereHas('lead', fn($q) => $q->where('funnel_id', $funnel->id))
            ->where('created_at', '>=', $startDate);

        $pageViews = (clone $events)->where('type', EventType::PAGE_VIEW)->count();
        $formSubmits = (clone $events)->where('type', EventType::FORM_SUBMIT)->count();

        return [
            'page_views' => $pageViews,
            'form_submissions' => $formSubmits,
            'capture_rate' => $pageViews > 0 ? round($formSubmits / $pageViews * 100, 2) : 0,
            'video_starts' => (clone $events)->where('type', EventType::VIDEO_PLAY)->count(),
            'video_completions' => (clone $events)->where('type', EventType::VIDEO_100)->count(),
            'whatsapp_clicks' => (clone $events)->where('type', EventType::WHATSAPP_CLICK)->count(),
            'payment_clicks' => (clone $events)->where('type', EventType::PAYMENT_CLICK)->count(),
            'conversions' => (clone $events)->where('type', EventType::CONVERSION)->count(),
        ];
    }
}
