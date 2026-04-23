<?php

namespace App\Services;

use App\Enums\BlockType;
use App\Models\Block;
use App\Models\Page;
use Illuminate\Support\Collection;

/**
 * Service de gestion des Master Blocks
 * 
 * Les Master Blocks sont des blocs réutilisables qui peuvent être
 * instanciés sur plusieurs pages. Modifier le master met à jour
 * automatiquement toutes ses instances.
 */
class MasterBlockService
{
    /**
     * Récupère tous les master blocks disponibles pour un tenant
     */
    public function getAllMasterBlocks(?int $tenantId = null): Collection
    {
        $query = Block::query()->masterBlocks()->ordered();

        // Si un tenant ID est fourni, filtrer par tenant
        if ($tenantId) {
            $query->whereHas('page.funnel', function ($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId);
            });
        }

        return $query->get();
    }

    /**
     * Récupère les master blocks groupés par catégorie de type
     */
    public function getMasterBlocksByCategory(?int $tenantId = null): array
    {
        $blocks = $this->getAllMasterBlocks($tenantId);

        $categories = [];
        foreach ($blocks as $block) {
            $category = $block->type->getCategory();
            $categories[$category][] = $block;
        }

        return $categories;
    }

    /**
     * Crée un nouveau master block
     */
    public function createMasterBlock(array $data): Block
    {
        return Block::create(array_merge($data, [
            'is_master_block' => true,
            'is_active' => true,
        ]));
    }

    /**
     * Convertit un bloc existant en master block
     */
    public function convertToMasterBlock(Block $block, string $name): Block
    {
        $block->update([
            'is_master_block' => true,
            'master_block_name' => $name,
        ]);

        return $block->fresh();
    }

    /**
     * Crée une instance d'un master block sur une page
     */
    public function createInstance(Block $masterBlock, Page $page, int $sortOrder = 0): ?Block
    {
        if (!$masterBlock->is_master_block) {
            return null;
        }

        return Block::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'page_id' => $page->id,
            'master_block_id' => $masterBlock->id,
            'type' => $masterBlock->type,
            'content' => $masterBlock->content,
            'settings' => $masterBlock->settings,
            'styles' => $masterBlock->styles,
            'mobile_styles' => $masterBlock->mobile_styles,
            'animation' => $masterBlock->animation,
            'animation_delay' => $masterBlock->animation_delay,
            'sort_order' => $sortOrder ?: ($page->blocks()->max('sort_order') + 1),
            'is_active' => true,
            'is_master_block' => false,
            'show_on_mobile' => $masterBlock->show_on_mobile ?? true,
            'show_on_desktop' => $masterBlock->show_on_desktop ?? true,
        ]);
    }

    /**
     * Synchronise toutes les instances d'un master block
     * Retourne le nombre d'instances mises à jour
     */
    public function syncMasterToInstances(Block $masterBlock): int
    {
        if (!$masterBlock->is_master_block) {
            return 0;
        }

        return $masterBlock->instances()->update([
            'content' => $masterBlock->content,
            'settings' => $masterBlock->settings,
            'styles' => $masterBlock->styles,
            'mobile_styles' => $masterBlock->mobile_styles,
            'animation' => $masterBlock->animation,
            'animation_delay' => $masterBlock->animation_delay,
        ]);
    }

    /**
     * Synchronise une instance depuis son master block
     */
    public function syncInstanceFromMaster(Block $instance): bool
    {
        if (!$instance->master_block_id) {
            return false;
        }

        $master = $instance->masterBlock;
        if (!$master) {
            return false;
        }

        $instance->update([
            'content' => $master->content,
            'settings' => $master->settings,
            'styles' => $master->styles,
            'mobile_styles' => $master->mobile_styles,
            'animation' => $master->animation,
            'animation_delay' => $master->animation_delay,
        ]);

        return true;
    }

    /**
     * Détache une instance de son master block (la rend indépendante)
     */
    public function detachInstance(Block $instance): bool
    {
        if (!$instance->master_block_id) {
            return false;
        }

        $instance->update([
            'master_block_id' => null,
        ]);

        return true;
    }

    /**
     * Supprime un master block et optionnellement ses instances
     */
    public function deleteMasterBlock(Block $masterBlock, bool $deleteInstances = false): bool
    {
        if (!$masterBlock->is_master_block) {
            return false;
        }

        if ($deleteInstances) {
            // Supprimer toutes les instances
            $masterBlock->instances()->delete();
        } else {
            // Détacher toutes les instances
            $masterBlock->instances()->update(['master_block_id' => null]);
        }

        return $masterBlock->delete();
    }

    /**
     * Récupère le nombre d'instances pour un master block
     */
    public function getInstanceCount(Block $masterBlock): int
    {
        if (!$masterBlock->is_master_block) {
            return 0;
        }

        return $masterBlock->instances()->count();
    }

    /**
     * Liste les pages où un master block est utilisé
     */
    public function getUsagePages(Block $masterBlock): Collection
    {
        if (!$masterBlock->is_master_block) {
            return collect();
        }

        return Page::whereHas('blocks', function ($query) use ($masterBlock) {
            $query->where('master_block_id', $masterBlock->id);
        })->with('funnel:id,name')->get();
    }

    /**
     * Duplique un master block
     */
    public function duplicateMasterBlock(Block $masterBlock, string $newName): Block
    {
        $newBlock = $masterBlock->replicate();
        $newBlock->uuid = (string) \Illuminate\Support\Str::uuid();
        $newBlock->master_block_name = $newName;
        $newBlock->is_master_block = true;
        $newBlock->master_block_id = null;
        $newBlock->save();

        return $newBlock;
    }

    /**
     * Recherche des master blocks par nom
     */
    public function searchMasterBlocks(string $query, ?int $tenantId = null): Collection
    {
        $search = Block::query()
            ->masterBlocks()
            ->where(function ($q) use ($query) {
                $q->where('master_block_name', 'like', "%{$query}%")
                    ->orWhereRaw("JSON_EXTRACT(content, '$.title') LIKE ?", ["%{$query}%"]);
            });

        if ($tenantId) {
            $search->whereHas('page.funnel', function ($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId);
            });
        }

        return $search->get();
    }
}
