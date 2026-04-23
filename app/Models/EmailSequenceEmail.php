<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmailSequenceEmail extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'email_sequence_id',
        'subject',
        'content',
        'send_after_hours',
        'is_active',
        'settings',
        'stats',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array',
        'stats' => 'array',
    ];

    // Relationships
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function emailSequence(): BelongsTo
    {
        return $this->belongsTo(EmailSequence::class);
    }

    public function sends(): HasMany
    {
        return $this->hasMany(EmailSequenceEmailSend::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrderedByDelay($query)
    {
        return $query->orderBy('send_after_hours');
    }

    // Helpers
    public function getDelayFormatted(): string
    {
        if ($this->send_after_hours === 0) {
            return 'Immédiat';
        } elseif ($this->send_after_hours < 24) {
            return $this->send_after_hours . 'h';
        } else {
            $days = round($this->send_after_hours / 24, 1);
            return $days . ' jour' . ($days > 1 ? 's' : '');
        }
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

    public function shouldSendTo(EmailSequenceSubscription $subscription): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $sendTime = $subscription->subscribed_at->addHours($this->send_after_hours);
        
        if (now() < $sendTime) {
            return false;
        }

        // Check if already sent
        return !$this->sends()
            ->where('subscription_id', $subscription->id)
            ->exists();
    }

    public function incrementStats(string $metric, int $amount = 1): void
    {
        $stats = $this->stats ?? [];
        $stats[$metric] = ($stats[$metric] ?? 0) + $amount;
        $this->update(['stats' => $stats]);
    }

    public function getPersonalizedContent(Lead $lead): string
    {
        $content = $this->content;

        // Replace placeholders
        $replacements = [
            '{first_name}' => $lead->first_name ?? 'cher prospect',
            '{last_name}' => $lead->last_name ?? '',
            '{full_name}' => $lead->getFullName(),
            '{email}' => $lead->email ?? '',
            '{score}' => $lead->score,
            '{funnel_name}' => $lead->funnel?->name ?? '',
            '{tenant_name}' => $lead->tenant?->name ?? '',
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $content);
    }

    public function getPersonalizedSubject(Lead $lead): string
    {
        $subject = $this->subject;

        // Replace placeholders
        $replacements = [
            '{first_name}' => $lead->first_name ?? '',
            '{last_name}' => $lead->last_name ?? '',
            '{funnel_name}' => $lead->funnel?->name ?? '',
        ];

        return trim(str_replace(array_keys($replacements), array_values($replacements), $subject));
    }
}
