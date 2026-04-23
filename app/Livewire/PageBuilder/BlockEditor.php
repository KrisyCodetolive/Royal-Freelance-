<?php

namespace App\Livewire\PageBuilder;

use App\Enums\BlockType;
use App\Models\Block;
use Livewire\Attributes\Modelable;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class BlockEditor extends Component
{
    use WithFileUploads;

    public Block $block;
    public array $content = [];
    public array $styles = [];
    public array $originalContent = [];
    public array $originalStyles = [];

    public $imageUpload;

    public function mount(Block $block): void
    {
        $this->block = $block;
        $this->content = $block->content ?? [];
        $this->styles = $block->styles ?? [];
        $this->originalContent = $this->content;
        $this->originalStyles = $this->styles;
    }

    // Listener pour chaque modification de propriété du content
    public function updatedContent($value, $key): void
    {
        $this->saveContent();
    }

    // Listener pour chaque modification de propriété des styles
    public function updatedStyles($value, $key): void
    {
        $this->saveStyles();
    }

    public function saveContent(): void
    {
        $this->block->update(['content' => $this->content]);
        $this->dispatch('block-updated', blockId: $this->block->id);
    }

    public function saveStyles(): void
    {
        $this->block->update(['styles' => $this->styles]);
        $this->dispatch('block-updated', blockId: $this->block->id);
    }

    public function undoChanges(): void
    {
        $this->content = $this->originalContent;
        $this->styles = $this->originalStyles;
        $this->saveContent();
        $this->saveStyles();
        $this->dispatch('notify', message: 'Modifications annulées', type: 'info');
    }

    public function updatedImageUpload(): void
    {
        if ($this->imageUpload) {
            $path = $this->imageUpload->store('builder-images', 'public');
            $url = '/storage/' . $path;

            $blockType = $this->block->type instanceof BlockType ? $this->block->type->value : $this->block->type;

            if (in_array($blockType, ['image', 'video'])) {
                $this->content['url'] = $url;
            } else {
                $this->content['image_url'] = $url;
            }

            $this->saveContent();
            $this->imageUpload = null; // Reset l'upload
        }
    }

    public function addFormField(): void
    {
        $fields = $this->content['fields'] ?? [];
        $fields[] = [
            'name' => 'field_' . count($fields),
            'type' => 'text',
            'label' => 'Nouveau champ',
            'required' => false,
            'placeholder' => '',
            'options_raw' => '',
        ];
        $this->content['fields'] = $fields;
        $this->saveContent();
    }

    public function removeFormField(int $index): void
    {
        $fields = $this->content['fields'] ?? [];
        unset($fields[$index]);
        $this->content['fields'] = array_values($fields);
        $this->saveContent();
    }

    public function addFeature(): void
    {
        $features = $this->content['features'] ?? [];
        $features[] = [
            'title' => 'Nouvel avantage',
            'text' => 'Description courte...',
            'icon' => 'heroicon-o-check-circle',
        ];
        $this->content['features'] = $features;
        $this->saveContent();
    }

    public function removeFeature(int $index): void
    {
        $features = $this->content['features'] ?? [];
        unset($features[$index]);
        $this->content['features'] = array_values($features);
        $this->saveContent();
    }

    public function render()
    {
        $blockType = $this->block->type instanceof BlockType
            ? $this->block->type->value
            : $this->block->type;

        return view('livewire.page-builder.block-editor', [
            'blockType' => $blockType,
        ]);
    }
}
