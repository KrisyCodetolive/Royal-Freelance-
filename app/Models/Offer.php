<?php

namespace App\Models;

use App\Enums\OfferType;
use App\Traits\BelongsToTenant;
use App\Traits\HasSettings;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Offer extends Model implements HasMedia
{
    use HasFactory, BelongsToTenant, HasSlug, HasSettings, SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'uuid',
        'tenant_id',
        'name',
        'slug',
        'type',
        'description',
        'short_description',
        'price',
        'currency',
        'image',
        'features',
        'settings',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'type' => OfferType::class,
        'price' => 'decimal:2',
        'features' => 'array',
        'settings' => 'array',
        'is_active' => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Offer $offer) {
            $offer->uuid = $offer->uuid ?? (string) \Illuminate\Support\Str::uuid();
        });
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')
            ->singleFile();
    }

    // Relationships
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function funnels(): HasMany
    {
        return $this->hasMany(Funnel::class);
    }

    // Helpers
    public function getFormattedPrice(): string
    {
        return number_format($this->price, 0, ',', ' ') . ' ' . $this->currency;
    }

    public function getImageUrl(): ?string
    {
        return $this->getFirstMediaUrl('image') ?: $this->image;
    }
}
