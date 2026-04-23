<?php

namespace App\Traits;

use App\Enums\EventType;
use App\Models\Event;

/**
 * Trait Trackable
 *
 * Provides event tracking functionality for Lead models.
 */
trait Trackable
{
    /**
     * Track an event for this lead.
     */
    public function track(EventType $type, array $data = []): Event
    {
        $event = $this->events()->create([
            'type' => $type,
            'data' => $data,
            'page_id' => $data['page_id'] ?? null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        // Update last activity timestamp
        $this->update(['last_activity_at' => now()]);

        // Recalculate score after tracking
        $this->recalculateScore();

        return $event;
    }

    /**
     * Track a page view event.
     */
    public function trackPageView(int $pageId): Event
    {
        return $this->track(EventType::PAGE_VIEW, ['page_id' => $pageId]);
    }

    /**
     * Track a form submission event.
     */
    public function trackFormSubmit(int $pageId, array $formData = []): Event
    {
        return $this->track(EventType::FORM_SUBMIT, [
            'page_id' => $pageId,
            'form_data' => $formData,
        ]);
    }

    /**
     * Track a video progress event.
     */
    public function trackVideoProgress(int $pageId, int $percentage, ?string $videoId = null): Event
    {
        $type = match (true) {
            $percentage >= 100 => EventType::VIDEO_100,
            $percentage >= 75 => EventType::VIDEO_75,
            $percentage >= 50 => EventType::VIDEO_50,
            $percentage >= 25 => EventType::VIDEO_25,
            default => EventType::VIDEO_PLAY,
        };

        return $this->track($type, [
            'page_id' => $pageId,
            'percentage' => $percentage,
            'video_id' => $videoId,
        ]);
    }

    /**
     * Track a CTA click event.
     */
    public function trackCtaClick(int $pageId, ?string $buttonId = null): Event
    {
        return $this->track(EventType::CTA_CLICK, [
            'page_id' => $pageId,
            'button_id' => $buttonId,
        ]);
    }

    /**
     * Track a WhatsApp click event.
     */
    public function trackWhatsAppClick(int $pageId): Event
    {
        return $this->track(EventType::WHATSAPP_CLICK, ['page_id' => $pageId]);
    }

    /**
     * Track a payment click event.
     */
    public function trackPaymentClick(int $pageId): Event
    {
        return $this->track(EventType::PAYMENT_CLICK, ['page_id' => $pageId]);
    }

    /**
     * Track a conversion event.
     */
    public function trackConversion(array $data = []): Event
    {
        $this->update(['converted_at' => now()]);

        return $this->track(EventType::CONVERSION, $data);
    }

    /**
     * Get the timeline of events for this lead.
     */
    public function getTimeline(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->events()
            ->with('page:id,title')
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Get video completion stats.
     */
    public function getVideoStats(): array
    {
        $videoEvents = $this->events()
            ->whereIn('type', [
                EventType::VIDEO_PLAY,
                EventType::VIDEO_25,
                EventType::VIDEO_50,
                EventType::VIDEO_75,
                EventType::VIDEO_100,
            ])
            ->get()
            ->groupBy(fn($e) => $e->data['video_id'] ?? $e->data['page_id'] ?? 'unknown');

        $stats = [];
        foreach ($videoEvents as $videoId => $events) {
            $maxProgress = 0;
            foreach ($events as $event) {
                $progress = match ($event->type) {
                    EventType::VIDEO_100 => 100,
                    EventType::VIDEO_75 => 75,
                    EventType::VIDEO_50 => 50,
                    EventType::VIDEO_25 => 25,
                    default => 0,
                };
                $maxProgress = max($maxProgress, $progress);
            }
            $stats[$videoId] = $maxProgress;
        }

        return $stats;
    }
}
