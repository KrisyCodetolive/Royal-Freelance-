<?php

namespace App\Observers;

use App\Events\LeadStatusChanged;
use App\Models\Lead;
use App\Models\Tag;
use App\Services\AlertService;
use App\Services\ScoringService;

class LeadObserver
{
    protected ScoringService $scoringService;
    protected AlertService $alertService;

    public function __construct(ScoringService $scoringService, AlertService $alertService)
    {
        $this->scoringService = $scoringService;
        $this->alertService = $alertService;
    }

    /**
     * Handle the Lead "created" event.
     */
    public function created(Lead $lead): void
    {
        // Update funnel stats
        $lead->funnel?->updateStats();

        // Create new registration alert is handled in TrackingService
    }

    /**
     * Handle the Lead "updated" event.
     */
    public function updated(Lead $lead): void
    {
        // Update funnel stats if status changed
        if ($lead->isDirty('status')) {
            $lead->funnel?->updateStats();

            // Update commercial stats if applicable
            if ($lead->brought_by) {
                $commercial = $lead->broughtBy;
                if ($commercial) {
                    $lead->funnel?->updateStatsForCommercial($commercial);
                }
            }
        }

        // Auto-tagging basé sur le score
        if ($lead->isDirty('score')) {
            $this->autoAssignTagsByScore($lead);

            // Vérifier les séquences email basées sur le score
            app(\App\Services\EmailService::class)->checkAndSubscribeLeadToSequences($lead, \App\Enums\EmailSequenceTrigger::SCORE_THRESHOLD->value);
        }
    }

    /**
     * Auto-assign tags based on lead score
     */
    protected function autoAssignTagsByScore(Lead $lead): void
    {
        // Score ≥ 31 (HOT) → Tag "score_threshold_31"
        if ($lead->score >= 31 && $lead->score < 61) {
            $tag = Tag::where('auto_trigger', 'score_threshold_31')
                ->where('is_auto', true)
                ->first();
            
            if ($tag && !$lead->tags->contains($tag->id)) {
                $lead->tags()->attach($tag->id, [
                    'assigned_by' => null,
                    'assigned_at' => now(),
                ]);
            }
        }

        // Score ≥ 61 (ULTRA HOT) → Tag "score_threshold_61"
        if ($lead->score >= 61) {
            $tag = Tag::where('auto_trigger', 'score_threshold_61')
                ->where('is_auto', true)
                ->first();
            
            if ($tag && !$lead->tags->contains($tag->id)) {
                $lead->tags()->attach($tag->id, [
                    'assigned_by' => null,
                    'assigned_at' => now(),
                ]);
            }

            // Retirer le tag HOT si présent (car maintenant ULTRA HOT)
            $hotTag = Tag::where('auto_trigger', 'score_threshold_31')->first();
            if ($hotTag && $lead->tags->contains($hotTag->id)) {
                $lead->tags()->detach($hotTag->id);
            }
        }
    }

    /**
     * Handle the Lead "deleted" event.
     */
    public function deleted(Lead $lead): void
    {
        // Update funnel stats
        $lead->funnel?->updateStats();
    }

    /**
     * Handle the Lead "restored" event.
     */
    public function restored(Lead $lead): void
    {
        // Update funnel stats
        $lead->funnel?->updateStats();
    }
}
