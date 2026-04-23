<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmailSequenceSubscription extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'email_sequence_id',
        'lead_id',
        'subscribed_at',
        'unsubscribed_at',
        'completed_at',
        'is_active',
    ];

    protected $casts = [
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
        'completed_at' => 'datetime',
        'is_active' => 'boolean',
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

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function emailSends(): HasMany
    {
        return $this->hasMany(EmailSequenceEmailSend::class, 'subscription_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->whereNull('unsubscribed_at');
    }

    public function scopeCompleted($query)
    {
        return $query->whereNotNull('completed_at');
    }

    public function scopeUnsubscribed($query)
    {
        return $query->whereNotNull('unsubscribed_at');
    }

    // Helpers
    public function isActive(): bool
    {
        return $this->is_active && 
               $this->unsubscribed_at === null &&
               $this->completed_at === null;
    }

    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }

    public function isUnsubscribed(): bool
    {
        return $this->unsubscribed_at !== null;
    }

    public function unsubscribe(): void
    {
        $this->update([
            'is_active' => false,
            'unsubscribed_at' => now(),
        ]);
    }

    public function markCompleted(): void
    {
        $this->update([
            'completed_at' => now(),
        ]);
    }

    public function getProgress(): array
    {
        $totalEmails = $this->emailSequence->emails()->active()->count();
        $sentEmails = $this->emailSends()->whereNotNull('sent_at')->count();
        
        $percentage = $totalEmails > 0 ? round(($sentEmails / $totalEmails) * 100) : 0;
        
        return [
            'total' => $totalEmails,
            'sent' => $sentEmails,
            'remaining' => $totalEmails - $sentEmails,
            'percentage' => $percentage,
        ];
    }

    public function getNextEmailToSend(): ?EmailSequenceEmail
    {
        return $this->emailSequence
            ->emails()
            ->active()
            ->whereDoesntHave('sends', function ($query) {
                $query->where('subscription_id', $this->id);
            })
            ->where(function ($query) {
                $sendTime = $this->subscribed_at->addHours($query->getModel()->send_after_hours ?? 0);
                return now() >= $sendTime;
            })
            ->orderBy('send_after_hours')
            ->first();
    }

    public function getDaysInSequence(): int
    {
        return $this->subscribed_at->diffInDays(now());
    }

    public function getEngagementStats(): array
    {
        $sends = $this->emailSends()->whereNotNull('sent_at');
        
        $totalSent = $sends->count();
        $totalOpened = $sends->whereNotNull('opened_at')->count();
        $totalClicked = $sends->whereNotNull('clicked_at')->count();
        
        return [
            'sent' => $totalSent,
            'opened' => $totalOpened,
            'clicked' => $totalClicked,
            'open_rate' => $totalSent > 0 ? round(($totalOpened / $totalSent) * 100, 2) : 0,
            'click_rate' => $totalSent > 0 ? round(($totalClicked / $totalSent) * 100, 2) : 0,
        ];
    }
}
