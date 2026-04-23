<?php

namespace App\Traits;

use App\Enums\EventType;
use App\Enums\LeadStatus;
use App\Models\ScoringRule;
use Illuminate\Support\Facades\DB;

/**
 * Trait HasScore
 *
 * Provides scoring functionality for Lead models.
 * Calculates score based on events and configurable scoring rules.
 */
trait HasScore
{
    /**
     * Recalculate the lead's score based on all events and scoring rules.
     */
    public function recalculateScore(): void
    {
        // Get scoring rules for this tenant
        $rules = ScoringRule::where('tenant_id', $this->tenant_id)
            ->where('is_active', true)
            ->get()
            ->keyBy('event_type');

        // Calculate score from events
        $eventCounts = $this->events()
            ->select('type', DB::raw('COUNT(*) as count'))
            ->groupBy('type')
            ->pluck('count', 'type');

        $score = 0;
        foreach ($eventCounts as $eventType => $count) {
            if (isset($rules[$eventType])) {
                $score += $count * $rules[$eventType]->points;
            } else {
                // Use default points from EventType enum
                $enumType = EventType::tryFrom($eventType);
                if ($enumType) {
                    $score += $count * $enumType->defaultPoints();
                }
            }
        }

        $this->update(['score' => $score]);
        $this->updateStatusFromScore();
    }

    /**
     * Add points to the lead's score.
     */
    public function addPoints(int $points): void
    {
        $this->increment('score', $points);
        $this->updateStatusFromScore();
    }

    /**
     * Update lead status based on current score.
     */
    public function updateStatusFromScore(): void
    {
        // Don't change status if already a client or member
        if (in_array($this->status, [LeadStatus::CLIENT, LeadStatus::MEMBER])) {
            return;
        }

        $thresholds = $this->tenant?->settings['scoring_thresholds'] ?? [];
        $newStatus = LeadStatus::fromScore($this->score, $thresholds);

        if ($this->status !== $newStatus) {
            $oldStatus = $this->status;
            $this->update(['status' => $newStatus]);

            // Dispatch event for status change (can trigger alerts)
            event(new \App\Events\LeadStatusChanged($this, $oldStatus, $newStatus));
        }
    }

    /**
     * Get the scoring thresholds for this lead's tenant.
     */
    public function getScoringThresholds(): array
    {
        return array_merge([
            'cold' => 0,
            'warm' => 11,
            'hot' => 31,
            'ultra_hot' => 61,
        ], $this->tenant?->settings['scoring_thresholds'] ?? []);
    }

    /**
     * Get progress to next status level.
     */
    public function getProgressToNextStatus(): array
    {
        $thresholds = $this->getScoringThresholds();
        $currentScore = $this->score;

        $levels = [
            'cold' => $thresholds['cold'],
            'warm' => $thresholds['warm'],
            'hot' => $thresholds['hot'],
            'ultra_hot' => $thresholds['ultra_hot'],
        ];

        $currentLevel = $this->status->value;
        $nextLevel = null;
        $nextThreshold = null;

        foreach ($levels as $level => $threshold) {
            if ($threshold > $currentScore) {
                $nextLevel = $level;
                $nextThreshold = $threshold;
                break;
            }
        }

        if (!$nextLevel) {
            return [
                'current' => $currentLevel,
                'next' => null,
                'progress' => 100,
                'points_needed' => 0,
            ];
        }

        $previousThreshold = $levels[$currentLevel] ?? 0;
        $range = $nextThreshold - $previousThreshold;
        $progress = $range > 0 ? (($currentScore - $previousThreshold) / $range) * 100 : 0;

        return [
            'current' => $currentLevel,
            'next' => $nextLevel,
            'progress' => min(100, max(0, $progress)),
            'points_needed' => max(0, $nextThreshold - $currentScore),
        ];
    }
}
