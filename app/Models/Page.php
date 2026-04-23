<?php

namespace App\Models;

use App\Enums\PageType;
use App\Traits\HasSettings;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Page extends Model
{
    use HasFactory, HasSlug, HasSettings, SoftDeletes;

    protected $fillable = [
        'uuid',
        'funnel_id',
        'type',
        'title',
        'slug',
        'description',
        'settings',
        'branding',
        'meta_title',
        'meta_description',
        'sort_order',
        'is_active',
        'is_required',
        'views_count',
        'submissions_count',
    ];

    protected $casts = [
        'type' => PageType::class,
        'settings' => 'array',
        'branding' => 'array',
        'is_active' => 'boolean',
        'is_required' => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Page $page) {
            $page->uuid = $page->uuid ?? (string) \Illuminate\Support\Str::uuid();
        });
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    // Relationships
    public function funnel(): BelongsTo
    {
        return $this->belongsTo(Funnel::class);
    }

    public function blocks(): HasMany
    {
        return $this->hasMany(Block::class)->orderBy('sort_order');
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    // Helpers
    public function getPublicUrl(): ?string
    {
        if (!$this->funnel) {
            return null;
        }

        return url("/f/{$this->funnel->slug}/{$this->slug}");
    }

    public function isCapturePage(): bool
    {
        return $this->type === PageType::CAPTURE;
    }

    public function requiresForm(): bool
    {
        return $this->type->requiresForm();
    }

    public function getFormBlocks(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->blocks()->where('type', 'form')->get();
    }

    public function getNextPage(): ?self
    {
        return $this->funnel->pages()
            ->where('sort_order', '>', $this->sort_order)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->first();
    }

    public function getPreviousPage(): ?self
    {
        return $this->funnel->pages()
            ->where('sort_order', '<', $this->sort_order)
            ->where('is_active', true)
            ->orderByDesc('sort_order')
            ->first();
    }

    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    public function incrementSubmissions(): void
    {
        $this->increment('submissions_count');
    }

    /**
     * Get the 1-based position of this page among active pages in the funnel.
     * Page 1 = Froid, Page 2 = Tiède, Page 3 = Chaud, Page 4 = Ultra Chaud
     */
    public function getPositionInFunnel(): int
    {
        $activePages = $this->funnel->pages()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('id')
            ->toArray();

        $position = array_search($this->id, $activePages);

        return $position !== false ? $position + 1 : 1;
    }

    /**
     * Check if this is the last active page of the funnel.
     */
    public function isLastPage(): bool
    {
        $totalActivePages = $this->funnel->pages()
            ->where('is_active', true)
            ->count();

        return $this->getPositionInFunnel() === $totalActivePages;
    }

    public function getConversionRate(): float
    {
        if ($this->views_count === 0) {
            return 0;
        }

        return round(($this->submissions_count / $this->views_count) * 100, 2);
    }

    /**
     * Duplique la page ainsi que tous ses blocs associés.
     */
    public function duplicate(): self
    {
        $newPage = $this->replicate(['blocks_count', 'uuid']);
        $newPage->title = $this->title . ' (copie)';
        $newPage->sort_order = $this->funnel->pages()->max('sort_order') + 1;
        $newPage->views_count = 0;
        $newPage->submissions_count = 0;
        $newPage->save();

        // Dupliquer les blocs racines (la récursion est gérée dans Block::duplicate)
        foreach ($this->blocks()->root()->get() as $block) {
            $block->duplicate($newPage->id);
        }

        return $newPage;
    }
}
