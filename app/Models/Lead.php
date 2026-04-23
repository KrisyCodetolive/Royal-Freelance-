<?php

namespace App\Models;

use App\Enums\LeadStatus;
use App\Traits\BelongsToTenant;
use App\Traits\HasScore;
use App\Traits\Trackable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasFactory, BelongsToTenant, HasScore, Trackable, SoftDeletes;

    protected $fillable = [
        'uuid',
        'tenant_id',
        'funnel_id',
        'assigned_to',
        'brought_by',
        'email',
        'phone',
        'first_name',
        'last_name',
        'source',
        'medium',
        'campaign',
        'referrer',
        'score',
        'status',
        'custom_fields',
        'form_data',
        'ip_address',
        'user_agent',
        'device_type',
        'browser',
        'browser_version',
        'os',
        'os_version',
        'country',
        'city',
        'region',
        'timezone',
        'latitude',
        'longitude',
        'screen_resolution',
        'language',
        'last_activity_at',
        'converted_at',
    ];

    protected $casts = [
        'status' => LeadStatus::class,
        'custom_fields' => 'array',
        'form_data' => 'array',
        'last_activity_at' => 'datetime',
        'converted_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Lead $lead) {
            $lead->uuid = $lead->uuid ?? (string) \Illuminate\Support\Str::uuid();
        });
    }

    // Relationships
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function funnel(): BelongsTo
    {
        return $this->belongsTo(Funnel::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * The commercial who brought this lead (via their subdomain)
     */
    public function broughtBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'brought_by');
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(Alert::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'lead_tag')
            ->withPivot('assigned_by')
            ->withTimestamps();
    }

    // Scopes
    public function scopeByStatus($query, LeadStatus $status)
    {
        return $query->where('status', $status);
    }

    public function scopeCold($query)
    {
        return $query->where('status', LeadStatus::COLD);
    }

    public function scopeWarm($query)
    {
        return $query->where('status', LeadStatus::WARM);
    }

    public function scopeHot($query)
    {
        return $query->whereIn('status', [LeadStatus::HOT, LeadStatus::ULTRA_HOT]);
    }

    public function scopeConverted($query)
    {
        return $query->whereNotNull('converted_at');
    }

    public function scopeInactive($query, int $days = 7)
    {
        return $query->where('last_activity_at', '<', now()->subDays($days));
    }

    public function scopeWithTag($query, string|int $tag)
    {
        return $query->whereHas('tags', function ($q) use ($tag) {
            if (is_int($tag)) {
                $q->where('tags.id', $tag);
            } else {
                $q->where('tags.slug', $tag);
            }
        });
    }

    public function scopeBroughtBy($query, User|int $user)
    {
        $userId = $user instanceof User ? $user->id : $user;
        return $query->where('brought_by', $userId);
    }

    // Helpers
    public function getFullName(): string
    {
        return trim("{$this->first_name} {$this->last_name}") ?: 'Sans nom';
    }

    public function getDisplayName(): string
    {
        if ($name = $this->getFullName()) {
            return $name !== 'Sans nom' ? $name : ($this->email ?? $this->phone ?? 'Lead #' . $this->id);
        }

        return $this->email ?? $this->phone ?? 'Lead #' . $this->id;
    }

    public function isConverted(): bool
    {
        return $this->converted_at !== null;
    }

    public function isHot(): bool
    {
        return in_array($this->status, [LeadStatus::HOT, LeadStatus::ULTRA_HOT]);
    }

    public function isInactive(int $days = 7): bool
    {
        if (!$this->last_activity_at) {
            return $this->created_at->diffInDays(now()) >= $days;
        }

        return $this->last_activity_at->diffInDays(now()) >= $days;
    }

    public function addTag(Tag|int $tag, ?int $assignedBy = null): void
    {
        $tagId = $tag instanceof Tag ? $tag->id : $tag;

        if (!$this->tags()->where('tag_id', $tagId)->exists()) {
            $this->tags()->attach($tagId, ['assigned_by' => $assignedBy ?? auth()->id()]);
        }
    }

    public function removeTag(Tag|int $tag): void
    {
        $tagId = $tag instanceof Tag ? $tag->id : $tag;
        $this->tags()->detach($tagId);
    }

    public function addNote(string $content, ?int $userId = null, bool $isPrivate = false): Note
    {
        return $this->notes()->create([
            'user_id' => $userId ?? auth()->id(),
            'content' => $content,
            'is_private' => $isPrivate,
        ]);
    }

    public function markAsConverted(array $conversionData = []): void
    {
        $this->update([
            'status' => LeadStatus::CLIENT,
            'converted_at' => now(),
        ]);

        $this->trackConversion($conversionData);
    }
}
