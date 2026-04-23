<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailSequenceEmailSend extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'subscription_id',
        'email_sequence_email_id',
        'lead_id',
        'subject',
        'content',
        'sent_at',
        'opened_at',
        'clicked_at',
        'bounced_at',
        'unsubscribed_at',
        'tracking_data',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'opened_at' => 'datetime',
        'clicked_at' => 'datetime',
        'bounced_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
        'tracking_data' => 'array',
    ];

    // Relationships
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(EmailSequenceSubscription::class);
    }

    public function emailSequenceEmail(): BelongsTo
    {
        return $this->belongsTo(EmailSequenceEmail::class);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    // Scopes
    public function scopeSent($query)
    {
        return $query->whereNotNull('sent_at');
    }

    public function scopeOpened($query)
    {
        return $query->whereNotNull('opened_at');
    }

    public function scopeClicked($query)
    {
        return $query->whereNotNull('clicked_at');
    }

    public function scopeBounced($query)
    {
        return $query->whereNotNull('bounced_at');
    }

    // Helpers
    public function isSent(): bool
    {
        return $this->sent_at !== null;
    }

    public function isOpened(): bool
    {
        return $this->opened_at !== null;
    }

    public function isClicked(): bool
    {
        return $this->clicked_at !== null;
    }

    public function isBounced(): bool
    {
        return $this->bounced_at !== null;
    }

    public function markAsSent(): void
    {
        $this->update(['sent_at' => now()]);
        
        // Update stats
        $this->emailSequenceEmail->incrementStats('sent');
        $this->emailSequenceEmail->emailSequence->incrementStats('sent');
    }

    public function markAsOpened(): void
    {
        if (!$this->isOpened()) {
            $this->update(['opened_at' => now()]);
            
            // Update stats
            $this->emailSequenceEmail->incrementStats('opened');
            $this->emailSequenceEmail->emailSequence->incrementStats('opened');
        }
    }

    public function markAsClicked(): void
    {
        if (!$this->isClicked()) {
            $this->update(['clicked_at' => now()]);
            
            // Update stats
            $this->emailSequenceEmail->incrementStats('clicked');
            $this->emailSequenceEmail->emailSequence->incrementStats('clicked');
            
            // Also mark as opened if not already
            $this->markAsOpened();
        }
    }

    public function markAsBounced(): void
    {
        $this->update(['bounced_at' => now()]);
        
        // Update stats
        $this->emailSequenceEmail->incrementStats('bounced');
        $this->emailSequenceEmail->emailSequence->incrementStats('bounced');
    }

    public function markAsUnsubscribed(): void
    {
        $this->update(['unsubscribed_at' => now()]);
        
        // Also unsubscribe from the sequence
        $this->subscription->unsubscribe();
        
        // Update stats
        $this->emailSequenceEmail->incrementStats('unsubscribed');
        $this->emailSequenceEmail->emailSequence->incrementStats('unsubscribed');
    }

    public function getTrackingPixelUrl(): string
    {
        return route('email.tracking.open', [
            'send' => $this->id,
            'token' => md5($this->id . config('app.key'))
        ]);
    }

    public function getUnsubscribeUrl(): string
    {
        return route('email.unsubscribe', [
            'send' => $this->id,
            'token' => md5($this->id . config('app.key'))
        ]);
    }

    public function getFinalContent(): string
    {
        return view('emails.layouts.sequence', [
            'subject' => $this->subject,
            'content' => $this->content,
            'tenant_name' => $this->tenant?->name ?? 'notre plateforme',
            'unsubscribeUrl' => $this->getUnsubscribeUrl(),
            'pixelUrl' => $this->getTrackingPixelUrl(),
        ])->render();
    }
}
