<?php

namespace App\Livewire\PageBuilder;

use App\Models\Block;
use App\Models\Page;
use App\Services\PageBuilderService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;

class PageBuilder extends Component
{
    use WithFileUploads;

    public Page $page;
    public ?Block $selectedBlock = null;
    public string $selectedBlockId = '';
    public bool $showBlockPanel = false;
    public bool $showSettingsPanel = false;
    public string $previewMode = 'desktop'; // desktop, tablet, mobile

    #[Url]
    public string $view = 'editor'; // editor, preview

    // Page Settings
    public array $pageSettings = [];

    protected PageBuilderService $builderService;

    public function boot(PageBuilderService $builderService): void
    {
        $this->builderService = $builderService;
    }

    public function mount(string $model, int $modelId): void
    {
        // Vérifier que le modèle est bien 'Page'
        if ($model !== 'Page') {
            abort(404, 'Modèle non supporté');
        }

        // Charger la page avec ses relations
        $this->page = Page::with([
            'blocks' => function ($query) {
                $query->active()->ordered();
            },
            'funnel'
        ])->findOrFail($modelId);

        $this->pageSettings = $this->page->settings ?? [];

        // Log de débogage
        \Log::info("PageBuilder mounted for page {$this->page->id}", [
            'page_title' => $this->page->name,
            'blocks_count' => $this->page->blocks->count(),
        ]);
    }

    public function savePageSettings(): void
    {
        $this->page->update(['settings' => $this->pageSettings]);
        $this->dispatch('settings-saved');
    }

    #[Computed]
    public function blocks()
    {
        // Obtenir TOUS les blocs pour debug
        $allBlocks = $this->page->blocks()->get();

        // Obtenir uniquement les blocs actifs et ordonnés
        $blocks = $this->page->blocks()->active()->ordered()->get();

        // Debug logging
        \Log::info("PageBuilder blocks() for page {$this->page->id}:", [
            'total_blocks_in_db' => $allBlocks->count(),
            'active_ordered_blocks' => $blocks->count(),
            'all_blocks_details' => $allBlocks->map(fn($b) => [
                'id' => $b->id,
                'type' => $b->type instanceof \App\Enums\BlockType ? $b->type->value : $b->type,
                'active' => $b->is_active,
                'sort_order' => $b->sort_order,
            ])->toArray(),
            'filtered_blocks' => $blocks->map(fn($b) => [
                'id' => $b->id,
                'type' => $b->type instanceof \App\Enums\BlockType ? $b->type->value : $b->type,
                'active' => $b->is_active,
                'mobile' => $b->show_on_mobile,
                'desktop' => $b->show_on_desktop,
            ])->toArray()
        ]);

        return $blocks;
    }

    #[Computed]
    public function blockTypes()
    {
        return app(PageBuilderService::class)->getBlockTypesByCategory();
    }

    #[Computed]
    public function branding()
    {
        return app(PageBuilderService::class)->getPageBranding($this->page);
    }

    #[Computed]
    public function availableFonts()
    {
        return app(PageBuilderService::class)->getAvailableFonts();
    }

    // ===== Block Management =====

    public function addBlock(string $type): void
    {
        $service = app(PageBuilderService::class);
        $block = $service->createBlock($this->page, $type);

        $this->selectBlock($block->id);
        $this->showBlockPanel = false;

        $this->dispatch('block-added', blockId: $block->id);
        $this->dispatch('notify', message: 'Bloc ajouté', type: 'success');
    }

    /**
     * Ajoute un bloc enfant à un bloc parent (Section)
     */
    public function addBlockToParent(int $parentId, string $type): void
    {
        $parent = Block::find($parentId);
        if (!$parent) {
            $this->dispatch('notify', message: 'Bloc parent introuvable', type: 'error');
            return;
        }

        $service = app(PageBuilderService::class);
        $blockTypes = collect($service->getAvailableBlockTypes());
        $blockConfig = $blockTypes->firstWhere('type', $type);

        if (!$blockConfig) {
            $this->dispatch('notify', message: "Type de bloc inconnu: {$type}", type: 'error');
            return;
        }

        $maxOrder = $parent->children()->max('sort_order') ?? 0;

        $block = Block::create([
            'page_id' => $this->page->id,
            'parent_id' => $parentId,
            'depth' => ($parent->depth ?? 0) + 1,
            'type' => $type,
            'content' => $blockConfig['defaults']['content'] ?? [],
            'styles' => $blockConfig['defaults']['styles'] ?? [],
            'settings' => [],
            'sort_order' => $maxOrder + 1,
            'is_active' => true,
            'show_on_mobile' => true,
            'show_on_desktop' => true,
        ]);

        $this->selectBlock($block->id);
        $this->dispatch('block-added', blockId: $block->id);
        $this->dispatch('notify', message: 'Bloc ajouté dans la section', type: 'success');
    }

    /**
     * Ajoute un bloc dans une colonne spécifique d'un bloc Columns
     */
    public function addBlockToColumn(int $parentId, int $columnIndex, string $type): void
    {
        $parent = Block::find($parentId);
        if (!$parent) {
            $this->dispatch('notify', message: 'Bloc parent introuvable', type: 'error');
            return;
        }

        $service = app(PageBuilderService::class);
        $blockTypes = collect($service->getAvailableBlockTypes());
        $blockConfig = $blockTypes->firstWhere('type', $type);

        if (!$blockConfig) {
            $this->dispatch('notify', message: "Type de bloc inconnu: {$type}", type: 'error');
            return;
        }

        // Trouver l'ordre max dans cette colonne
        $maxOrder = Block::where('parent_id', $parentId)
            ->where('column_index', $columnIndex)
            ->max('sort_order') ?? 0;

        $block = Block::create([
            'page_id' => $this->page->id,
            'parent_id' => $parentId,
            'column_index' => $columnIndex,
            'depth' => ($parent->depth ?? 0) + 1,
            'type' => $type,
            'content' => $blockConfig['defaults']['content'] ?? [],
            'styles' => $blockConfig['defaults']['styles'] ?? [],
            'settings' => [],
            'sort_order' => $maxOrder + 1,
            'is_active' => true,
            'show_on_mobile' => true,
            'show_on_desktop' => true,
        ]);

        $this->selectBlock($block->id);
        $this->dispatch('block-added', blockId: $block->id);
        $this->dispatch('notify', message: 'Bloc ajouté dans la colonne ' . ($columnIndex + 1), type: 'success');
    }

    public function selectBlock(int $blockId): void
    {
        $this->selectedBlockId = (string) $blockId;
        $this->selectedBlock = Block::find($blockId);
        $this->showSettingsPanel = false; // Fermer le settings panel pour ouvrir le block editor
    }

    public function deselectBlock(): void
    {
        $this->selectedBlockId = '';
        $this->selectedBlock = null;
        $this->showSettingsPanel = false;
    }

    #[On('block-updated')]
    public function refreshBlock($blockId): void
    {
        // Rafraîchir le bloc sélectionné si c'est celui qui a été modifié
        if ($this->selectedBlock && $this->selectedBlock->id === $blockId) {
            $this->selectedBlock = Block::find($blockId);
        }

        // Forcer le rafraîchissement de la liste des blocs
        unset($this->blocks);
    }

    public function updateBlockContent(int $blockId, array $content): void
    {
        $block = Block::findOrFail($blockId);
        $block->update(['content' => array_merge($block->content ?? [], $content)]);

        if ($this->selectedBlock && $this->selectedBlock->id === $blockId) {
            $this->selectedBlock = $block->fresh();
        }

        $this->dispatch('block-updated', blockId: $blockId);
    }

    public function updateBlockStyles(int $blockId, array $styles): void
    {
        $block = Block::findOrFail($blockId);
        $block->update(['styles' => array_merge($block->styles ?? [], $styles)]);

        if ($this->selectedBlock && $this->selectedBlock->id === $blockId) {
            $this->selectedBlock = $block->fresh();
        }

        $this->dispatch('block-updated', blockId: $blockId);
    }

    public function duplicateBlock(int $blockId): void
    {
        $block = Block::findOrFail($blockId);
        $service = app(PageBuilderService::class);
        $newBlock = $service->duplicateBlock($block);

        $this->selectBlock($newBlock->id);
        $this->dispatch('notify', message: 'Bloc dupliqué', type: 'success');
    }

    public function deleteBlock(int $blockId): void
    {
        Block::findOrFail($blockId)->delete();

        if ($this->selectedBlockId === (string) $blockId) {
            $this->deselectBlock();
        }

        $this->dispatch('block-deleted', blockId: $blockId);
        $this->dispatch('notify', message: 'Bloc supprimé', type: 'success');
    }

    public function moveBlockUp(int $blockId): void
    {
        $block = Block::findOrFail($blockId);
        /** @var Block|null $previousBlock */
        $previousBlock = $this->page->blocks()
            ->where('sort_order', '<', $block->sort_order)
            ->orderByDesc('sort_order')
            ->first();

        if ($previousBlock) {
            $tempOrder = $block->sort_order;
            $block->update(['sort_order' => $previousBlock->sort_order]);
            $previousBlock->update(['sort_order' => $tempOrder]);
        }

        $this->dispatch('blocks-reordered');
    }

    public function moveBlockDown(int $blockId): void
    {
        $block = Block::findOrFail($blockId);
        /** @var Block|null $nextBlock */
        $nextBlock = $this->page->blocks()
            ->where('sort_order', '>', $block->sort_order)
            ->orderBy('sort_order')
            ->first();

        if ($nextBlock) {
            $tempOrder = $block->sort_order;
            $block->update(['sort_order' => $nextBlock->sort_order]);
            $nextBlock->update(['sort_order' => $tempOrder]);
        }

        $this->dispatch('blocks-reordered');
    }

    public function reorderBlocks(array $order): void
    {
        $service = app(PageBuilderService::class);
        $service->reorderBlocks($this->page, $order);

        $this->dispatch('blocks-reordered');
    }

    public function toggleBlockVisibility(int $blockId, string $device): void
    {
        $block = Block::findOrFail($blockId);
        $field = $device === 'mobile' ? 'show_on_mobile' : 'show_on_desktop';
        $block->update([$field => !$block->$field]);

        $this->dispatch('block-updated', blockId: $blockId);
    }

    // ===== Page Settings =====

    public function updatePageBranding(array $branding): void
    {
        $this->page->update([
            'branding' => array_merge($this->page->branding ?? [], $branding),
        ]);

        $this->dispatch('branding-updated');
        $this->dispatch('notify', message: 'Branding mis à jour', type: 'success');
    }

    public function updatePageMeta(array $meta): void
    {
        $this->page->update([
            'meta_title' => $meta['meta_title'] ?? $this->page->meta_title,
            'meta_description' => $meta['meta_description'] ?? $this->page->meta_description,
        ]);

        $this->dispatch('notify', message: 'SEO mis à jour', type: 'success');
    }

    // ===== View Modes =====

    public function setPreviewMode(string $mode): void
    {
        $this->previewMode = $mode;
    }

    public function toggleView(): void
    {
        $this->view = $this->view === 'editor' ? 'preview' : 'editor';
    }

    // ===== Panels =====

    public function toggleBlockPanel(): void
    {
        $this->showBlockPanel = !$this->showBlockPanel;
    }

    public function toggleSettingsPanel(): void
    {
        $this->showSettingsPanel = !$this->showSettingsPanel;
    }

    // ===== Master Blocks =====

    public bool $showMasterBlockPanel = false;

    #[Computed]
    public function masterBlocks()
    {
        $service = app(\App\Services\MasterBlockService::class);
        $tenantId = $this->page->funnel?->tenant_id;
        return $service->getMasterBlocksByCategory($tenantId);
    }

    public function toggleMasterBlockPanel(): void
    {
        $this->showMasterBlockPanel = !$this->showMasterBlockPanel;
    }

    /**
     * Ajoute une instance d'un master block à la page
     */
    public function addMasterBlockInstance(int $masterBlockId): void
    {
        $masterBlock = Block::find($masterBlockId);
        if (!$masterBlock || !$masterBlock->is_master_block) {
            $this->dispatch('notify', message: 'Master block introuvable', type: 'error');
            return;
        }

        $service = app(\App\Services\MasterBlockService::class);
        $instance = $service->createInstance($masterBlock, $this->page);

        if ($instance) {
            $this->selectBlock($instance->id);
            $this->showMasterBlockPanel = false;
            $this->dispatch('block-added', blockId: $instance->id);
            $this->dispatch('notify', message: 'Master block ajouté', type: 'success');
        }
    }

    /**
     * Convertit le bloc sélectionné en master block
     */
    public function convertToMasterBlock(int $blockId, string $name): void
    {
        $block = Block::find($blockId);
        if (!$block) {
            $this->dispatch('notify', message: 'Bloc introuvable', type: 'error');
            return;
        }

        $service = app(\App\Services\MasterBlockService::class);
        $service->convertToMasterBlock($block, $name);

        $this->selectedBlock = $block->fresh();
        $this->dispatch('notify', message: 'Bloc converti en Master Block', type: 'success');
    }

    /**
     * Synchronise une instance avec son master block
     */
    public function syncBlockFromMaster(int $blockId): void
    {
        $block = Block::find($blockId);
        if (!$block || !$block->master_block_id) {
            $this->dispatch('notify', message: 'Ce bloc n\'est pas une instance de master block', type: 'error');
            return;
        }

        $service = app(\App\Services\MasterBlockService::class);
        if ($service->syncInstanceFromMaster($block)) {
            $this->selectedBlock = $block->fresh();
            $this->dispatch('block-updated', blockId: $blockId);
            $this->dispatch('notify', message: 'Bloc synchronisé depuis le master', type: 'success');
        }
    }

    /**
     * Détache une instance de son master block
     */
    public function detachFromMaster(int $blockId): void
    {
        $block = Block::find($blockId);
        if (!$block || !$block->master_block_id) {
            return;
        }

        $service = app(\App\Services\MasterBlockService::class);
        $service->detachInstance($block);

        $this->selectedBlock = $block->fresh();
        $this->dispatch('notify', message: 'Bloc détaché du master', type: 'success');
    }

    /**
     * Met à jour toutes les instances depuis ce master block
     */
    public function syncMasterToAllInstances(int $blockId): void
    {
        $block = Block::find($blockId);
        if (!$block || !$block->is_master_block) {
            return;
        }

        $service = app(\App\Services\MasterBlockService::class);
        $count = $service->syncMasterToInstances($block);

        $this->dispatch('notify', message: "{$count} instances mises à jour", type: 'success');
    }

    // ===== Display Conditions =====

    /**
     * Met à jour les conditions d'affichage d'un bloc
     */
    public function updateBlockDisplayConditions(int $blockId, array $conditions): void
    {
        $block = Block::findOrFail($blockId);
        $block->update(['display_conditions' => $conditions]);

        if ($this->selectedBlock && $this->selectedBlock->id === $blockId) {
            $this->selectedBlock = $block->fresh();
        }

        $this->dispatch('block-updated', blockId: $blockId);
        $this->dispatch('notify', message: 'Conditions mises à jour', type: 'success');
    }

    /**
     * Ajoute une règle de condition à un bloc
     */
    public function addDisplayConditionRule(int $blockId, array $rule): void
    {
        $block = Block::findOrFail($blockId);
        $conditions = $block->display_conditions ?? ['operator' => 'AND', 'rules' => []];
        $conditions['rules'][] = $rule;

        $block->update(['display_conditions' => $conditions]);

        if ($this->selectedBlock && $this->selectedBlock->id === $blockId) {
            $this->selectedBlock = $block->fresh();
        }

        $this->dispatch('block-updated', blockId: $blockId);
    }

    /**
     * Supprime une règle de condition
     */
    public function removeDisplayConditionRule(int $blockId, int $ruleIndex): void
    {
        $block = Block::findOrFail($blockId);
        $conditions = $block->display_conditions ?? ['operator' => 'AND', 'rules' => []];

        if (isset($conditions['rules'][$ruleIndex])) {
            array_splice($conditions['rules'], $ruleIndex, 1);
            $block->update(['display_conditions' => $conditions]);

            if ($this->selectedBlock && $this->selectedBlock->id === $blockId) {
                $this->selectedBlock = $block->fresh();
            }

            $this->dispatch('block-updated', blockId: $blockId);
        }
    }

    /**
     * Change l'opérateur de combinaison des conditions
     */
    public function setDisplayConditionOperator(int $blockId, string $operator): void
    {
        $block = Block::findOrFail($blockId);
        $conditions = $block->display_conditions ?? ['operator' => 'AND', 'rules' => []];
        $conditions['operator'] = strtoupper($operator) === 'OR' ? 'OR' : 'AND';

        $block->update(['display_conditions' => $conditions]);

        if ($this->selectedBlock && $this->selectedBlock->id === $blockId) {
            $this->selectedBlock = $block->fresh();
        }

        $this->dispatch('block-updated', blockId: $blockId);
    }

    /**
     * Efface toutes les conditions d'un bloc
     */
    public function clearDisplayConditions(int $blockId): void
    {
        $block = Block::findOrFail($blockId);
        $block->update(['display_conditions' => null]);

        if ($this->selectedBlock && $this->selectedBlock->id === $blockId) {
            $this->selectedBlock = $block->fresh();
        }

        $this->dispatch('block-updated', blockId: $blockId);
        $this->dispatch('notify', message: 'Conditions effacées', type: 'success');
    }

    public function render()
    {
        return view('livewire.page-builder.page-builder')
            ->layout('layouts.builder', [
                'page' => $this->page,
                'funnel' => $this->page->funnel,
            ]);
    }
}
