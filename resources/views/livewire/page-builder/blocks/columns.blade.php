{{-- Columns Block - PAGE BUILDER VERSION with per-column drop zones --}}
@php
    $columnsLayout = $block->columns_layout ?? $content['layout'] ?? '1/2,1/2';
    $columns = explode(',', $columnsLayout);
    $gap = $styles['gap'] ?? '24px';
    $gridCols = count($columns);
    
    // Récupérer les blocs enfants groupés par colonne
    $childrenByColumn = $block->getChildrenByColumn();
    
    // Convertir les fractions en pourcentages pour le grid
    $columnWidths = [];
    foreach ($columns as $col) {
        $col = trim($col);
        if (str_contains($col, '/')) {
            [$num, $den] = explode('/', $col);
            $columnWidths[] = (floatval($num) / floatval($den)) . 'fr';
        } else {
            $columnWidths[] = '1fr';
        }
    }
    $gridTemplate = implode(' ', $columnWidths);
@endphp

<div class="relative w-full group/columns">
    {{-- Columns Label --}}
    <div class="absolute -top-6 left-2 text-xs bg-purple-600 text-white px-2 py-0.5 rounded-t opacity-0 group-hover/columns:opacity-100 transition-opacity flex items-center gap-1">
        <x-heroicon-o-view-columns class="w-3 h-3" />
        Colonnes ({{ $gridCols }})
    </div>
    
    <div class="w-full grid border-2 border-dashed border-transparent hover:border-purple-500/50 rounded-lg transition-colors p-2"
         style="grid-template-columns: {{ $gridTemplate }}; gap: {{ $gap }};">
        
        @foreach($columns as $index => $width)
            @php
                $columnChildren = $childrenByColumn[$index] ?? [];
            @endphp
            
            <div class="column-{{ $index + 1 }} relative group/column min-h-[80px] border border-dashed border-gray-600 hover:border-purple-400 rounded-lg p-3 transition-colors">
                
                {{-- Column header --}}
                <div class="absolute -top-5 left-2 text-xs text-gray-500 group-hover/column:text-purple-400 transition-colors">
                    Col {{ $index + 1 }} ({{ trim($width) }})
                </div>
                
                {{-- Column children --}}
                @if(count($columnChildren) > 0)
                    <div class="space-y-3">
                        @foreach($columnChildren as $childBlock)
                            @php
                                $childBlockType = $childBlock->type->value ?? $childBlock->type;
                            @endphp
                            <div class="relative group/child cursor-pointer hover:ring-2 hover:ring-purple-400 rounded" 
                                 wire:key="column-child-{{ $childBlock->id }}"
                                 wire:click.stop="selectBlock({{ $childBlock->id }})">
                                
                                {{-- Child controls --}}
                                <div class="absolute -top-6 right-0 opacity-0 group-hover/child:opacity-100 transition-opacity z-10 flex gap-1">
                                    <button wire:click.stop="selectBlock({{ $childBlock->id }})"
                                            class="p-1 bg-blue-600 hover:bg-blue-700 rounded text-xs">
                                        <x-heroicon-o-pencil class="w-3 h-3" />
                                    </button>
                                    <button wire:click.stop="deleteBlock({{ $childBlock->id }})"
                                            class="p-1 bg-red-600 hover:bg-red-700 rounded text-xs"
                                            onclick="event.stopPropagation(); return confirm('Supprimer ?')">
                                        <x-heroicon-o-trash class="w-3 h-3" />
                                    </button>
                                </div>
                                
                                @include("livewire.page-builder.blocks.{$childBlockType}", [
                                    'block' => $childBlock,
                                    'content' => $childBlock->content ?? [],
                                    'styles' => $childBlock->styles ?? [],
                                ])
                            </div>
                        @endforeach
                    </div>
                @endif
                
                {{-- Add block button for this column --}}
                <div class="mt-3" x-data="{ showTypes: false }">
                    <button @click.stop="showTypes = !showTypes"
                            class="w-full py-2 border border-dashed border-gray-600 hover:border-purple-500 hover:bg-purple-500/10 rounded text-center transition-all">
                        <span class="text-xs text-gray-400 hover:text-purple-400 flex items-center justify-center gap-1">
                            <x-heroicon-o-plus class="w-4 h-4" />
                            Ajouter
                        </span>
                    </button>
                    
                    {{-- Quick type selector --}}
                    <div x-show="showTypes" 
                         x-transition
                         @click.outside="showTypes = false"
                         class="absolute bottom-full left-0 right-0 mb-2 bg-gray-800 border border-gray-600 rounded-lg p-2 shadow-xl z-20">
                        <div class="grid grid-cols-2 gap-1 text-xs" @click.stop>
                            <button @click.stop="$wire.addBlockToColumn({{ $block->id }}, {{ $index }}, 'title'); showTypes = false;" 
                                    class="px-2 py-1.5 bg-gray-700 hover:bg-purple-600 rounded transition text-left">
                                📝 Titre
                            </button>
                            <button @click.stop="$wire.addBlockToColumn({{ $block->id }}, {{ $index }}, 'text'); showTypes = false;" 
                                    class="px-2 py-1.5 bg-gray-700 hover:bg-purple-600 rounded transition text-left">
                                📄 Texte
                            </button>
                            <button @click.stop="$wire.addBlockToColumn({{ $block->id }}, {{ $index }}, 'image'); showTypes = false;" 
                                    class="px-2 py-1.5 bg-gray-700 hover:bg-purple-600 rounded transition text-left">
                                🖼️ Image
                            </button>
                            <button @click.stop="$wire.addBlockToColumn({{ $block->id }}, {{ $index }}, 'button'); showTypes = false;" 
                                    class="px-2 py-1.5 bg-gray-700 hover:bg-purple-600 rounded transition text-left">
                                🔘 Bouton
                            </button>
                            <button @click.stop="$wire.addBlockToColumn({{ $block->id }}, {{ $index }}, 'icon_box'); showTypes = false;" 
                                    class="px-2 py-1.5 bg-gray-700 hover:bg-purple-600 rounded transition text-left">
                                💡 Icon Box
                            </button>
                            <button @click.stop="$wire.addBlockToColumn({{ $block->id }}, {{ $index }}, 'video'); showTypes = false;" 
                                    class="px-2 py-1.5 bg-gray-700 hover:bg-purple-600 rounded transition text-left">
                                🎬 Vidéo
                            </button>
                        </div>
                        <button @click.stop="showTypes = false" 
                                class="w-full mt-2 px-2 py-1 bg-gray-600 hover:bg-gray-500 rounded text-xs transition">
                            ✕ Fermer
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    
    {{-- Layout selector --}}
    <div class="mt-3 flex justify-center gap-2 opacity-0 group-hover/columns:opacity-100 transition-opacity">
        <span class="text-xs text-gray-500 mr-2">Layout :</span>
        <button @click.stop="$wire.updateBlockContent({{ $block->id }}, {layout: '1/2,1/2'})"
                class="px-2 py-1 text-xs rounded {{ $columnsLayout === '1/2,1/2' ? 'bg-purple-600' : 'bg-gray-700 hover:bg-gray-600' }} transition">
            50/50
        </button>
        <button @click.stop="$wire.updateBlockContent({{ $block->id }}, {layout: '1/3,2/3'})"
                class="px-2 py-1 text-xs rounded {{ $columnsLayout === '1/3,2/3' ? 'bg-purple-600' : 'bg-gray-700 hover:bg-gray-600' }} transition">
            33/66
        </button>
        <button @click.stop="$wire.updateBlockContent({{ $block->id }}, {layout: '2/3,1/3'})"
                class="px-2 py-1 text-xs rounded {{ $columnsLayout === '2/3,1/3' ? 'bg-purple-600' : 'bg-gray-700 hover:bg-gray-600' }} transition">
            66/33
        </button>
        <button @click.stop="$wire.updateBlockContent({{ $block->id }}, {layout: '1/3,1/3,1/3'})"
                class="px-2 py-1 text-xs rounded {{ $columnsLayout === '1/3,1/3,1/3' ? 'bg-purple-600' : 'bg-gray-700 hover:bg-gray-600' }} transition">
            3 cols
        </button>
    </div>
</div>