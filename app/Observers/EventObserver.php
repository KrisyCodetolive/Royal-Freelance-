<?php

namespace App\Observers;

use App\Models\Event;
use App\Models\Tag;
use App\Enums\EventType;
use App\Services\AlertService;
use App\Services\ScoringService;

class EventObserver
{
    protected ScoringService $scoringService;
    protected AlertService $alertService;

    public function __construct(ScoringService $scoringService, AlertService $alertService)
    {
        $this->scoringService = $scoringService;
        $this->alertService = $alertService;
    }

    /**
     * Handle the Event "created" event.
     */
    public function created(Event $event): void
    {
        // Recalculate lead score
        $lead = $event->lead;
        if ($lead) {
            $this->scoringService->recalculateScore($lead);

            // Check for event-based alerts
            $this->alertService->checkEventAlerts($lead, $event->type);

            // Auto-tagging basé sur les événements
            $this->autoAssignTags($lead, $event);
        }
    }

    /**
     * Auto-assign tags based on event type
     */
    protected function autoAssignTags($lead, Event $event): void
    {
        $tagToAssign = null;

        // Vidéo complétée → Tag "video_complete"
        if ($event->type === EventType::VIDEO_100) {
            $tagToAssign = Tag::where('auto_trigger', 'video_complete')
                ->where('is_auto', true)
                ->first();
        }

        // Clic WhatsApp → Tag "whatsapp_click"
        if ($event->type === EventType::WHATSAPP_CLICK) {
            $tagToAssign = Tag::where('auto_trigger', 'whatsapp_click')
                ->where('is_auto', true)
                ->first();
        }

        // Soumission formulaire → Tag "form_submit"
        if ($event->type === EventType::FORM_SUBMIT) {
            $tagToAssign = Tag::where('auto_trigger', 'form_submit')
                ->where('is_auto', true)
                ->first();
        }

        // Assigner le tag si trouvé et pas déjà présent
        if ($tagToAssign && !$lead->tags->contains($tagToAssign->id)) {
            $lead->tags()->attach($tagToAssign->id, [
                'assigned_by' => null, // Auto-assigné
                'assigned_at' => now(),
            ]);
        }
    }
}
