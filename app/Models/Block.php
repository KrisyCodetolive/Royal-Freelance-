<?php

namespace App\Models;

use App\Enums\BlockType;
use App\Traits\HasSettings;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Block extends Model
{
    use HasFactory, HasSettings;

    protected $fillable = [
        'uuid',
        'page_id',
        'parent_id',
        'depth',
        'type',
        'content',
        'settings',
        'styles',
        'mobile_styles',
        'columns_layout',
        'column_index',
        'animation',
        'animation_delay',
        'sort_order',
        'is_active',
        'is_master_block',
        'master_block_name',
        'master_block_id',
        'show_on_mobile',
        'show_on_desktop',
        'display_conditions',
    ];

    protected $casts = [
        'type' => BlockType::class,
        'content' => 'array',
        'settings' => 'array',
        'styles' => 'array',
        'mobile_styles' => 'array',
        'display_conditions' => 'array',
        'is_active' => 'boolean',
        'is_master_block' => 'boolean',
        'show_on_mobile' => 'boolean',
        'show_on_desktop' => 'boolean',
        'depth' => 'integer',
        'column_index' => 'integer',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Block $block) {
            $block->uuid = $block->uuid ?? (string) \Illuminate\Support\Str::uuid();

            // Calculer automatiquement la profondeur
            if ($block->parent_id) {
                $parent = self::find($block->parent_id);
                $block->depth = $parent ? $parent->depth + 1 : 0;
            }
        });

        // Quand on supprime un bloc parent, supprimer les enfants
        static::deleting(function (Block $block) {
            $block->children()->delete();
        });
    }

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    /**
     * Bloc parent (pour hiérarchie Section > Row > Element)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Blocs enfants
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->ordered();
    }

    /**
     * Tous les descendants (récursif)
     */
    public function descendants(): HasMany
    {
        return $this->children()->with('descendants');
    }

    /**
     * Master block d'origine (si ce bloc est une instance)
     */
    public function masterBlock(): BelongsTo
    {
        return $this->belongsTo(self::class, 'master_block_id');
    }

    /**
     * Instances de ce master block
     */
    public function instances(): HasMany
    {
        return $this->hasMany(self::class, 'master_block_id');
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public function scopeForMobile($query)
    {
        return $query->where('show_on_mobile', true);
    }

    public function scopeForDesktop($query)
    {
        return $query->where('show_on_desktop', true);
    }

    /**
     * Blocs racines (sans parent)
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Uniquement les master blocks
     */
    public function scopeMasterBlocks($query)
    {
        return $query->where('is_master_block', true);
    }

    /**
     * Blocs d'un certain type
     */
    public function scopeOfType($query, BlockType $type)
    {
        return $query->where('type', $type);
    }

    // =========================================================================
    // CONTENT HELPERS
    // =========================================================================

    public function getContent(string $key, mixed $default = null): mixed
    {
        return $this->content[$key] ?? $default;
    }

    public function setContent(string $key, mixed $value): self
    {
        $content = $this->content ?? [];
        $content[$key] = $value;
        $this->content = $content;
        $this->save();

        return $this;
    }

    public function mergeContent(array $data): self
    {
        $this->content = array_merge($this->content ?? [], $data);
        $this->save();
        return $this;
    }

    // =========================================================================
    // STYLE HELPERS
    // =========================================================================

    public function getStyle(string $key, mixed $default = null): mixed
    {
        return $this->styles[$key] ?? $default;
    }

    public function getMobileStyle(string $key, mixed $default = null): mixed
    {
        return $this->mobile_styles[$key] ?? $this->getStyle($key, $default);
    }

    /**
     * Retourne les styles CSS compilés
     */
    public function getCompiledStyles(): string
    {
        if (empty($this->styles)) {
            return '';
        }

        $css = [];
        foreach ($this->styles as $property => $value) {
            // Convertir camelCase en kebab-case
            $cssProperty = strtolower(preg_replace('/([A-Z])/', '-$1', $property));
            $css[] = "{$cssProperty}: {$value}";
        }

        return implode('; ', $css);
    }

    // =========================================================================
    // BLOCK TYPE HELPERS
    // =========================================================================

    public function isTitle(): bool
    {
        return $this->type === BlockType::TITLE;
    }
    public function isText(): bool
    {
        return $this->type === BlockType::TEXT;
    }
    public function isImage(): bool
    {
        return $this->type === BlockType::IMAGE;
    }
    public function isVideo(): bool
    {
        return $this->type === BlockType::VIDEO;
    }
    public function isAudio(): bool
    {
        return $this->type === BlockType::AUDIO;
    }
    public function isButton(): bool
    {
        return $this->type === BlockType::BUTTON;
    }
    public function isForm(): bool
    {
        return $this->type === BlockType::FORM;
    }
    public function isPopup(): bool
    {
        return $this->type === BlockType::POPUP;
    }
    public function isOrderBump(): bool
    {
        return $this->type === BlockType::ORDER_BUMP;
    }
    public function isSection(): bool
    {
        return $this->type === BlockType::SECTION;
    }
    public function isColumns(): bool
    {
        return $this->type === BlockType::COLUMNS;
    }
    public function isPricing(): bool
    {
        return $this->type === BlockType::PRICING;
    }
    public function isFaq(): bool
    {
        return $this->type === BlockType::FAQ;
    }
    public function isCountdown(): bool
    {
        return $this->type === BlockType::COUNTDOWN;
    }
    public function isHero(): bool
    {
        return $this->type === BlockType::HERO;
    }
    public function isTestimonial(): bool
    {
        return $this->type === BlockType::TESTIMONIAL;
    }
    public function isFeatures(): bool
    {
        return $this->type === BlockType::FEATURES;
    }
    public function isEmbed(): bool
    {
        return $this->type === BlockType::EMBED;
    }

    /**
     * Vérifie si le bloc peut avoir des enfants
     */
    public function canHaveChildren(): bool
    {
        return $this->type->canHaveChildren();
    }

    /**
     * Vérifie si c'est un bloc de mise en page
     */
    public function isLayoutBlock(): bool
    {
        return $this->type->isLayoutBlock();
    }

    // =========================================================================
    // HIERARCHY HELPERS
    // =========================================================================

    /**
     * Retourne tous les ancêtres
     */
    public function getAncestors(): Collection
    {
        $ancestors = collect();
        $current = $this->parent;

        while ($current) {
            $ancestors->push($current);
            $current = $current->parent;
        }

        return $ancestors->reverse();
    }

    /**
     * Retourne le chemin complet (breadcrumb)
     */
    public function getPath(): string
    {
        $path = $this->getAncestors()->pluck('type.value')->push($this->type->value);
        return $path->implode(' > ');
    }

    /**
     * Vérifie si ce bloc est un ancêtre d'un autre
     */
    public function isAncestorOf(Block $block): bool
    {
        return $block->getAncestors()->contains('id', $this->id);
    }

    /**
     * Ajoute un bloc enfant
     */
    public function addChild(array $data): Block
    {
        return self::create(array_merge($data, [
            'page_id' => $this->page_id,
            'parent_id' => $this->id,
            'depth' => $this->depth + 1,
            'sort_order' => $this->children()->count() + 1,
        ]));
    }

    // =========================================================================
    // MASTER BLOCK HELPERS
    // =========================================================================

    /**
     * Créer une instance de ce master block
     */
    public function createInstance(Page $page, int $sortOrder = 1): ?Block
    {
        if (!$this->is_master_block) {
            return null;
        }

        return self::create([
            'page_id' => $page->id,
            'master_block_id' => $this->id,
            'type' => $this->type,
            'content' => $this->content,
            'settings' => $this->settings,
            'styles' => $this->styles,
            'mobile_styles' => $this->mobile_styles,
            'sort_order' => $sortOrder,
            'is_active' => true,
        ]);
    }

    /**
     * Synchronise cette instance avec son master block
     */
    public function syncFromMaster(): self
    {
        if (!$this->master_block_id) {
            return $this;
        }

        $master = $this->masterBlock;
        if ($master) {
            $this->content = $master->content;
            $this->settings = $master->settings;
            $this->styles = $master->styles;
            $this->mobile_styles = $master->mobile_styles;
            $this->save();
        }

        return $this;
    }

    /**
     * Met à jour toutes les instances de ce master block
     */
    public function syncToInstances(): int
    {
        if (!$this->is_master_block) {
            return 0;
        }

        return $this->instances()->update([
            'content' => $this->content,
            'settings' => $this->settings,
            'styles' => $this->styles,
            'mobile_styles' => $this->mobile_styles,
        ]);
    }

    // =========================================================================
    // COLUMNS HELPERS
    // =========================================================================

    /**
     * Parse la configuration des colonnes
     */
    public function getColumnsConfig(): array
    {
        if (!$this->columns_layout) {
            return [['width' => '1/1']];
        }

        $columns = explode(',', $this->columns_layout);
        return array_map(fn($width) => ['width' => trim($width)], $columns);
    }

    /**
     * Retourne les blocs enfants par colonne
     */
    public function getChildrenByColumn(): array
    {
        $grouped = [];
        foreach ($this->children as $child) {
            $index = $child->column_index ?? 0;
            $grouped[$index][] = $child;
        }
        return $grouped;
    }

    // =========================================================================
    // RENDER HELPERS
    // =========================================================================

    public function getVideoEmbedUrl(): ?string
    {
        if (!$this->isVideo()) {
            return null;
        }

        $url = $this->getContent('url') ?? $this->getContent('video_url');
        if (!$url) {
            return null;
        }

        // YouTube
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return "https://www.youtube.com/embed/{$matches[1]}";
        }

        // Vimeo
        if (preg_match('/vimeo\.com\/(\d+)/', $url, $matches)) {
            return "https://player.vimeo.com/video/{$matches[1]}";
        }

        return $url;
    }

    public function getFormFields(): array
    {
        if (!$this->isForm()) {
            return [];
        }

        return $this->getContent('fields', []);
    }

    /**
     * Retourne le HTML d'animation si configuré
     */
    public function getAnimationClasses(): string
    {
        if (!$this->animation) {
            return '';
        }

        $classes = "animate-{$this->animation}";
        if ($this->animation_delay) {
            $classes .= " animation-delay-{$this->animation_delay}";
        }

        return $classes;
    }

    /**
     * Vérifie si le bloc doit être affiché selon les conditions
     * 
     * Format des conditions:
     * [
     *   'operator' => 'AND' | 'OR',  // Comment combiner les règles (défaut: AND)
     *   'rules' => [
     *     ['type' => 'date_range', 'start' => '2025-01-01', 'end' => '2025-12-31'],
     *     ['type' => 'time_range', 'start' => '09:00', 'end' => '18:00'],
     *     ['type' => 'day_of_week', 'days' => [1, 2, 3, 4, 5]], // Lun-Ven
     *     ['type' => 'url_contains', 'value' => 'utm_source=facebook'],
     *     ['type' => 'url_equals', 'value' => '/promo'],
     *     ['type' => 'cookie_exists', 'name' => 'returning_visitor'],
     *     ['type' => 'cookie_equals', 'name' => 'segment', 'value' => 'premium'],
     *     ['type' => 'device', 'value' => 'mobile' | 'desktop' | 'tablet'],
     *     ['type' => 'referrer_contains', 'value' => 'google.com'],
     *     ['type' => 'context_variable', 'key' => 'quiz_score', 'operator' => '>', 'value' => 50],
     *     ['type' => 'visitor_count', 'operator' => '>=', 'value' => 2],
     *   ]
     * ]
     */
    public function shouldDisplay(array $context = []): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if (empty($this->display_conditions)) {
            return true;
        }

        $conditions = $this->display_conditions;
        $operator = strtoupper($conditions['operator'] ?? 'AND');
        $rules = $conditions['rules'] ?? [];

        if (empty($rules)) {
            return true;
        }

        $results = [];
        foreach ($rules as $rule) {
            $results[] = $this->evaluateRule($rule, $context);
        }

        if ($operator === 'OR') {
            return in_array(true, $results, true);
        }

        // Default: AND
        return !in_array(false, $results, true);
    }

    /**
     * Évalue une règle individuelle
     */
    protected function evaluateRule(array $rule, array $context = []): bool
    {
        $type = $rule['type'] ?? '';

        return match ($type) {
            'date_range' => $this->evaluateDateRange($rule),
            'time_range' => $this->evaluateTimeRange($rule),
            'day_of_week' => $this->evaluateDayOfWeek($rule),
            'url_contains' => $this->evaluateUrlContains($rule),
            'url_equals' => $this->evaluateUrlEquals($rule),
            'cookie_exists' => $this->evaluateCookieExists($rule),
            'cookie_equals' => $this->evaluateCookieEquals($rule),
            'device' => $this->evaluateDevice($rule),
            'referrer_contains' => $this->evaluateReferrerContains($rule),
            'context_variable' => $this->evaluateContextVariable($rule, $context),
            'visitor_count' => $this->evaluateVisitorCount($rule),
            'is_logged_in' => $this->evaluateIsLoggedIn($rule),
            'user_role' => $this->evaluateUserRole($rule),
            default => true, // Type inconnu = condition passée
        };
    }

    protected function evaluateDateRange(array $rule): bool
    {
        $now = now();
        $start = isset($rule['start']) ? \Carbon\Carbon::parse($rule['start'])->startOfDay() : null;
        $end = isset($rule['end']) ? \Carbon\Carbon::parse($rule['end'])->endOfDay() : null;

        if ($start && $now->lt($start))
            return false;
        if ($end && $now->gt($end))
            return false;

        return true;
    }

    protected function evaluateTimeRange(array $rule): bool
    {
        $now = now();
        $currentTime = $now->format('H:i');

        $start = $rule['start'] ?? '00:00';
        $end = $rule['end'] ?? '23:59';

        return $currentTime >= $start && $currentTime <= $end;
    }

    protected function evaluateDayOfWeek(array $rule): bool
    {
        $today = now()->dayOfWeek; // 0 = Sunday, 1 = Monday, etc.
        $allowedDays = $rule['days'] ?? [0, 1, 2, 3, 4, 5, 6];

        return in_array($today, $allowedDays);
    }

    protected function evaluateUrlContains(array $rule): bool
    {
        $url = request()->fullUrl();
        return str_contains($url, $rule['value'] ?? '');
    }

    protected function evaluateUrlEquals(array $rule): bool
    {
        $path = request()->path();
        return $path === ltrim($rule['value'] ?? '', '/');
    }

    protected function evaluateCookieExists(array $rule): bool
    {
        return request()->cookie($rule['name'] ?? '') !== null;
    }

    protected function evaluateCookieEquals(array $rule): bool
    {
        $cookieValue = request()->cookie($rule['name'] ?? '');
        return $cookieValue === ($rule['value'] ?? null);
    }

    protected function evaluateDevice(array $rule): bool
    {
        $agent = request()->userAgent();
        $expectedDevice = strtolower($rule['value'] ?? 'desktop');

        // Simple device detection
        $isMobile = preg_match('/Mobile|Android|iPhone|iPad/i', $agent);
        $isTablet = preg_match('/iPad|Tablet/i', $agent);

        return match ($expectedDevice) {
            'mobile' => $isMobile && !$isTablet,
            'tablet' => $isTablet,
            'desktop' => !$isMobile && !$isTablet,
            default => true,
        };
    }

    protected function evaluateReferrerContains(array $rule): bool
    {
        $referrer = request()->header('referer', '');
        return str_contains($referrer, $rule['value'] ?? '');
    }

    protected function evaluateContextVariable(array $rule, array $context): bool
    {
        $key = $rule['key'] ?? '';
        $operator = $rule['operator'] ?? '=';
        $expectedValue = $rule['value'] ?? null;

        $actualValue = $context[$key] ?? null;

        return match ($operator) {
            '=', '==' => $actualValue == $expectedValue,
            '===' => $actualValue === $expectedValue,
            '!=', '<>' => $actualValue != $expectedValue,
            '>' => $actualValue > $expectedValue,
            '>=' => $actualValue >= $expectedValue,
            '<' => $actualValue < $expectedValue,
            '<=' => $actualValue <= $expectedValue,
            'contains' => is_string($actualValue) && str_contains($actualValue, $expectedValue),
            'in' => is_array($expectedValue) && in_array($actualValue, $expectedValue),
            default => true,
        };
    }

    protected function evaluateVisitorCount(array $rule): bool
    {
        // Récupérer le compteur de visites depuis le cookie ou la session
        $visitCount = (int) (request()->cookie('visit_count') ?? session('visit_count', 1));
        $operator = $rule['operator'] ?? '>=';
        $expectedValue = (int) ($rule['value'] ?? 1);

        return match ($operator) {
            '=', '==' => $visitCount == $expectedValue,
            '>' => $visitCount > $expectedValue,
            '>=' => $visitCount >= $expectedValue,
            '<' => $visitCount < $expectedValue,
            '<=' => $visitCount <= $expectedValue,
            default => true,
        };
    }

    protected function evaluateIsLoggedIn(array $rule): bool
    {
        $expectedLoggedIn = $rule['value'] ?? true;
        $isLoggedIn = auth()->check();

        return $expectedLoggedIn ? $isLoggedIn : !$isLoggedIn;
    }

    protected function evaluateUserRole(array $rule): bool
    {
        if (!auth()->check())
            return false;

        $expectedRole = $rule['value'] ?? '';
        $user = auth()->user();

        // Utiliser Spatie Permissions si disponible
        if (method_exists($user, 'hasRole')) {
            return $user->hasRole($expectedRole);
        }

        return false;
    }

    // =========================================================================
    // DUPLICATION
    // =========================================================================

    /**
     * Duplique le bloc avec ses enfants
     */
    public function duplicate(?int $newPageId = null, ?int $newParentId = null): Block
    {
        $newBlock = $this->replicate();
        $newBlock->uuid = (string) \Illuminate\Support\Str::uuid();
        $newBlock->page_id = $newPageId ?? $this->page_id;
        $newBlock->parent_id = $newParentId;
        $newBlock->master_block_id = null; // Ne pas lier au master
        $newBlock->is_master_block = false;
        $newBlock->save();

        // Dupliquer les enfants récursivement
        foreach ($this->children as $child) {
            $child->duplicate($newBlock->page_id, $newBlock->id);
        }

        return $newBlock;
    }
}
