<div class="h-screen flex flex-col" 
     x-data="{ 
        showBlockPanel: @entangle('showBlockPanel').live, 
        showSettingsPanel: @entangle('showSettingsPanel').live 
     }">
    {{-- Header Toolbar --}}
    <header class="h-14 bg-gray-800 border-b border-gray-700 flex items-center justify-between px-4 shrink-0">
        {{-- Left: Back & Page Info --}}
        <div class="flex items-center gap-4">
            @if($this->page->funnel_id)
                <a href="{{ route('filament.admin.resources.funnels.edit', ['record' => $this->page->funnel_id]) }}"
                   class="p-2 hover:bg-gray-700 rounded-lg transition">
                    <x-heroicon-o-arrow-left class="w-5 h-5" />
                </a>
            @endif
            <div>
                <h1 class="text-sm font-semibold">{{ $this->page->title }}</h1>
                @if($this->page->funnel)
                    <p class="text-xs text-gray-400">{{ $this->page->funnel->name }}</p>
                @endif
            </div>
        </div>

        {{-- Center: Preview Controls --}}
        <div class="flex items-center gap-2 bg-gray-900 rounded-lg p-1">
            <button wire:click="setPreviewMode('desktop')"
                    class="p-2 rounded {{ $previewMode === 'desktop' ? 'bg-blue-600' : 'hover:bg-gray-700' }} transition">
                <x-heroicon-o-computer-desktop class="w-5 h-5" />
            </button>
            <button wire:click="setPreviewMode('tablet')"
                    class="p-2 rounded {{ $previewMode === 'tablet' ? 'bg-blue-600' : 'hover:bg-gray-700' }} transition">
                <x-heroicon-o-device-tablet class="w-5 h-5" />
            </button>
            <button wire:click="setPreviewMode('mobile')"
                    class="p-2 rounded {{ $previewMode === 'mobile' ? 'bg-blue-600' : 'hover:bg-gray-700' }} transition">
                <x-heroicon-o-device-phone-mobile class="w-5 h-5" />
            </button>
        </div>

        {{-- Right: Actions --}}
        <div class="flex items-center gap-2">
            <div class="flex items-center gap-2 text-xs text-gray-400" x-data="{ saved: true }" @block-updated.window="saved = false; setTimeout(() => saved = true, 2000)">
                <div x-show="!saved" class="flex items-center gap-1">
                    <svg class="animate-spin h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Sauvegarde...</span>
                </div>
                <div x-show="saved" class="flex items-center gap-1 text-green-400">
                    <x-heroicon-o-check class="w-3 h-3" />
                    <span>Sauvegardé</span>
                </div>
            </div>
            @if($this->page->getPublicUrl())
                <a href="{{ $this->page->getPublicUrl() }}" target="_blank"
                   class="flex items-center gap-2 px-3 py-2 text-sm bg-gray-700 hover:bg-gray-600 rounded-lg transition">
                    <x-heroicon-o-eye class="w-4 h-4" />
                    <span>Prévisualiser</span>
                </a>
            @endif
        </div>
    </header>

    <div class="flex-1 flex overflow-hidden">
        {{-- Left Sidebar: Block Types --}}
        <aside class="w-16 bg-gray-800 border-r border-gray-700 flex flex-col items-center py-4 gap-2">
            <button wire:click="$toggle('showBlockPanel')"
                    class="p-3 rounded-lg transition {{ $showBlockPanel ? 'bg-blue-600' : 'hover:bg-gray-700' }}"
                    title="Ajouter un bloc">
                <x-heroicon-o-plus class="w-6 h-6" />
            </button>
            <button wire:click="$toggle('showSettingsPanel')"
                    class="p-3 rounded-lg transition {{ $showSettingsPanel ? 'bg-blue-600' : 'hover:bg-gray-700' }}"
                    title="Paramètres de la page">
                <x-heroicon-o-cog-6-tooth class="w-6 h-6" />
            </button>
            <div class="flex-1"></div>
            <button class="p-3 rounded-lg hover:bg-gray-700 transition" title="Aide">
                <x-heroicon-o-question-mark-circle class="w-6 h-6" />
            </button>
        </aside>

        {{-- Block Type Panel (Slide-over) --}}
        <aside x-show="showBlockPanel"
               x-transition:enter="transition ease-out duration-200"
               x-transition:enter-start="-translate-x-full"
               x-transition:enter-end="translate-x-0"
               x-transition:leave="transition ease-in duration-200"
               x-transition:leave-start="translate-x-0"
               x-transition:leave-end="-translate-x-full"
               class="w-72 bg-gray-800 border-r border-gray-700 overflow-y-auto builder-scrollbar">
            <div class="p-4" x-data="{ activeBlockTab: 'blocks' }">
                <h2 class="font-semibold mb-4 flex items-center gap-2">
                    <x-heroicon-o-squares-plus class="w-5 h-5 text-blue-400" />
                    Ajouter un bloc
                </h2>

                {{-- Tabs: Regular Blocks vs Master Blocks --}}
                <div class="flex gap-1 mb-4 bg-gray-900/50 p-1 rounded-lg">
                    <button @click="activeBlockTab = 'blocks'"
                            :class="activeBlockTab === 'blocks' ? 'bg-blue-600' : 'hover:bg-gray-700'"
                            class="flex-1 px-3 py-1.5 text-xs rounded transition flex items-center justify-center gap-1">
                        <x-heroicon-o-squares-plus class="w-4 h-4" />
                        Blocs
                    </button>
                    <button @click="activeBlockTab = 'master'"
                            :class="activeBlockTab === 'master' ? 'bg-purple-600' : 'hover:bg-gray-700'"
                            class="flex-1 px-3 py-1.5 text-xs rounded transition flex items-center justify-center gap-1">
                        <x-heroicon-o-cube class="w-4 h-4" />
                        Master
                    </button>
                </div>

                {{-- Regular Blocks Tab --}}
                <div x-show="activeBlockTab === 'blocks'">
                @foreach($this->blockTypes as $category => $blocks)
                    <div class="mb-6">
                        <h3 class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">
                            {{ match($category) {
                                'content' => 'Contenu',
                                'media' => 'Médias',
                                'action' => 'Actions',
                                'layout' => 'Mise en page',
                                'advanced' => 'Avancé',
                                default => $category
                            } }}
                        </h3>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach($blocks as $block)
                                <button wire:click="addBlock('{{ $block['type'] }}')"
                                        class="p-3 bg-gray-700 hover:bg-gray-600 rounded-lg transition text-left group">
                                    <div class="flex flex-col items-center text-center gap-1">
                                        <div class="w-8 h-8 flex items-center justify-center text-blue-400 group-hover:text-blue-300">
                                            @switch($block['type'])
                                                @case('title')
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16" />
                                                    </svg>
                                                    @break
                                                @case('text')
                                                    <x-heroicon-o-document-text class="w-6 h-6" />
                                                    @break
                                                @case('image')
                                                    <x-heroicon-o-photo class="w-6 h-6" />
                                                    @break
                                                @case('video')
                                                    <x-heroicon-o-video-camera class="w-6 h-6" />
                                                    @break
                                                @case('button')
                                                    <x-heroicon-o-cursor-arrow-rays class="w-6 h-6" />
                                                    @break
                                                @case('form')
                                                    <x-heroicon-o-clipboard-document-list class="w-6 h-6" />
                                                    @break
                                                @case('spacer')
                                                    <x-heroicon-o-arrows-up-down class="w-6 h-6" />
                                                    @break
                                                @case('divider')
                                                    <x-heroicon-o-minus class="w-6 h-6" />
                                                    @break
                                                @case('testimonial')
                                                    <x-heroicon-o-chat-bubble-bottom-center-text class="w-6 h-6" />
                                                    @break
                                                @case('countdown')
                                                    <x-heroicon-o-clock class="w-6 h-6" />
                                                    @break
                                                @case('section')
                                                    <x-heroicon-o-rectangle-group class="w-6 h-6" />
                                                    @break
                                                @case('columns')
                                                    <x-heroicon-o-view-columns class="w-6 h-6" />
                                                    @break
                                                @case('pricing')
                                                    <x-heroicon-o-currency-dollar class="w-6 h-6" />
                                                    @break
                                                @case('faq')
                                                    <x-heroicon-o-question-mark-circle class="w-6 h-6" />
                                                    @break
                                                @case('popup')
                                                    <x-heroicon-o-window class="w-6 h-6" />
                                                    @break
                                                @case('order_bump')
                                                    <x-heroicon-o-shopping-cart class="w-6 h-6" />
                                                    @break
                                                @case('trust_badges')
                                                    <x-heroicon-o-shield-check class="w-6 h-6" />
                                                    @break
                                                @case('social_icons')
                                                    <x-heroicon-o-share class="w-6 h-6" />
                                                    @break
                                                @case('icon_box')
                                                    <x-heroicon-o-squares-2x2 class="w-6 h-6" />
                                                    @break
                                                @case('progress')
                                                    <x-heroicon-o-chart-bar class="w-6 h-6" />
                                                    @break
                                                @case('audio')
                                                    <x-heroicon-o-musical-note class="w-6 h-6" />
                                                    @break
                                                @case('embed')
                                                    <x-heroicon-o-code-bracket class="w-6 h-6" />
                                                    @break
                                                @case('sticky_bar')
                                                    <x-heroicon-o-bars-3 class="w-6 h-6" />
                                                    @break
                                                @default
                                                    <x-heroicon-o-square-3-stack-3d class="w-6 h-6" />
                                            @endswitch
                                        </div>
                                        <span class="text-xs font-medium">{{ $block['label'] }}</span>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endforeach
                </div>

                {{-- Master Blocks Tab --}}
                <div x-show="activeBlockTab === 'master'" class="space-y-4">
                    @php
                        $masterBlocks = $this->masterBlocks;
                    @endphp
                    
                    @if(count($masterBlocks) > 0)
                        @foreach($masterBlocks as $category => $blocks)
                            <div class="mb-4">
                                <h3 class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">
                                    {{ match($category) {
                                        'content' => 'Contenu',
                                        'media' => 'Médias',
                                        'action' => 'Actions',
                                        'layout' => 'Mise en page',
                                        'advanced' => 'Avancé',
                                        default => ucfirst($category)
                                    } }}
                                </h3>
                                <div class="space-y-2">
                                    @foreach($blocks as $masterBlock)
                                        <button wire:click="addMasterBlockInstance({{ $masterBlock->id }})"
                                                class="w-full p-3 bg-purple-900/30 hover:bg-purple-900/50 border border-purple-500/30 rounded-lg transition text-left group">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 flex items-center justify-center bg-purple-500/20 rounded-lg text-purple-400">
                                                    <x-heroicon-o-cube class="w-5 h-5" />
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="font-medium text-sm truncate">{{ $masterBlock->master_block_name ?? 'Sans nom' }}</p>
                                                    <p class="text-xs text-gray-400">{{ $masterBlock->type->getLabel() }}</p>
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $masterBlock->instances()->count() }} usage(s)
                                                </div>
                                            </div>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-8">
                            <x-heroicon-o-cube class="w-12 h-12 text-gray-600 mx-auto mb-4" />
                            <p class="text-gray-400 mb-2">Aucun Master Block</p>
                            <p class="text-xs text-gray-500">Créez des master blocks en convertissant des blocs existants.</p>
                        </div>
                    @endif
                </div>
            </div>
        </aside>

        {{-- Main Canvas --}}
        <main class="flex-1 bg-gray-950 overflow-auto p-8">
            <div class="mx-auto transition-all duration-300 preview-{{ $previewMode }}"
                 style="background-color: {{ $this->branding['background_color'] ?? '#0F172A' }}; min-height: 100%;">

                {{-- Page Content --}}
                <div class="py-8 px-4 md:px-8" 
                     x-data="{ dragging: null }"
                     x-init="
                        Sortable.create($el, {
                            animation: 150,
                            handle: '.drag-handle',
                            ghostClass: 'opacity-50',
                            onEnd: function(evt) {
                                let order = Array.from(evt.to.children).map(el => el.dataset.blockId).filter(Boolean);
                                $wire.reorderBlocks(order);
                            }
                        });
                     ">
                    @forelse($this->blocks as $block)
                        <div wire:key="block-{{ $block->id }}"
                             data-block-id="{{ $block->id }}"
                             class="block-wrapper group relative mb-4 rounded transition-all duration-200
                                    {{ $selectedBlockId == $block->id ? 'ring-2 ring-blue-500 bg-blue-500/5' : 'hover:ring-2 hover:ring-gray-600' }}">

                            {{-- Block Controls Toolbar --}}
                            <div class="absolute -top-10 left-0 right-0 flex items-center justify-between opacity-0 group-hover:opacity-100 transition-opacity z-20"
                                 @click.stop>
                                <div class="flex items-center gap-1 bg-gray-800 rounded-lg shadow-lg p-1 border border-gray-700">
                                    {{-- Edit Button - Most Important --}}
                                    <button wire:click="selectBlock({{ $block->id }})"
                                            class="p-1.5 bg-blue-600 hover:bg-blue-700 rounded font-medium text-xs px-3" 
                                            title="Éditer ce bloc">
                                        <span class="flex items-center gap-1">
                                            <x-heroicon-o-pencil class="w-4 h-4" />
                                            Éditer
                                        </span>
                                    </button>
                                    <div class="w-px h-4 bg-gray-700"></div>
                                    {{-- Drag Handle --}}
                                    <button class="drag-handle p-1.5 hover:bg-gray-700 rounded cursor-move" title="Déplacer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/>
                                        </svg>
                                    </button>
                                    <button wire:click="duplicateBlock({{ $block->id }})"
                                            class="p-1.5 hover:bg-gray-700 rounded" title="Dupliquer">
                                        <x-heroicon-o-document-duplicate class="w-4 h-4" />
                                    </button>
                                    <button wire:click="toggleBlockVisibility({{ $block->id }}, 'desktop')"
                                            class="p-1.5 hover:bg-gray-700 rounded" title="Visibilité Desktop">
                                        <x-heroicon-o-computer-desktop class="w-4 h-4 {{ $block->show_on_desktop ? 'text-blue-400' : 'text-gray-600' }}" />
                                    </button>
                                    <button wire:click="toggleBlockVisibility({{ $block->id }}, 'mobile')"
                                            class="p-1.5 hover:bg-gray-700 rounded" title="Visibilité Mobile">
                                        <x-heroicon-o-device-phone-mobile class="w-4 h-4 {{ $block->show_on_mobile ? 'text-blue-400' : 'text-gray-600' }}" />
                                    </button>
                                    <div class="w-px h-4 bg-gray-700"></div>
                                    <button wire:click="deleteBlock({{ $block->id }})"
                                            class="p-1.5 hover:bg-red-600 rounded text-red-400 hover:text-white" 
                                            title="Supprimer"
                                            onclick="return confirm('Supprimer ce bloc ?')">
                                        <x-heroicon-o-trash class="w-4 h-4" />
                                    </button>
                                </div>
                                <div class="bg-gray-800 rounded-lg shadow-lg px-2 py-1 border border-gray-700">
                                    <span class="text-xs text-gray-400">{{ $block->type instanceof \App\Enums\BlockType ? $block->type->label() : $block->type }}</span>
                                </div>
                            </div>

                            {{-- Block Preview --}}
                            @include('livewire.page-builder.blocks.' . ($block->type->value ?? $block->type), [
                                'block' => $block,
                                'content' => $block->content ?? [],
                                'styles' => $block->styles ?? [],
                                'branding' => $this->branding
                            ])
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center py-20 text-gray-500">
                            <x-heroicon-o-squares-plus class="w-16 h-16 mb-4 opacity-50" />
                            <p class="text-lg mb-2">Cette page est vide</p>
                            <p class="text-sm mb-4">Cliquez sur le bouton + pour ajouter votre premier bloc</p>
                            <button @click="showBlockPanel = true"
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                                Ajouter un bloc
                            </button>
                        </div>
                    @endforelse
                </div>
            </div>
        </main>

        {{-- Right Panel: Settings or Block Editor --}}
        @if($showSettingsPanel)
            <aside class="w-96 bg-gray-800 border-l border-gray-700 overflow-hidden">
                @include('livewire.page-builder.page-settings-panel')
            </aside>
        @elseif($selectedBlock)
            <aside x-data="{ show: true }"
                   x-show="show"
                   x-transition:enter="transition ease-out duration-200"
                   x-transition:enter-start="translate-x-full"
                   x-transition:enter-end="translate-x-0"
                   class="w-80 bg-gray-800 border-l border-gray-700 overflow-y-auto builder-scrollbar">
                <livewire:page-builder.block-editor :block="$selectedBlock" :key="'editor-'.$selectedBlock->id" />
            </aside>
        @else
            <div class="w-80 bg-gray-800 border-l border-gray-700 flex items-center justify-center text-gray-500">
                <div class="text-center p-6">
                    <x-heroicon-o-cursor-arrow-rays class="w-12 h-12 mx-auto mb-3 opacity-50" />
                    <p class="text-sm">Passez la souris sur un bloc</p>
                    <p class="text-sm">et cliquez sur "Éditer"</p>
                </div>
            </div>
        @endif
    </div>
</div>
