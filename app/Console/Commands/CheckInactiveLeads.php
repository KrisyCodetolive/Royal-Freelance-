<?php

namespace App\Console\Commands;

use App\Models\Lead;
use App\Models\Tag;
use Illuminate\Console\Command;

class CheckInactiveLeads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leads:check-inactive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vérifie les leads inactifs et assigne automatiquement les tags de relance';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Vérification des leads inactifs...');

        // Leads inactifs depuis 7 jours
        $tag7j = Tag::where('auto_trigger', 'inactive_7_days')
            ->where('is_auto', true)
            ->first();

        if ($tag7j) {
            $inactifs7j = Lead::where('last_activity_at', '<', now()->subDays(7))
                ->where('last_activity_at', '>=', now()->subDays(14))
                ->get();

            $count7j = 0;
            foreach ($inactifs7j as $lead) {
                if (!$lead->tags->contains($tag7j->id)) {
                    $lead->tags()->attach($tag7j->id, [
                        'assigned_by' => null,
                        'assigned_at' => now(),
                    ]);
                    $count7j++;
                    
                    app(\App\Services\EmailService::class)->checkAndSubscribeLeadToSequences($lead, \App\Enums\EmailSequenceTrigger::TAG_ASSIGNED->value);
                }
                
                app(\App\Services\EmailService::class)->checkAndSubscribeLeadToSequences($lead, \App\Enums\EmailSequenceTrigger::INACTIVITY->value);
            }

            $this->info("✓ {$count7j} leads marqués comme inactifs 7 jours");
        }

        // Leads inactifs depuis 14 jours
        $tag14j = Tag::where('auto_trigger', 'inactive_14_days')
            ->where('is_auto', true)
            ->first();

        if ($tag14j) {
            $inactifs14j = Lead::where('last_activity_at', '<', now()->subDays(14))
                ->get();

            $count14j = 0;
            foreach ($inactifs14j as $lead) {
                // Retirer le tag 7j si présent
                if ($tag7j && $lead->tags->contains($tag7j->id)) {
                    $lead->tags()->detach($tag7j->id);
                }

                // Ajouter le tag 14j
                if (!$lead->tags->contains($tag14j->id)) {
                    $lead->tags()->attach($tag14j->id, [
                        'assigned_by' => null,
                        'assigned_at' => now(),
                    ]);
                    $count14j++;

                    app(\App\Services\EmailService::class)->checkAndSubscribeLeadToSequences($lead, \App\Enums\EmailSequenceTrigger::TAG_ASSIGNED->value);
                }
                
                app(\App\Services\EmailService::class)->checkAndSubscribeLeadToSequences($lead, \App\Enums\EmailSequenceTrigger::INACTIVITY->value);
            }

            $this->info("✓ {$count14j} leads marqués comme inactifs 14 jours");
        }

        $this->info('✓ Vérification terminée !');
        return Command::SUCCESS;
    }
}
