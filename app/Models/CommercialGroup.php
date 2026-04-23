<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class CommercialGroup extends Model
{
    use HasFactory, BelongsToTenant, HasSlug;

    protected $fillable = [
        'tenant_id',
        'name',
        'slug',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
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

    /**
     * Users (commercials) in this group
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'commercial_group_user')
            ->withTimestamps();
    }

    /**
     * Alias for users - commercials in this group
     */
    public function commercials(): BelongsToMany
    {
        return $this->users()->role('commercial');
    }

    /**
     * Funnels available to this group
     */
    public function funnels(): BelongsToMany
    {
        return $this->belongsToMany(Funnel::class, 'commercial_group_funnel')
            ->withPivot('can_customize')
            ->withTimestamps();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Helpers
    public function addUser(User $user): void
    {
        if (!$this->users()->where('user_id', $user->id)->exists()) {
            $this->users()->attach($user->id);
        }
    }

    public function removeUser(User $user): void
    {
        $this->users()->detach($user->id);
    }

    public function addFunnel(Funnel $funnel, bool $canCustomize = false): void
    {
        $this->funnels()->syncWithoutDetaching([
            $funnel->id => ['can_customize' => $canCustomize],
        ]);
    }

    public function removeFunnel(Funnel $funnel): void
    {
        $this->funnels()->detach($funnel->id);
    }

    public function getUsersCount(): int
    {
        return $this->users()->count();
    }

    public function getFunnelsCount(): int
    {
        return $this->funnels()->count();
    }

    /**
     * Get total leads brought by all commercials in this group
     */
    public function getTotalLeads(): int
    {
        return Lead::whereIn('brought_by', $this->users()->pluck('users.id'))->count();
    }

    /**
     * Get total conversions from all commercials in this group
     */
    public function getTotalConversions(): int
    {
        return Lead::whereIn('brought_by', $this->users()->pluck('users.id'))
            ->whereNotNull('converted_at')
            ->count();
    }
}
