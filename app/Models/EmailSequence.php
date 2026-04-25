<?php

namespace App\Models;

use App\Enums\EmailSequenceStatus;
use App\Enums\EmailSequenceTrigger;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailSequence extends Model
{
    use HasFactory, BelongsToTenant, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'funnel_id',
        'name',
        'description',
        'trigger',
        'status',
        'trigger_conditions',
        'settings',
        'stats',
    ];

    protected $casts = [
        'trigger' => EmailSequenceTrigger::class,
        'status' => EmailSequenceStatus::class,
        'trigger_conditions' => 'array',
        'settings' => 'array',
        'stats' => 'array',
    ];

    // Relationships
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function funnel(): BelongsTo
    {
        return $this->belongsTo(Funnel::class);
    }

    public function emails(): HasMany
    {
        return $this->hasMany(EmailSequenceEmail::class)->orderBy('send_after_hours');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(EmailSequenceSubscription::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', EmailSequenceStatus::ACTIVE);
    }

    public function scopeByTrigger($query, EmailSequenceTrigger $trigger)
    {
        return $query->where('trigger', $trigger);
    }

    // Helpers
    public function isActive(): bool
    {
        return $this->status === EmailSequenceStatus::ACTIVE;
    }

    public function getTotalEmails(): int
    {
        return $this->emails()->count();
    }

    public function getTotalSubscribers(): int
    {
        return $this->subscriptions()->count();
    }

    public function getOpenRate(): float
    {
        $stats = $this->stats ?? [];
        $sent = $stats['sent'] ?? 0;
        $opened = $stats['opened'] ?? 0;
        
        return $sent > 0 ? round(($opened / $sent) * 100, 2) : 0;
    }

    public function getClickRate(): float
    {
        $stats = $this->stats ?? [];
        $sent = $stats['sent'] ?? 0;
        $clicked = $stats['clicked'] ?? 0;
        
        return $sent > 0 ? round(($clicked / $sent) * 100, 2) : 0;
    }

    public function subscribeLead(Lead $lead): EmailSequenceSubscription
    {
        $existing = $this->subscriptions()->where('lead_id', $lead->id)->first();

        if ($existing) {
            // Reset completed subscription so emails are re-sent
            $existing->update([
                'is_active' => true,
                'subscribed_at' => now(),
                'completed_at' => null,
                'unsubscribed_at' => null,
            ]);
            $existing->emailSends()->delete();
            return $existing->fresh();
        }

        return $this->subscriptions()->create([
            'lead_id' => $lead->id,
            'tenant_id' => $this->tenant_id,
            'subscribed_at' => now(),
        ]);
    }

    public function checkTriggerConditions(Lead $lead): bool
    {
        $conditions = $this->trigger_conditions ?? [];

        switch ($this->trigger) {
            case EmailSequenceTrigger::FORM_SUBMIT:
                return true; // Always trigger on form submit

            case EmailSequenceTrigger::SCORE_THRESHOLD:
                $threshold = $conditions['min_score'] ?? 10;
                return $lead->score >= $threshold;

            case EmailSequenceTrigger::PAGE_VIEW:
                $requiredPageId = $conditions['page_id'] ?? null;
                if (!$requiredPageId) return false;
                return $lead->events()->where('type', 'page_view')
                    ->where('page_id', $requiredPageId)->exists();

            case EmailSequenceTrigger::TAG_ASSIGNED:
                $requiredTag = $conditions['tag_id'] ?? null;
                if (!$requiredTag) return false;
                return $lead->tags()->where('tag_id', $requiredTag)->exists();

            case EmailSequenceTrigger::INACTIVITY:
                $days = $conditions['days'] ?? 7;
                return $lead->isInactive($days);

            case EmailSequenceTrigger::STATUS_CHANGED:
                $targetStatus = $conditions['status'] ?? null;
                if (!$targetStatus) return false;
                return $lead->status->value === $targetStatus;

            default:
                return false;
        }
    }

    public function incrementStats(string $metric, int $amount = 1): void
    {
        $stats = $this->stats ?? [];
        $stats[$metric] = ($stats[$metric] ?? 0) + $amount;
        $this->update(['stats' => $stats]);
    }
}
