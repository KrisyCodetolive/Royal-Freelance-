<?php

namespace App\Models;

use App\Enums\EventType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'page_id',
        'type',
        'data',
        'ip_address',
        'user_agent',
        'device_type',
        'browser',
        'os',
        'time_spent_seconds',
        'scroll_depth_percentage',
    ];

    protected $casts = [
        'type' => EventType::class,
        'data' => 'array',
    ];

    // Relationships
    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    // Scopes
    public function scopeOfType($query, EventType $type)
    {
        return $query->where('type', $type);
    }

    public function scopeVideoEvents($query)
    {
        return $query->whereIn('type', [
            EventType::VIDEO_PLAY,
            EventType::VIDEO_25,
            EventType::VIDEO_50,
            EventType::VIDEO_75,
            EventType::VIDEO_100,
        ]);
    }

    public function scopeConversionEvents($query)
    {
        return $query->whereIn('type', [
            EventType::WHATSAPP_CLICK,
            EventType::PAYMENT_CLICK,
            EventType::CONVERSION,
        ]);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month);
    }

    // Helpers
    public function isVideoEvent(): bool
    {
        return $this->type->isVideoEvent();
    }

    public function isConversionEvent(): bool
    {
        return $this->type->isConversionEvent();
    }

    public function getData(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }
}
