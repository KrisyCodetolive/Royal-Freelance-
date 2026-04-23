{{-- Section Block - PAGE BUILDER VERSION with drag & drop zone --}}
@php
    $paddingTop = $styles['paddingTop'] ?? '40px';
    $paddingBottom = $styles['paddingBottom'] ?? '40px';
    $backgroundColor = $styles['backgroundColor'] ?? 'transparent';
    $backgroundImage = $content['background_image'] ?? null;
    $borderRadius = $styles['borderRadius'] ?? '0';
    
    // Récupérer les blocs enfants
    $children = $block->children ?? collect();
@endphp

<section class="relative w-full group/section border-2 border-dashed border-transparent hover:border-blue-500/50 rounded-lg transition-colors"
    style="padding-top: {{ $paddingTop }}; padding-bottom: {{ $paddingBottom }}; background-color: {{ $backgroundColor }}; border-radius: {{ $borderRadius }};"
    @if($backgroundImage) 
        style="background-image: url('{{ $backgroundImage }}'); background-size: cover; background-position: center;"
    @endif>
    
    {{-- Section Label --}}
    <div class="absolute -top-6 left-2 text-xs bg-blue-600 text-white px-2 py-0.5 rounded-t opacity-0 group-hover/section:opacity-100 transition-opacity flex items-center gap-1">
        <x-heroicon-o-square-2-stack class="w-3 h-3" />
        Section
    </div>
    
    <div class="max-w-6xl mx-auto px-4">
        @if($children->count() > 0)
            {{-- Render child blocks --}}
            <div class="space-y-4">
                @foreach($children as $childBlock)
                    @php
                        $childBlockType = $childBlock->type->value ?? $childBlock->type;
                    @endphp
                    <div class="relative group/child cursor-pointer hover:ring-2 hover:ring-blue-400 rounded" 
                         wire:key="section-child-{{ $childBlock->id }}"
                         wire:click.stop="selectBlock({{ $childBlock->id }})">
                        
                        {{-- Child block controls --}}
                        <div class="absolute -top-8 right-0 opacity-0 group-hover/child:opacity-100 transition-opacity z-10 flex gap-1">
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
        
        {{-- Drop zone / Add button --}}
        <div class="mt-4" x-data="{ showTypes: false }">
            <div @click.stop="showTypes = !showTypes"
                 class="border-2 border-dashed border-gray-600 hover:border-blue-500 rounded-lg p-4 text-center transition-colors cursor-pointer">
                
                <div x-show="!showTypes" class="flex items-center justify-center gap-2 text-gray-400 hover:text-blue-400 transition-colors">
                    <x-heroicon-o-plus-circle class="w-5 h-5" />
                    <span class="text-sm">Ajouter un bloc dans cette section</span>
                </div>
                
                {{-- Quick block type selector --}}
                <div x-show="showTypes" x-transition class="flex flex-wrap justify-center gap-2" @click.stop>
                    <button @click.stop="$wire.addBlockToParent({{ $block->id }}, 'title'); showTypes = false;" 
                            class="px-3 py-1.5 bg-gray-700 hover:bg-blue-600 rounded text-xs transition">
                        📝 Titre
                    </button>
                    <button @click.stop="$wire.addBlockToParent({{ $block->id }}, 'text'); showTypes = false;" 
                            class="px-3 py-1.5 bg-gray-700 hover:bg-blue-600 rounded text-xs transition">
                        📄 Texte
                    </button>
                    <button @click.stop="$wire.addBlockToParent({{ $block->id }}, 'image'); showTypes = false;" 
                            class="px-3 py-1.5 bg-gray-700 hover:bg-blue-600 rounded text-xs transition">
                        🖼️ Image
                    </button>
                    <button @click.stop="$wire.addBlockToParent({{ $block->id }}, 'button'); showTypes = false;" 
                            class="px-3 py-1.5 bg-gray-700 hover:bg-blue-600 rounded text-xs transition">
                        🔘 Bouton
                    </button>
                    <button @click.stop="$wire.addBlockToParent({{ $block->id }}, 'video'); showTypes = false;" 
                            class="px-3 py-1.5 bg-gray-700 hover:bg-blue-600 rounded text-xs transition">
                        🎬 Vidéo
                    </button>
                    <button @click.stop="$wire.addBlockToParent({{ $block->id }}, 'form'); showTypes = false;" 
                            class="px-3 py-1.5 bg-gray-700 hover:bg-blue-600 rounded text-xs transition">
                        📋 Formulaire
                    </button>
                    <button @click.stop="$wire.addBlockToParent({{ $block->id }}, 'icon_box'); showTypes = false;" 
                            class="px-3 py-1.5 bg-gray-700 hover:bg-blue-600 rounded text-xs transition">
                        💡 Icon Box
                    </button>
                    <button @click.stop="showTypes = false" 
                            class="px-3 py-1.5 bg-gray-600 hover:bg-gray-500 rounded text-xs transition">
                        ✕ Fermer
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>