<div class="h-full flex flex-col bg-gray-800">
    {{-- Header --}}
    <div class="flex items-center justify-between p-4 border-b border-gray-700 bg-gray-900 shrink-0">
        <div>
            <h2 class="font-semibold flex items-center gap-2 text-base">
            @switch($blockType)
                    @case('title')
                        <x-heroicon-o-h1 class="w-5 h-5 text-blue-400" />
                        <span>Titre Accrocheur</span>
                        @break
                    @case('text')
                        <x-heroicon-o-document-text class="w-5 h-5 text-blue-400" />
                        <span>Texte de Vente</span>
                        @break
                    @case('button')
                        <x-heroicon-o-cursor-arrow-rays class="w-5 h-5 text-green-400" />
                        <span>Call-to-Action</span>
                        @break
                    @case('form')
                        <x-heroicon-o-clipboard-document-list class="w-5 h-5 text-purple-400" />
                        <span>Formulaire Lead</span>
                        @break
                    @case('countdown')
                        <x-heroicon-o-clock class="w-5 h-5 text-red-400" />
                        <span>Urgence & Rareté</span>
                        @break
                    @case('pricing')
                        <x-heroicon-o-currency-dollar class="w-5 h-5 text-green-400" />
                        <span>Tableau Tarifs</span>
                        @break
                    @case('faq')
                        <x-heroicon-o-question-mark-circle class="w-5 h-5 text-amber-400" />
                        <span>FAQ Accordéon</span>
                        @break
                    @case('popup')
                        <x-heroicon-o-window class="w-5 h-5 text-purple-400" />
                        <span>Popup</span>
                        @break
                    @case('order_bump')
                        <x-heroicon-o-shopping-cart class="w-5 h-5 text-amber-400" />
                        <span>Order Bump</span>
                        @break
                    @case('trust_badges')
                        <x-heroicon-o-shield-check class="w-5 h-5 text-green-400" />
                        <span>Trust Badges</span>
                        @break
                    @case('social_icons')
                        <x-heroicon-o-share class="w-5 h-5 text-blue-400" />
                        <span>Réseaux Sociaux</span>
                        @break
                    @case('icon_box')
                        <x-heroicon-o-squares-2x2 class="w-5 h-5 text-indigo-400" />
                        <span>Icône + Texte</span>
                        @break
                    @case('progress')
                        <x-heroicon-o-chart-bar class="w-5 h-5 text-blue-400" />
                        <span>Barre de Progression</span>
                        @break
                    @case('audio')
                        <x-heroicon-o-musical-note class="w-5 h-5 text-pink-400" />
                        <span>Audio</span>
                        @break
                    @case('embed')
                        <x-heroicon-o-code-bracket class="w-5 h-5 text-gray-400" />
                        <span>Code Embed</span>
                        @break
                    @case('sticky_bar')
                        <x-heroicon-o-bars-3 class="w-5 h-5 text-red-400" />
                        <span>Barre Fixe</span>
                        @break
                    @case('section')
                        <x-heroicon-o-rectangle-group class="w-5 h-5 text-indigo-400" />
                        <span>Section</span>
                        @break
                    @case('columns')
                        <x-heroicon-o-view-columns class="w-5 h-5 text-indigo-400" />
                        <span>Colonnes</span>
                        @break
                    @default
                        <x-heroicon-o-square-3-stack-3d class="w-5 h-5 text-blue-400" />
                        <span>{{ ucfirst($blockType) }}</span>
                @endswitch
            </h2>
            <p class="text-xs text-gray-400 mt-0.5">ID: #{{ $block->id }}</p>
        </div>
        <button wire:click="$parent.deselectBlock" 
                class="p-2 hover:bg-gray-700 rounded-lg transition">
            <x-heroicon-o-x-mark class="w-5 h-5" />
        </button>
    </div>

    {{-- Quick Actions Bar --}}
    <div class="flex gap-1 p-2 bg-gray-900/50 border-b border-gray-700 shrink-0">
        <button type="button" wire:click="$parent.deselectBlock" 
                class="flex-1 px-3 py-1.5 text-xs bg-blue-600 hover:bg-blue-700 rounded transition flex items-center justify-center gap-1"
                title="Valider et fermer l'éditeur">
            <x-heroicon-o-check class="w-3 h-3" />
            OK
        </button>
        <button type="button" wire:click="undoChanges" 
                class="flex-1 px-3 py-1.5 text-xs bg-gray-700 hover:bg-gray-600 rounded transition flex items-center justify-center gap-1 text-gray-300"
                title="Annuler les modifications">
            <x-heroicon-o-arrow-uturn-left class="w-3 h-3" />
            Annuler
        </button>
        <button type="button" wire:click="$parent.duplicateBlock({{ $block->id }})" 
                class="px-3 py-1.5 text-xs bg-gray-700 hover:bg-gray-600 rounded transition" title="Dupliquer le bloc en dessous">
            <x-heroicon-o-document-duplicate class="w-4 h-4" />
        </button>
        <button type="button" wire:click="$parent.deleteBlock({{ $block->id }})" 
                class="px-3 py-1.5 text-xs bg-red-600/20 hover:bg-red-600/40 text-red-400 rounded transition" 
                onclick="return confirm('Supprimer ce bloc ?')" title="Supprimer totalement">
            <x-heroicon-o-trash class="w-4 h-4" />
        </button>
    </div>

    {{-- Scrollable Content --}}
    <div class="flex-1 overflow-y-auto builder-scrollbar p-4">

        {{-- Content Section --}}
        <div x-data="{ contentOpen: true }" class="mb-4">
            <button @click="contentOpen = !contentOpen" 
                    class="w-full flex items-center justify-between p-3 bg-gray-700 hover:bg-gray-600 rounded-lg transition mb-2">
                <span class="font-medium flex items-center gap-2">
                    <x-heroicon-o-pencil class="w-4 h-4 text-blue-400" />
                    Contenu
                </span>
                <x-heroicon-o-chevron-down class="w-4 h-4 transition-transform" x-bind:class="{ 'rotate-180': contentOpen }" />
            </button>
            
            <div x-show="contentOpen" x-collapse class="space-y-4">
                @if(file_exists(resource_path("views/livewire/page-builder/editors/{$blockType}.blade.php")))
                    @include("livewire.page-builder.editors.{$blockType}")
                @else
                    @switch($blockType)
                        @case('title')
                            @include('livewire.page-builder.editors.title')
                            @break
                        @case('text')
                            @include('livewire.page-builder.editors.text')
                            @break
                        @case('button')
                            @include('livewire.page-builder.editors.button')
                            @break
                        @case('image')
                            @include('livewire.page-builder.editors.image')
                            @break
                        @case('video')
                            @include('livewire.page-builder.editors.video')
                            @break
                        @case('countdown')
                            @include('livewire.page-builder.editors.countdown')
                            @break
                        @case('form')
                            @include('livewire.page-builder.editors.form')
                            @break
                        @case('features')
                            @include('livewire.page-builder.editors.features')
                            @break
                        @case('hero')
                            @include('livewire.page-builder.editors.hero')
                            @break
                        @case('text_image')
                            @include('livewire.page-builder.editors.text_image')
                            @break
                        @case('spacer')
                            @include('livewire.page-builder.editors.spacer')
                            @break
                        @case('divider')
                            @include('livewire.page-builder.editors.divider')
                            @break
                        @case('testimonial')
                            @include('livewire.page-builder.editors.testimonial')
                            @break
                        @default
                            <div class="text-center py-8 text-gray-400">
                                <p>Éditeur non disponible pour ce type de bloc</p>
                            </div>
                    @endswitch
                @endif
            </div>
        </div>

        {{-- Design Section --}}
        <div x-data="{ designOpen: false }" class="mb-4">
            <button @click="designOpen = !designOpen" 
                    class="w-full flex items-center justify-between p-3 bg-gray-700 hover:bg-gray-600 rounded-lg transition mb-2">
                <span class="font-medium flex items-center gap-2">
                    <x-heroicon-o-paint-brush class="w-4 h-4 text-purple-400" />
                    Design & Style
                </span>
                <x-heroicon-o-chevron-down class="w-4 h-4 transition-transform" x-bind:class="{ 'rotate-180': designOpen }" />
            </button>
            
            <div x-show="designOpen" x-collapse x-data="{ activeTab: 'colors' }" class="space-y-3">
                {{-- Design Tabs --}}
                <div class="flex gap-1 bg-gray-900/50 p-1 rounded-lg">
                    <button @click="activeTab = 'colors'" 
                            x-bind:class="activeTab === 'colors' ? 'bg-blue-600' : 'hover:bg-gray-700'"
                            class="flex-1 px-2 py-1.5 text-xs rounded transition">
                        Couleurs
                    </button>
                    <button @click="activeTab = 'spacing'" 
                            x-bind:class="activeTab === 'spacing' ? 'bg-blue-600' : 'hover:bg-gray-700'"
                            class="flex-1 px-2 py-1.5 text-xs rounded transition">
                        Espacement
                    </button>
                    <button @click="activeTab = 'effects'" 
                            x-bind:class="activeTab === 'effects' ? 'bg-blue-600' : 'hover:bg-gray-700'"
                            class="flex-1 px-2 py-1.5 text-xs rounded transition">
                        Effets
                    </button>
                </div>

                {{-- Colors Tab --}}
                <div x-show="activeTab === 'colors'" class="space-y-3">
                    {{-- Color Presets for Sales Pages --}}
                    @if(in_array($blockType, ['button', 'title', 'text']))
                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-2">Presets conversion</label>
                            <div class="grid grid-cols-3 gap-2">
                                {{-- Urgence (Rouge) --}}
                                <button type="button" 
                                        wire:click="$set('styles.backgroundColor', '#EF4444'); $set('styles.color', '#FFFFFF')" 
                                        class="h-10 rounded bg-red-500 hover:ring-2 ring-white transition" 
                                        title="Urgence">
                                </button>
                                {{-- Confiance (Bleu) --}}
                                <button type="button" 
                                        wire:click="$set('styles.backgroundColor', '#3B82F6'); $set('styles.color', '#FFFFFF')" 
                                        class="h-10 rounded bg-blue-500 hover:ring-2 ring-white transition" 
                                        title="Confiance">
                                </button>
                                {{-- Succès (Vert) --}}
                                <button type="button" 
                                        wire:click="$set('styles.backgroundColor', '#10B981'); $set('styles.color', '#FFFFFF')" 
                                        class="h-10 rounded bg-green-500 hover:ring-2 ring-white transition" 
                                        title="Succès">
                                </button>
                                {{-- Premium (Or) --}}
                                <button type="button" 
                                        wire:click="$set('styles.backgroundColor', '#F59E0B'); $set('styles.color', '#000000')" 
                                        class="h-10 rounded bg-amber-500 hover:ring-2 ring-white transition" 
                                        title="Premium">
                                </button>
                                {{-- Attention (Orange) --}}
                                <button type="button" 
                                        wire:click="$set('styles.backgroundColor', '#F97316'); $set('styles.color', '#FFFFFF')" 
                                        class="h-10 rounded bg-orange-500 hover:ring-2 ring-white transition" 
                                        title="Attention">
                                </button>
                                {{-- Sombre (Noir) --}}
                                <button type="button" 
                                        wire:click="$set('styles.backgroundColor', '#1F2937'); $set('styles.color', '#FFFFFF')" 
                                        class="h-10 rounded bg-gray-800 hover:ring-2 ring-white transition" 
                                        title="Sombre">
                                </button>
                            </div>
                        </div>
                    @endif
                    @if(in_array($blockType, ['title', 'text', 'button']))
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Couleur du texte</label>
                            <div class="flex gap-2">
                                <input type="color"
                                       wire:model.live="styles.color"
                                       class="w-12 h-10 rounded cursor-pointer border border-gray-600">
                                <input type="text"
                                       wire:model.live.debounce.300ms="styles.color"
                                       placeholder="#FFFFFF"
                                       class="flex-1 px-3 py-1.5 bg-gray-700 border border-gray-600 rounded text-sm">
                            </div>
                        </div>
                    @endif

                    @if(in_array($blockType, ['button', 'form']))
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Couleur de fond</label>
                            <div class="flex gap-2">
                                <input type="color"
                                       wire:model.live="styles.backgroundColor"
                                       class="w-12 h-10 rounded cursor-pointer border border-gray-600">
                                <input type="text"
                                       wire:model.live.debounce.300ms="styles.backgroundColor"
                                       placeholder="#3B82F6"
                                       class="flex-1 px-3 py-1.5 bg-gray-700 border border-gray-600 rounded text-sm">
                            </div>
                        </div>
                    @endif

            @if(in_array($blockType, ['title', 'text', 'button']))
                <div>
                    <label class="block text-xs text-gray-400 mb-1">Police de caractères</label>
                    <select wire:model.live="styles.fontFamily" 
                            class="w-full px-2 py-1 bg-gray-700 border border-gray-600 rounded text-sm">
                            class="w-full px-2 py-1.5 bg-gray-700 border border-gray-600 rounded text-sm">
                        <option value="">Par défaut</option>
                        <option value="'Inter', sans-serif">Inter (Moderne)</option>
                        <option value="'Poppins', sans-serif">Poppins (Arrondie)</option>
                        <option value="'Outfit', sans-serif">Outfit (Élégante)</option>
                        <option value="'Georgia', serif">Georgia (Serif)</option>
                        <option value="'Courier New', monospace">Courier (Monospace)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-400 mb-1">Taille de police (px, rem, etc.)</label>
                    <input type="text"
                           wire:model.live.debounce.300ms="styles.fontSize"
                           placeholder="Ex: 16px ou 1.5rem"
                           class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-xs text-gray-400 mb-1">Épaisseur de police</label>
                    <select wire:model.live="styles.fontWeight" 
                            class="w-full px-2 py-1.5 bg-gray-700 border border-gray-600 rounded text-sm">
                        <option value="300">Light (300)</option>
                        <option value="400">Normal (400)</option>
                        <option value="500">Medium (500)</option>
                        <option value="600">Semi-Bold (600)</option>
                        <option value="700">Bold (700)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-400 mb-1">Espacement des lettres</label>
                    <input type="text"
                           wire:model.live.debounce.300ms="styles.letterSpacing"
                           placeholder="Ex: 1px ou 0.1em"
                           class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-sm">
                </div>
            @endif

            @if(in_array($blockType, ['image', 'video', 'form']))
                <div>
                    <label class="block text-xs text-gray-400 mb-1">Largeur max</label>
                    <input type="text"
                           wire:model.live.debounce.300ms="styles.maxWidth"
                           placeholder="800px"
                           class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-sm">
                </div>
            @endif

            <div>
                <label class="block text-xs text-gray-400 mb-1">Arrondi des coins (Border radius)</label>
                <input type="text"
                       wire:model.live.debounce.300ms="styles.borderRadius"
                       placeholder="Ex: 8px, 1rem, 50%"
                       class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-sm">
            </div>
                </div>

                {{-- Spacing Tab --}}
                <div x-show="activeTab === 'spacing'" class="space-y-3">
                    <div class="p-2 bg-blue-500/10 border border-blue-500/20 rounded text-xs text-blue-300">
                        💡 L'espacement améliore la lisibilité et la conversion
                    </div>
            <div>
                <label class="block text-xs text-gray-400 mb-1">Padding (Intérieur)</label>
                <div class="grid grid-cols-2 gap-2">
                    <input type="text" wire:model.live.debounce.300ms="styles.paddingTop" 
                           placeholder="Top" class="px-2 py-1 bg-gray-700 border border-gray-600 rounded text-sm">
                    <input type="text" wire:model.live.debounce.300ms="styles.paddingRight" 
                           placeholder="Right" class="px-2 py-1 bg-gray-700 border border-gray-600 rounded text-sm">
                    <input type="text" wire:model.live.debounce.300ms="styles.paddingBottom" 
                           placeholder="Bottom" class="px-2 py-1 bg-gray-700 border border-gray-600 rounded text-sm">
                    <input type="text" wire:model.live.debounce.300ms="styles.paddingLeft" 
                           placeholder="Left" class="px-2 py-1 bg-gray-700 border border-gray-600 rounded text-sm">
                </div>
            </div>
            <div>
                <label class="block text-xs text-gray-400 mb-1">Margin (Extérieur)</label>
                <div class="grid grid-cols-2 gap-2">
                    <input type="text" wire:model.live.debounce.300ms="styles.marginTop" 
                           placeholder="Top" class="px-2 py-1 bg-gray-700 border border-gray-600 rounded text-sm">
                    <input type="text" wire:model.live.debounce.300ms="styles.marginRight" 
                           placeholder="Right" class="px-2 py-1 bg-gray-700 border border-gray-600 rounded text-sm">
                    <input type="text" wire:model.live.debounce.300ms="styles.marginBottom" 
                           placeholder="Bottom" class="px-2 py-1 bg-gray-700 border border-gray-600 rounded text-sm">
                    <input type="text" wire:model.live.debounce.300ms="styles.marginLeft" 
                           placeholder="Left" class="px-2 py-1 bg-gray-700 border border-gray-600 rounded text-sm">
                </div>
            </div>
                </div>

                {{-- Effects Tab --}}
                <div x-show="activeTab === 'effects'" class="space-y-3">
            <div>
                <label class="block text-xs text-gray-400 mb-1">Ombre (Box Shadow)</label>
                <select wire:model.live="styles.boxShadow" 
                        class="w-full px-2 py-1 bg-gray-700 border border-gray-600 rounded text-sm">
                    <option value="">Aucune</option>
                    <option value="0 1px 2px 0 rgba(0, 0, 0, 0.05)">Petite</option>
                    <option value="0 4px 6px -1px rgba(0, 0, 0, 0.1)">Moyenne</option>
                    <option value="0 10px 15px -3px rgba(0, 0, 0, 0.1)">Grande</option>
                    <option value="0 20px 25px -5px rgba(0, 0, 0, 0.1)">Extra large</option>
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-400 mb-1">Opacité</label>
                <div class="flex gap-2 items-center">
                    <input type="range" 
                           wire:model.live="styles.opacity" 
                           min="0" max="1" step="0.1" 
                           class="flex-1">
                    <span class="text-sm text-gray-400 w-12">{{ $styles['opacity'] ?? '1' }}</span>
                </div>
            </div>
            <div>
                <label class="block text-xs text-gray-400 mb-1">Bordure</label>
                <div class="space-y-2">
                    <input type="text" 
                           wire:model.live.debounce.300ms="styles.borderWidth" 
                           placeholder="1px"
                           class="w-full px-2 py-1 bg-gray-700 border border-gray-600 rounded text-sm">
                    <div class="flex gap-2">
                        <input type="color" 
                               wire:model.live="styles.borderColor" 
                               class="w-10 h-10 rounded cursor-pointer bg-transparent border-0">
                        <input type="text" 
                               wire:model.live.debounce.300ms="styles.borderColor" 
                               placeholder="#000000"
                               class="flex-1 px-2 py-1 bg-gray-700 border border-gray-600 rounded text-sm">
                    </div>
                    <select wire:model.live="styles.borderStyle" 
                            class="w-full px-2 py-1 bg-gray-700 border border-gray-600 rounded text-sm">
                        <option value="solid">Solide</option>
                        <option value="dashed">Pointillés</option>
                        <option value="dotted">Points</option>
                        <option value="double">Double</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs text-gray-400 mb-1">Animation</label>
                <select wire:model.live="styles.animation" 
                        class="w-full px-2 py-1 bg-gray-700 border border-gray-600 rounded text-sm">
                    <option value="">Aucune</option>
                    <option value="fade-in">Fade In</option>
                    <option value="slide-up">Slide Up</option>
                    <option value="zoom-in">Zoom In</option>
                    <option value="bounce">Bounce</option>
                </select>
            </div>
        </div>

        {{-- Master Block Section --}}
        <div x-data="{ masterOpen: false }" class="mb-4">
            <button @click="masterOpen = !masterOpen" 
                    class="w-full flex items-center justify-between p-3 bg-gray-700 hover:bg-gray-600 rounded-lg transition mb-2">
                <span class="font-medium flex items-center gap-2">
                    <x-heroicon-o-cube class="w-4 h-4 text-purple-400" />
                    Master Block
                    @if($block->is_master_block ?? false)
                        <span class="text-xs bg-purple-500/30 text-purple-400 px-2 py-0.5 rounded-full">Master</span>
                    @elseif($block->master_block_id ?? false)
                        <span class="text-xs bg-blue-500/30 text-blue-400 px-2 py-0.5 rounded-full">Lié</span>
                    @endif
                </span>
                <x-heroicon-o-chevron-down class="w-4 h-4 transition-transform" x-bind:class="{ 'rotate-180': masterOpen }" />
            </button>
            
            <div x-show="masterOpen" x-collapse>
                @include('livewire.page-builder.partials.master-block-panel', ['block' => $block])
            </div>
        </div>

        {{-- Display Conditions Section --}}
        <div x-data="{ conditionsOpen: false }" class="mb-4">
            <button @click="conditionsOpen = !conditionsOpen" 
                    class="w-full flex items-center justify-between p-3 bg-gray-700 hover:bg-gray-600 rounded-lg transition mb-2">
                <span class="font-medium flex items-center gap-2">
                    <x-heroicon-o-funnel class="w-4 h-4 text-amber-400" />
                    Conditions d'affichage
                    @php
                        $conditionsCount = count($block->display_conditions['rules'] ?? []);
                    @endphp
                    @if($conditionsCount > 0)
                        <span class="text-xs bg-amber-500/30 text-amber-400 px-2 py-0.5 rounded-full">{{ $conditionsCount }}</span>
                    @endif
                </span>
                <x-heroicon-o-chevron-down class="w-4 h-4 transition-transform" x-bind:class="{ 'rotate-180': conditionsOpen }" />
            </button>
            
            <div x-show="conditionsOpen" x-collapse>
                @include('livewire.page-builder.partials.display-conditions-panel', ['block' => $block])
            </div>
        </div>

        {{-- Visibility Section --}}
        <div x-data="{ visibilityOpen: false }" class="mb-4">
            <button @click="visibilityOpen = !visibilityOpen" 
                    class="w-full flex items-center justify-between p-3 bg-gray-700 hover:bg-gray-600 rounded-lg transition mb-2">
                <span class="font-medium flex items-center gap-2">
                    <x-heroicon-o-eye class="w-4 h-4 text-green-400" />
                    Visibilité
                </span>
                <x-heroicon-o-chevron-down class="w-4 h-4 transition-transform" x-bind:class="{ 'rotate-180': visibilityOpen }" />
            </button>
            
            <div x-show="visibilityOpen" x-collapse class="space-y-3 p-4 bg-gray-800 rounded-lg">
                {{-- Device Visibility --}}
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-300 flex items-center gap-2">
                        <x-heroicon-o-computer-desktop class="w-4 h-4" />
                        Afficher sur Desktop
                    </span>
                    <button wire:click="$parent.toggleBlockVisibility({{ $block->id }}, 'desktop')"
                            class="relative w-10 h-5 rounded-full transition {{ ($block->show_on_desktop ?? true) ? 'bg-green-600' : 'bg-gray-600' }}">
                        <span class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full transition-transform {{ ($block->show_on_desktop ?? true) ? 'translate-x-5' : '' }}"></span>
                    </button>
                </div>
                
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-300 flex items-center gap-2">
                        <x-heroicon-o-device-phone-mobile class="w-4 h-4" />
                        Afficher sur Mobile
                    </span>
                    <button wire:click="$parent.toggleBlockVisibility({{ $block->id }}, 'mobile')"
                            class="relative w-10 h-5 rounded-full transition {{ ($block->show_on_mobile ?? true) ? 'bg-green-600' : 'bg-gray-600' }}">
                        <span class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full transition-transform {{ ($block->show_on_mobile ?? true) ? 'translate-x-5' : '' }}"></span>
                    </button>
                </div>

                {{-- Active Status --}}
                <div class="flex items-center justify-between pt-2 border-t border-gray-700">
                    <span class="text-sm text-gray-300 flex items-center gap-2">
                        <x-heroicon-o-bolt class="w-4 h-4" />
                        Bloc actif
                    </span>
                    <span class="px-2 py-0.5 text-xs rounded-full {{ ($block->is_active ?? true) ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                        {{ ($block->is_active ?? true) ? 'Actif' : 'Inactif' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Pro Tips Section --}}
        <div class="mt-4 p-3 bg-gradient-to-r from-blue-500/10 to-purple-500/10 border border-blue-500/20 rounded-lg">
            <div class="flex items-start gap-2">
                <span class="text-lg">💎</span>
                <div class="text-xs text-gray-300">
                    <strong class="text-blue-400">Astuce Pro:</strong>
                    @switch($blockType)
                        @case('title')
                            Un bon titre génère 80% de l'impact. Testez plusieurs variantes avec des chiffres et bénéfices concrets.
                            @break
                        @case('button')
                            Les CTA avec verbes d'action à la 1ère personne ("Je veux", "J'accède") convertissent jusqu'à 90% mieux.
                            @break
                        @case('text')
                            Utilisez des paragraphes courts, des listes à puces et mettez en gras les bénéfices clés.
                            @break
                        @case('countdown')
                            L'urgence augmente les conversions de 30-40%. Soyez précis sur ce qui se passe après l'expiration.
                            @break
                        @default
                            Gardez votre design simple et focalisé sur l'action principale que vous voulez que vos visiteurs prennent.
                    @endswitch
                </div>
            </div>
        </div>
    </div>
</div>
