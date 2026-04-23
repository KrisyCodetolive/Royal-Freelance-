<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Tag extends Model
{
    use HasFactory, BelongsToTenant, HasSlug;

    protected $fillable = [
        'tenant_id',
        'name',
        'slug',
        'color',
        'description',
        'is_auto',
        'auto_trigger',
        'sort_order',
    ];

    protected $casts = [
        'is_auto' => 'boolean',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    // Relationships
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function leads(): BelongsToMany
    {
        return $this->belongsToMany(Lead::class, 'lead_tag')
            ->withPivot('assigned_by')
            ->withTimestamps();
    }

    // Scopes
    public function scopeAuto($query)
    {
        return $query->where('is_auto', true);
    }

    public function scopeManual($query)
    {
        return $query->where('is_auto', false);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    // Helpers
    public function getLeadsCount(): int
    {
        return $this->leads()->count();
    }
}
