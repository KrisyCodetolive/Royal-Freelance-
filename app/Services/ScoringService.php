<?php

namespace App\Services;

use App\Enums\EventType;
use App\Enums\LeadStatus;
use App\Models\Lead;
use App\Models\ScoringRule;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

class ScoringService
{
    /**
     * Calculate score for a lead based on all events
     */
    public function calculateScore(Lead $lead): int
    {
        $rules = $this->getRulesForTenant($lead->tenant_id);

        $eventCounts = $lead->events()
            ->select('type', DB::raw('COUNT(*) as count'))
            ->groupBy('type')
            ->pluck('count', 'type');

        $score = 0;
        foreach ($eventCounts as $eventType => $count) {
            if (isset($rules[$eventType])) {
                $score += $count * $rules[$eventType];
            } else {
                // Fallback to default points
                $enumType = EventType::tryFrom($eventType);
                if ($enumType) {
                    $score += $count * $enumType->defaultPoints();
                }
            }
        }

        return $score;
    }

    /**
     * Recalculate and update lead score
     */
    public function recalculateScore(Lead $lead): Lead
    {
        $score = $this->calculateScore($lead);
        $lead->update(['score' => $score]);

        $this->updateStatusFromScore($lead);

        return $lead->fresh();
    }

    /**
     * Add points to a lead
     */
    public function addPoints(Lead $lead, int $points): Lead
    {
        $lead->increment('score', $points);
        $this->updateStatusFromScore($lead);

        return $lead->fresh();
    }

    /**
     * Update lead status based on current score
     */
    public function updateStatusFromScore(Lead $lead): Lead
    {
        // Don't change status if already client or member
        if (in_array($lead->status, [LeadStatus::CLIENT, LeadStatus::MEMBER])) {
            return $lead;
        }

        $thresholds = $this->getThresholdsForTenant($lead->tenant_id);
        $newStatus = LeadStatus::fromScore($lead->score, $thresholds);

        if ($lead->status !== $newStatus) {
            $oldStatus = $lead->status;
            $lead->update(['status' => $newStatus]);

            // Dispatch event for observers/listeners
            event(new \App\Events\LeadStatusChanged($lead, $oldStatus, $newStatus));
        }

        return $lead;
    }

    /**
     * Get scoring rules for a tenant (cached)
     */
    public function getRulesForTenant(int $tenantId): array
    {
        return cache()->remember(
            "scoring_rules_{$tenantId}",
            now()->addHours(1),
            fn() => ScoringRule::where('tenant_id', $tenantId)
                ->where('is_active', true)
                ->pluck('points', 'event_type')
                ->toArray()
        );
    }

    /**
     * Get scoring thresholds for a tenant
     */
    public function getThresholdsForTenant(int $tenantId): array
    {
        $tenant = Tenant::find($tenantId);

        return array_merge([
            'cold' => 0,
            'warm' => 11,
            'hot' => 31,
            'ultra_hot' => 61,
        ], $tenant?->settings['scoring_thresholds'] ?? []);
    }

    /**
     * Update scoring rules for a tenant
     */
    public function updateRules(Tenant $tenant, array $rules): void
    {
        foreach ($rules as $eventType => $points) {
            ScoringRule::updateOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'event_type' => $eventType,
                ],
                [
                    'name' => EventType::tryFrom($eventType)?->label() ?? $eventType,
                    'points' => $points,
                    'is_active' => true,
                ]
            );
        }

        // Clear cache
        cache()->forget("scoring_rules_{$tenant->id}");
    }

    /**
     * Get progress to next status level
     */
    public function getProgressToNextStatus(Lead $lead): array
    {
        $thresholds = $this->getThresholdsForTenant($lead->tenant_id);
        $currentScore = $lead->score;

        $levels = [
            LeadStatus::COLD->value => $thresholds['cold'],
            LeadStatus::WARM->value => $thresholds['warm'],
            LeadStatus::HOT->value => $thresholds['hot'],
            LeadStatus::ULTRA_HOT->value => $thresholds['ultra_hot'],
        ];

        $currentLevel = $lead->status->value;
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
            'progress' => min(100, max(0, round($progress, 2))),
            'points_needed' => max(0, $nextThreshold - $currentScore),
        ];
    }
}
