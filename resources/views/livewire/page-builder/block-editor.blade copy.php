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
        <button type="button" wire:click="saveContent" 
                class="flex-1 px-3 py-1.5 text-xs bg-blue-600 hover:bg-blue-700 rounded transition flex items-center justify-center gap-1">
            <x-heroicon-o-check class="w-3 h-3" />
            Sauvegarder
        </button>
        <button type="button" wire:click="$parent.duplicateBlock({{ $block->id }})" 
                class="px-3 py-1.5 text-xs bg-gray-700 hover:bg-gray-600 rounded transition" title="Dupliquer">
            <x-heroicon-o-document-duplicate class="w-4 h-4" />
        </button>
        <button type="button" wire:click="$parent.deleteBlock({{ $block->id }})" 
                class="px-3 py-1.5 text-xs bg-red-600/20 hover:bg-red-600/40 text-red-400 rounded transition" 
                onclick="return confirm('Supprimer ce bloc ?')" title="Supprimer">
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
                @switch($blockType)
                    @case('title')
                        {{-- Presets Marketing --}}
                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-2">Templates de titre</label>
                            <div class="grid grid-cols-1 gap-1">
                                <button type="button" wire:click="$set('content.text', 'Découvrez Comment [Résultat] en Seulement [Temps]')" 
                                        class="px-3 py-2 text-xs bg-gray-700 hover:bg-gray-600 rounded text-left transition">
                                    "Découvrez Comment..."
                                </button>
                                <button type="button" wire:click="$set('content.text', '[Nombre] Secrets Pour [Résultat] Que Personne Ne Vous Dit')" 
                                        class="px-3 py-2 text-xs bg-gray-700 hover:bg-gray-600 rounded text-left transition">
                                    "[X] Secrets Pour..."
                                </button>
                                <button type="button" wire:click="$set('content.text', 'La Méthode Complète Pour [Résultat] Sans [Objection]')" 
                                        class="px-3 py-2 text-xs bg-gray-700 hover:bg-gray-600 rounded text-left transition">
                                    "La Méthode Complète..."
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Votre titre</label>
                            <input type="text"
                                   wire:model.live.debounce.500ms="content.text"
                                   placeholder="Un titre qui capte l'attention..."
                                   class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <p class="text-xs text-gray-400 mt-1">💡 Utilisez des chiffres, bénéfices et créez de la curiosité</p>
                        </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Niveau</label>
                    <select wire:model.live="content.level"
                            class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="h1">H1 - Titre principal</option>
                        <option value="h2">H2 - Sous-titre</option>
                        <option value="h3">H3 - Titre tertiaire</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Alignement</label>
                    <div class="flex gap-2">
                        @foreach(['left' => 'Gauche', 'center' => 'Centre', 'right' => 'Droite'] as $value => $label)
                            <button wire:click="$set('content.alignment', '{{ $value }}')"
                                    class="flex-1 py-2 px-3 text-sm rounded-lg transition {{ ($content['alignment'] ?? 'center') === $value ? 'bg-blue-600' : 'bg-gray-700 hover:bg-gray-600' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Style Options --}}
                <div class="space-y-3 p-3 bg-gray-900/50 rounded-lg border border-gray-700">
                    <label class="block text-xs font-medium text-gray-400">Options de style</label>
                    
                    <div class="flex items-center gap-2">
                        <input type="checkbox" wire:model.live="content.bordered_box" 
                               id="title-bordered-box" class="rounded bg-gray-700 border-gray-600">
                        <label for="title-bordered-box" class="text-sm text-gray-300">Bordure stylisée (box)</label>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" wire:model.live="content.gradient" 
                               id="title-gradient" class="rounded bg-gray-700 border-gray-600">
                        <label for="title-gradient" class="text-sm text-gray-300">Texte dégradé (gradient)</label>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" wire:model.live="content.underline" 
                               id="title-underline" class="rounded bg-gray-700 border-gray-600">
                        <label for="title-underline" class="text-sm text-gray-300">Soulignement</label>
                    </div>
                </div>
                @break

                    @case('text')
                        {{-- Copywriting Shortcuts --}}
                        <div class="mb-3">
                            <label class="block text-xs font-medium text-gray-400 mb-2">Éléments de persuasion</label>
                            <div class="flex flex-wrap gap-1">
                                <button type="button" 
                                        wire:click="$set('content.html', $get('content.html') . '<p><strong>✓ Garantie satisfait ou remboursé 30 jours</strong></p>')" 
                                        class="px-2 py-1 text-xs bg-green-600/20 hover:bg-green-600/40 text-green-300 rounded">
                                    + Garantie
                                </button>
                                <button type="button" 
                                        wire:click="$set('content.html', $get('content.html') . '<p><strong>⚡ Accès immédiat</strong></p>')" 
                                        class="px-2 py-1 text-xs bg-yellow-600/20 hover:bg-yellow-600/40 text-yellow-300 rounded">
                                    + Accès immédiat
                                </button>
                                <button type="button" 
                                        wire:click="$set('content.html', $get('content.html') . '<p>🎁 <em>Bonus exclusif inclus</em></p>')" 
                                        class="px-2 py-1 text-xs bg-purple-600/20 hover:bg-purple-600/40 text-purple-300 rounded">
                                    + Bonus
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Texte de vente</label>
                            <textarea wire:model.live.debounce.1000ms="content.html"
                                      rows="10"
                                      placeholder="<p>Votre texte persuasif...</p>"
                                      class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 font-mono text-sm"></textarea>
                            <div class="flex items-start gap-2 mt-2 p-2 bg-blue-500/10 border border-blue-500/20 rounded text-xs text-blue-300">
                                <span>💡</span>
                                <div>
                                    <strong>Astuce:</strong> Parlez des bénéfices, pas des caractéristiques. 
                                    Adressez les objections et créez l'urgence.
                                </div>
                            </div>
                        </div>

                        {{-- Style Options --}}
                        <div class="space-y-3 p-3 bg-gray-900/50 rounded-lg border border-gray-700">
                            <label class="block text-xs font-medium text-gray-400">Options de style</label>
                            
                            <div class="flex items-center gap-2">
                                <input type="checkbox" wire:model.live="content.bordered_box" 
                                       id="text-bordered-box" class="rounded bg-gray-700 border-gray-600">
                                <label for="text-bordered-box" class="text-sm text-gray-300">Bordure stylisée (box)</label>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-400 mb-1">Colonnes</label>
                                <select wire:model.live="content.columns" 
                                        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-sm">
                                    <option value="1">1 colonne</option>
                                    <option value="2">2 colonnes</option>
                                    <option value="3">3 colonnes</option>
                                </select>
                            </div>
                        </div>
                        @break

            @case('image')
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Image</label>
                    
                    @if(!empty($content['url']))
                        <div class="mb-3 relative group">
                            <img src="{{ $content['url'] }}" 
                                 class="w-full h-48 object-cover rounded-lg border-2 border-gray-600">
                            <button type="button" wire:click="$set('content.url', '')" 
                                    class="absolute top-2 right-2 p-2 bg-red-600 hover:bg-red-700 rounded-lg opacity-0 group-hover:opacity-100 transition">
                                <x-heroicon-o-trash class="w-4 h-4" />
                            </button>
                        </div>
                    @endif

                    <div class="space-y-2">
                        <input type="file"
                               wire:model="imageUpload"
                               accept="image/*"
                               class="w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:bg-blue-600 file:text-white hover:file:bg-blue-700">
                        <p class="text-xs text-gray-500">Ou entrez une URL :</p>
                        <input type="url"
                               wire:model.live.debounce.500ms="content.url"
                               placeholder="https://..."
                               class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Texte alternatif</label>
                    <input type="text"
                           wire:model.live.debounce.300ms="content.alt"
                           placeholder="Description de l'image"
                           class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                @break

            @case('video')
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">URL de la vidéo</label>
                    @if(!empty($content['url']))
                        <div class="mb-3 relative group">
                            <div class="aspect-video bg-gray-800 rounded-lg border-2 border-gray-600 flex items-center justify-center">
                                @if(str_contains($content['url'], 'youtube.com') || str_contains($content['url'], 'youtu.be'))
                                    <div class="text-center">
                                        <x-heroicon-o-video-camera class="w-12 h-12 mx-auto text-red-500 mb-2" />
                                        <p class="text-sm text-gray-400">Vidéo YouTube</p>
                                    </div>
                                @elseif(str_contains($content['url'], 'vimeo.com'))
                                    <div class="text-center">
                                        <x-heroicon-o-video-camera class="w-12 h-12 mx-auto text-blue-500 mb-2" />
                                        <p class="text-sm text-gray-400">Vidéo Vimeo</p>
                                    </div>
                                @else
                                    <div class="text-center">
                                        <x-heroicon-o-video-camera class="w-12 h-12 mx-auto text-gray-500 mb-2" />
                                        <p class="text-sm text-gray-400">Vidéo</p>
                                    </div>
                                @endif
                            </div>
                            <button type="button" wire:click="$set('content.url', '')" 
                                    class="absolute top-2 right-2 p-2 bg-red-600 hover:bg-red-700 rounded-lg opacity-0 group-hover:opacity-100 transition">
                                <x-heroicon-o-trash class="w-4 h-4" />
                            </button>
                        </div>
                    @endif
                    <input type="url"
                           wire:model.live.debounce.500ms="content.url"
                           placeholder="https://youtube.com/watch?v=... ou https://vimeo.com/..."
                           class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                    <p class="text-xs text-gray-500 mt-1">Supporte YouTube, Vimeo ou vidéo directe (MP4, WebM)</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Options de lecture</label>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox"
                                   wire:model.live="content.autoplay"
                                   class="w-4 h-4 rounded bg-gray-700 border-gray-600 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm">Lecture automatique</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox"
                                   wire:model.live="content.controls"
                                   class="w-4 h-4 rounded bg-gray-700 border-gray-600 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm">Afficher les contrôles</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox"
                                   wire:model.live="content.loop"
                                   class="w-4 h-4 rounded bg-gray-700 border-gray-600 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm">Lecture en boucle</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox"
                                   wire:model.live="content.muted"
                                   class="w-4 h-4 rounded bg-gray-700 border-gray-600 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm">Muet par défaut</span>
                        </label>
                    </div>
                </div>
                @break

                    @case('button')
                        {{-- CTA Templates --}}
                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-2">CTA à haute conversion</label>
                            <div class="grid grid-cols-1 gap-1">
                                <button type="button" wire:click="$set('content.text', 'JE VEUX MON ACCÈS MAINTENANT ➜')" 
                                        class="px-3 py-2 text-xs bg-green-600 hover:bg-green-700 rounded text-left transition font-bold">
                                    "JE VEUX MON ACCÈS MAINTENANT"
                                </button>
                                <button type="button" wire:click="$set('content.text', 'OUI, JE RÉSERVE MA PLACE 🔥')" 
                                        class="px-3 py-2 text-xs bg-orange-600 hover:bg-orange-700 rounded text-left transition font-bold">
                                    "OUI, JE RÉSERVE MA PLACE"
                                </button>
                                <button type="button" wire:click="$set('content.text', 'OBTENIR [RÉSULTAT] AUJOURD\'HUI')" 
                                        class="px-3 py-2 text-xs bg-blue-600 hover:bg-blue-700 rounded text-left transition font-bold">
                                    "OBTENIR [RÉSULTAT] AUJOURD'HUI"
                                </button>
                                <button type="button" wire:click="$set('content.text', 'COMMENCER GRATUITEMENT ✓')" 
                                        class="px-3 py-2 text-xs bg-gray-700 hover:bg-gray-600 rounded text-left transition">
                                    "COMMENCER GRATUITEMENT"
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Texte du bouton</label>
                            <input type="text"
                                   wire:model.live.debounce.500ms="content.text"
                                   placeholder="Votre appel à l'action..."
                                   class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <p class="text-xs text-gray-400 mt-1">💡 Utilisez l'action, l'urgence et le bénéfice immédiat</p>
                        </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Lien (URL)</label>
                    <input type="text"
                           wire:model.live.debounce.500ms="content.url"
                           placeholder="https://... ou #section"
                           class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Ouvrir dans</label>
                    <select wire:model.live="content.target"
                            class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="_self">Même fenêtre</option>
                        <option value="_blank">Nouvel onglet</option>
                    </select>
                </div>
                @break

            @case('form')
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Titre du formulaire</label>
                    <input type="text"
                           wire:model.live.debounce.300ms="content.title"
                           class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Champs</label>
                    <div class="space-y-2">
                        @foreach($content['fields'] ?? [] as $index => $field)
                            <div class="flex flex-col gap-2 bg-gray-700 p-2 rounded-lg">
                                <div class="flex gap-2 items-center">
                                    <input type="text"
                                           wire:model.live="content.fields.{{ $index }}.label"
                                           placeholder="Label"
                                           class="flex-1 px-2 py-1 bg-gray-600 border border-gray-500 rounded text-sm">
                                    <select wire:model.live="content.fields.{{ $index }}.type"
                                            class="px-2 py-1 bg-gray-600 border border-gray-500 rounded text-sm max-w-[120px]">
                                        <option value="text">Texte</option>
                                        <option value="email">Email</option>
                                        <option value="tel">Téléphone</option>
                                        <option value="textarea">Zone de texte</option>
                                        <option value="number">Nombre</option>
                                        <option value="date">Date</option>
                                        <option value="select">Liste déroulante</option>
                                        <option value="radio">Choix unique</option>
                                        <option value="checkbox">Cases à cocher</option>
                                        <option value="country">Pays</option>
                                        <option value="city">Ville</option>
                                        <option value="gender">Sexe</option>
                                        <option value="profession">Profession</option>
                                        <option value="company">Entreprise</option>
                                        <option value="url">Site web</option>
                                        <option value="whatsapp">WhatsApp</option>
                                    </select>
                                    <div class="flex items-center gap-1">
                                        <input type="checkbox"
                                               wire:model.live="content.fields.{{ $index }}.required"
                                               class="w-4 h-4 rounded bg-gray-600 border-gray-500 text-blue-600"
                                               title="Obligatoire">
                                        <button wire:click="removeFormField({{ $index }})"
                                                class="p-1 text-red-400 hover:text-red-300">
                                            <x-heroicon-o-trash class="w-4 h-4" />
                                        </button>
                                    </div>
                                </div>

                                {{-- Options for select/radio/checkbox --}}
                                @if(in_array($content['fields'][$index]['type'] ?? '', ['select', 'radio', 'checkbox']))
                                    <div class="pl-4 border-l-2 border-gray-600 mt-1">
                                        <label class="block text-xs text-gray-400 mb-1">Options (séparées par une virgule)</label>
                                        <input type="text"
                                               wire:model.live.debounce.500ms="content.fields.{{ $index }}.options_raw"
                                               placeholder="Option 1, Option 2, Option 3"
                                               class="w-full px-2 py-1 bg-gray-600 border border-gray-500 rounded text-sm">
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <button wire:click="addFormField"
                            class="mt-2 w-full py-2 text-sm bg-gray-700 hover:bg-gray-600 rounded-lg transition">
                        + Ajouter un champ
                    </button>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Texte du bouton</label>
                    <input type="text"
                           wire:model.live.debounce.300ms="content.button_text"
                           class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Image Illustration</label>
                    <input type="file" wire:model="imageUpload" class="w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:bg-blue-600 file:text-white hover:file:bg-blue-700">
                    @if(!empty($content['image_url']))
                        <div class="mt-2">
                            <img src="{{ $content['image_url'] }}" class="h-20 rounded object-cover">
                        </div>
                    @endif
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Disposition (si image)</label>
                    <select wire:model.live="content.layout" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="center">Centré (Formulaire sous image)</option>
                        <option value="left">Formulaire Gauche / Image Droite</option>
                        <option value="right">Formulaire Droite / Image Gauche</option>
                    </select>
                </div>
                @break

            @case('hero')
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Titre</label>
                    <input type="text" wire:model.live.debounce.300ms="content.title" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Sous-titre</label>
                    <textarea wire:model.live.debounce.300ms="content.subtitle" rows="2" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Texte Bouton</label>
                        <input type="text" wire:model.live.debounce.300ms="content.button_text" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">URL Bouton</label>
                        <input type="text" wire:model.live.debounce.300ms="content.button_url" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Image (Fond/Côté)</label>
                    <input type="file" wire:model="imageUpload" class="w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:bg-blue-600 file:text-white hover:file:bg-blue-700">
                    @if(!empty($content['image_url']))
                        <div class="mt-2">
                            <img src="{{ $content['image_url'] }}" class="h-20 rounded object-cover">
                        </div>
                    @endif
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Disposition</label>
                    <select wire:model.live="content.layout" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="left">Texte Gauche / Image Droite</option>
                        <option value="right">Texte Droite / Image Gauche</option>
                        <option value="center">Centre + Image de fond</option>
                    </select>
                </div>
                @break

            @case('text_image')
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Titre</label>
                    <input type="text" wire:model.live.debounce.300ms="content.title" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Texte (HTML)</label>
                    <textarea wire:model.live.debounce.500ms="content.text" rows="6" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 font-mono text-sm"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Image</label>
                    <input type="file" wire:model="imageUpload" class="w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:bg-blue-600 file:text-white hover:file:bg-blue-700">
                    @if(!empty($content['image_url']))
                        <div class="mt-2">
                            <img src="{{ $content['image_url'] }}" class="h-20 rounded object-cover">
                        </div>
                    @endif
                </div>
                 <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Position Image</label>
                    <select wire:model.live="content.image_position" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="right">Droite</option>
                        <option value="left">Gauche</option>
                    </select>
                </div>
                @break

            @case('features')
                <div class="space-y-4">
                    <label class="block text-sm font-medium text-gray-300">Liste des avantages</label>
                    @foreach($content['features'] ?? [] as $index => $feature)
                        <div class="bg-gray-700 p-3 rounded-lg space-y-2 relative border border-gray-600">
                            <button wire:click="removeFeature({{ $index }})" class="absolute top-2 right-2 text-red-400 hover:text-red-300 p-1">
                                <x-heroicon-o-trash class="w-4 h-4" />
                            </button>
                            <div>
                                <label class="text-xs text-gray-400">Titre</label>
                                <input type="text" wire:model.live.debounce.300ms="content.features.{{ $index }}.title" class="w-full px-2 py-1 bg-gray-600 border border-gray-500 rounded text-sm">
                            </div>
                            <div>
                                <label class="text-xs text-gray-400">Description</label>
                                <textarea wire:model.live.debounce.300ms="content.features.{{ $index }}.text" rows="2" class="w-full px-2 py-1 bg-gray-600 border border-gray-500 rounded text-sm"></textarea>
                            </div>
                        </div>
                    @endforeach
                    <button wire:click="addFeature" class="w-full py-2 bg-gray-700 hover:bg-gray-600 rounded-lg text-sm border border-dashed border-gray-500 transition">+ Ajouter un avantage</button>
                </div>
                @break

            @case('spacer')
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Hauteur</label>
                    <input type="text"
                           wire:model.live.debounce.300ms="content.height"
                           placeholder="40px"
                           class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                @break

            @case('countdown')
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Titre</label>
                    <input type="text"
                           wire:model.live.debounce.300ms="content.title"
                           placeholder="Offre expire dans :"
                           class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Durée (heures)</label>
                    <input type="number"
                           wire:model.live.debounce.300ms="content.hours"
                           min="1"
                           placeholder="24"
                           class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-400 mt-1">Le compte à rebours démarre à partir de maintenant</p>
                </div>

                {{-- Format Options --}}
                <div class="space-y-3 p-3 bg-gray-900/50 rounded-lg border border-gray-700">
                    <label class="block text-xs font-medium text-gray-400">Format d'affichage</label>
                    
                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1">Style</label>
                        <select wire:model.live="content.format" 
                                class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-sm">
                            <option value="boxes">Boxes (Recommandé)</option>
                            <option value="inline">Inline (00j 00h 00m 00s)</option>
                            <option value="minimal">Minimal (00:00:00:00)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1">Taille</label>
                        <select wire:model.live="content.size" 
                                class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-sm">
                            <option value="small">Petit</option>
                            <option value="medium">Moyen</option>
                            <option value="large">Grand</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" wire:model.live="content.show_labels" 
                               id="countdown-show-labels" class="rounded bg-gray-700 border-gray-600">
                        <label for="countdown-show-labels" class="text-sm text-gray-300">Afficher les labels (Jours, Heures...)</label>
                    </div>
                </div>
                @break
        @endswitch
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
                    <label class="block text-xs text-gray-400 mb-1">Taille de police</label>
                    <div class="flex gap-2">
                        <input type="range" 
                               wire:model.live="styles.fontSizeValue" 
                               min="10" max="72" 
                               class="flex-1">
                        <input type="text"
                               wire:model.live.debounce.300ms="styles.fontSize"
                               placeholder="16px"
                               class="w-20 px-2 py-1 bg-gray-700 border border-gray-600 rounded text-sm">
                    </div>
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
                    <div class="flex gap-2">
                        <input type="range" 
                               wire:model.live="styles.letterSpacingValue" 
                               min="-2" max="10" step="0.5" 
                               class="flex-1">
                        <input type="text"
                               wire:model.live.debounce.300ms="styles.letterSpacing"
                               placeholder="0px"
                               class="w-20 px-2 py-1 bg-gray-700 border border-gray-600 rounded text-sm">
                    </div>
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
                <label class="block text-xs text-gray-400 mb-1">Arrondi des coins</label>
                <div class="flex gap-2">
                    <input type="range" 
                           wire:model.live="styles.borderRadius" 
                           min="0" max="50" 
                           class="flex-1">
                    <input type="text"
                           wire:model.live.debounce.300ms="styles.borderRadius"
                           placeholder="8px"
                           class="w-20 px-2 py-1 bg-gray-700 border border-gray-600 rounded text-sm">
                </div>
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
