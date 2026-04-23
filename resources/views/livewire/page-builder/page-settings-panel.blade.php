<div class="h-full flex flex-col bg-gray-800">
    {{-- Header --}}
    <div class="p-4 border-b border-gray-700">
        <div class="flex items-center justify-between mb-2">
            <h2 class="font-semibold flex items-center gap-2">
                <x-heroicon-o-cog-6-tooth class="w-5 h-5 text-purple-400" />
                Paramètres de la page
            </h2>
            <button wire:click="$toggle('showSettingsPanel')" class="p-1 hover:bg-gray-700 rounded">
                <x-heroicon-o-x-mark class="w-5 h-5" />
            </button>
        </div>
        <button wire:click="savePageSettings" 
                class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
            <span class="flex items-center justify-center gap-2">
                <x-heroicon-o-check class="w-4 h-4" />
                Sauvegarder les paramètres
            </span>
        </button>
    </div>

    {{-- Content --}}
    <div class="flex-1 overflow-y-auto builder-scrollbar p-4 space-y-4">
        
        {{-- Design Global --}}
        <div x-data="{ open: true }" class="border border-gray-700 rounded-lg overflow-hidden">
            <button @click="open = !open" 
                    class="w-full p-3 bg-gray-700 hover:bg-gray-600 flex items-center justify-between transition">
                <span class="font-medium flex items-center gap-2">
                    <x-heroicon-o-paint-brush class="w-4 h-4 text-blue-400" />
                    Design global
                </span>
                <x-heroicon-o-chevron-down class="w-4 h-4 transition-transform" x-bind:class="{ 'rotate-180': open }" />
            </button>
            <div x-show="open" x-collapse class="p-4 space-y-3 bg-gray-800">
                {{-- Police par défaut --}}
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">Police par défaut</label>
                    <select wire:model.live="pageSettings.defaultFont" 
                            class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-sm">
                        <option value="Inter">Inter (Moderne)</option>
                        <option value="Poppins">Poppins (Arrondie)</option>
                        <option value="Outfit">Outfit (Élégante)</option>
                        <option value="Georgia">Georgia (Serif)</option>
                        <option value="Arial">Arial (Classique)</option>
                    </select>
                </div>

                {{-- Taille de police --}}
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">Taille de police (px)</label>
                    <input type="number" wire:model.live.debounce.300ms="pageSettings.defaultFontSize" 
                           min="12" max="24" placeholder="16"
                           class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-sm">
                </div>

                {{-- Hauteur de ligne --}}
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">Hauteur de ligne (px)</label>
                    <input type="number" wire:model.live.debounce.300ms="pageSettings.defaultLineHeight" 
                           min="16" max="40" placeholder="24"
                           class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-sm">
                </div>

                {{-- Couleur du texte --}}
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">Couleur du texte</label>
                    <div class="flex gap-2">
                        <input type="color" wire:model.live="pageSettings.textColor" 
                               class="w-12 h-10 rounded cursor-pointer border border-gray-600">
                        <input type="text" wire:model.live.debounce.300ms="pageSettings.textColor" 
                               placeholder="#FFFFFF"
                               class="flex-1 px-3 py-2 bg-gray-700 border border-gray-600 rounded text-sm">
                    </div>
                </div>

                {{-- Couleur des liens --}}
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">Couleur des liens</label>
                    <div class="flex gap-2">
                        <input type="color" wire:model.live="pageSettings.linkColor" 
                               class="w-12 h-10 rounded cursor-pointer border border-gray-600">
                        <input type="text" wire:model.live.debounce.300ms="pageSettings.linkColor" 
                               placeholder="#3B82F6"
                               class="flex-1 px-3 py-2 bg-gray-700 border border-gray-600 rounded text-sm">
                    </div>
                </div>

                {{-- Alignement par défaut --}}
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">Alignement</label>
                    <select wire:model.live="pageSettings.defaultAlignment" 
                            class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-sm">
                        <option value="left">Gauche</option>
                        <option value="center">Centre</option>
                        <option value="right">Droite</option>
                        <option value="justify">Justifié</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Polices des titres --}}
        <div x-data="{ open: false }" class="border border-gray-700 rounded-lg overflow-hidden">
            <button @click="open = !open" 
                    class="w-full p-3 bg-gray-700 hover:bg-gray-600 flex items-center justify-between transition">
                <span class="font-medium flex items-center gap-2">
                    <x-heroicon-o-document-text class="w-4 h-4 text-green-400" />
                    Polices des titres
                </span>
                <x-heroicon-o-chevron-down class="w-4 h-4 transition-transform" x-bind:class="{ 'rotate-180': open }" />
            </button>
            <div x-show="open" x-collapse class="p-4 space-y-3 bg-gray-800">
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">Type de police</label>
                    <select wire:model.live="pageSettings.headingFont" 
                            class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-sm">
                        <option value="same">Même police que la page</option>
                        <option value="Inter">Inter</option>
                        <option value="Poppins">Poppins</option>
                        <option value="Outfit">Outfit</option>
                        <option value="Georgia">Georgia</option>
                        <option value="Arial">Arial</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">Couleur du texte</label>
                    <div class="flex gap-2">
                        <input type="color" wire:model.live="pageSettings.headingColor" 
                               class="w-12 h-10 rounded cursor-pointer border border-gray-600">
                        <input type="text" wire:model.live.debounce.300ms="pageSettings.headingColor" 
                               placeholder="#FFFFFF"
                               class="flex-1 px-3 py-2 bg-gray-700 border border-gray-600 rounded text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">Alignement</label>
                    <select wire:model.live="pageSettings.headingAlignment" 
                            class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-sm">
                        <option value="left">Gauche</option>
                        <option value="center">Centre</option>
                        <option value="right">Droite</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Langue --}}
        <div x-data="{ open: false }" class="border border-gray-700 rounded-lg overflow-hidden">
            <button @click="open = !open" 
                    class="w-full p-3 bg-gray-700 hover:bg-gray-600 flex items-center justify-between transition">
                <span class="font-medium flex items-center gap-2">
                    <x-heroicon-o-language class="w-4 h-4 text-yellow-400" />
                    Langue de la page
                </span>
                <x-heroicon-o-chevron-down class="w-4 h-4 transition-transform" x-bind:class="{ 'rotate-180': open }" />
            </button>
            <div x-show="open" x-collapse class="p-4 space-y-3 bg-gray-800">
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">Choisir la langue</label>
                    <select wire:model.live="pageSettings.language" 
                            class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-sm">
                        <option value="fr">Français</option>
                        <option value="en">English</option>
                        <option value="es">Español</option>
                        <option value="de">Deutsch</option>
                        <option value="it">Italiano</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Arrière-fond --}}
        <div x-data="{ open: false }" class="border border-gray-700 rounded-lg overflow-hidden">
            <button @click="open = !open" 
                    class="w-full p-3 bg-gray-700 hover:bg-gray-600 flex items-center justify-between transition">
                <span class="font-medium flex items-center gap-2">
                    <x-heroicon-o-photo class="w-4 h-4 text-purple-400" />
                    Arrière-fond
                </span>
                <x-heroicon-o-chevron-down class="w-4 h-4 transition-transform" x-bind:class="{ 'rotate-180': open }" />
            </button>
            <div x-show="open" x-collapse class="p-4 space-y-3 bg-gray-800">
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">Couleur de l'arrière-plan</label>
                    <div class="flex gap-2">
                        <input type="color" wire:model.live="pageSettings.backgroundColor" 
                               class="w-12 h-10 rounded cursor-pointer border border-gray-600">
                        <input type="text" wire:model.live.debounce.300ms="pageSettings.backgroundColor" 
                               placeholder="#0F172A"
                               class="flex-1 px-3 py-2 bg-gray-700 border border-gray-600 rounded text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">Image de fond (URL)</label>
                    <input type="text" wire:model.live.debounce.300ms="pageSettings.backgroundImage" 
                           placeholder="https://..."
                           class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-sm">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">Floutage (%)</label>
                    <input type="range" wire:model.live="pageSettings.backgroundBlur" 
                           min="0" max="100" step="5"
                           class="w-full">
                    <div class="text-xs text-gray-500 mt-1">{{ $pageSettings['backgroundBlur'] ?? 0 }}%</div>
                </div>
            </div>
        </div>

        {{-- SEO --}}
        <div x-data="{ open: false }" class="border border-gray-700 rounded-lg overflow-hidden">
            <button @click="open = !open" 
                    class="w-full p-3 bg-gray-700 hover:bg-gray-600 flex items-center justify-between transition">
                <span class="font-medium flex items-center gap-2">
                    <x-heroicon-o-magnifying-glass class="w-4 h-4 text-red-400" />
                    SEO & Métadonnées
                </span>
                <x-heroicon-o-chevron-down class="w-4 h-4 transition-transform" x-bind:class="{ 'rotate-180': open }" />
            </button>
            <div x-show="open" x-collapse class="p-4 space-y-3 bg-gray-800">
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">Titre SEO</label>
                    <input type="text" wire:model.live.debounce.300ms="pageSettings.seoTitle" 
                           placeholder="Titre de la page pour les moteurs de recherche"
                           class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-sm">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">Description</label>
                    <textarea wire:model.live.debounce.300ms="pageSettings.seoDescription" 
                              rows="3" placeholder="Description pour les moteurs de recherche..."
                              class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-sm"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">Mots-clés (séparés par des virgules)</label>
                    <input type="text" wire:model.live.debounce.300ms="pageSettings.seoKeywords" 
                           placeholder="mot1, mot2, mot3"
                           class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-sm">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">Auteur</label>
                    <input type="text" wire:model.live.debounce.300ms="pageSettings.seoAuthor" 
                           placeholder="Nom de l'auteur"
                           class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-sm">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">Image pour les réseaux sociaux (URL)</label>
                    <input type="text" wire:model.live.debounce.300ms="pageSettings.seoImage" 
                           placeholder="https://..."
                           class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-sm">
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" wire:model.live="pageSettings.hideFromSearch" 
                           id="hideFromSearch" class="rounded bg-gray-700 border-gray-600">
                    <label for="hideFromSearch" class="text-sm text-gray-300">Masquer des moteurs de recherche</label>
                </div>
            </div>
        </div>

        {{-- Tracking --}}
        <div x-data="{ open: false }" class="border border-gray-700 rounded-lg overflow-hidden">
            <button @click="open = !open" 
                    class="w-full p-3 bg-gray-700 hover:bg-gray-600 flex items-center justify-between transition">
                <span class="font-medium flex items-center gap-2">
                    <x-heroicon-o-chart-bar class="w-4 h-4 text-indigo-400" />
                    Tracking & Analytics
                </span>
                <x-heroicon-o-chevron-down class="w-4 h-4 transition-transform" x-bind:class="{ 'rotate-180': open }" />
            </button>
            <div x-show="open" x-collapse class="p-4 space-y-3 bg-gray-800">
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">Événement Facebook Pixel</label>
                    <select wire:model.live="pageSettings.facebookEvent" 
                            class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-sm">
                        <option value="">Aucun</option>
                        <option value="PageView">Page View</option>
                        <option value="Lead">Prospect (Lead)</option>
                        <option value="CompleteRegistration">Inscription complète</option>
                        <option value="Purchase">Achat</option>
                        <option value="AddToCart">Ajout au panier</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">Google Analytics ID</label>
                    <input type="text" wire:model.live.debounce.300ms="pageSettings.googleAnalyticsId" 
                           placeholder="G-XXXXXXXXXX"
                           class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-sm">
                </div>
            </div>
        </div>

        {{-- Code personnalisé --}}
        <div x-data="{ open: false }" class="border border-gray-700 rounded-lg overflow-hidden">
            <button @click="open = !open" 
                    class="w-full p-3 bg-gray-700 hover:bg-gray-600 flex items-center justify-between transition">
                <span class="font-medium flex items-center gap-2">
                    <x-heroicon-o-code-bracket class="w-4 h-4 text-orange-400" />
                    Code personnalisé
                </span>
                <x-heroicon-o-chevron-down class="w-4 h-4 transition-transform" x-bind:class="{ 'rotate-180': open }" />
            </button>
            <div x-show="open" x-collapse class="p-4 space-y-3 bg-gray-800">
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">Code d'en-tête (avant &lt;/head&gt;)</label>
                    <textarea wire:model.live.debounce.300ms="pageSettings.headerCode" 
                              rows="4" placeholder="<script>...</script>"
                              class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-sm font-mono text-xs"></textarea>
                    <p class="text-xs text-gray-500 mt-1">Scripts de tracking, métadonnées personnalisées...</p>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">Code de pied de page (avant &lt;/body&gt;)</label>
                    <textarea wire:model.live.debounce.300ms="pageSettings.footerCode" 
                              rows="4" placeholder="<script>...</script>"
                              class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-sm font-mono text-xs"></textarea>
                    <p class="text-xs text-gray-500 mt-1">Scripts analytics, chat widgets...</p>
                </div>
            </div>
        </div>

    </div>
</div>
