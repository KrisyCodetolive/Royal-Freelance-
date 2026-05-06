<div class="flex flex-col h-screen bg-gray-900">

    {{-- ===== HEADER ===== --}}
    <header class="flex items-center justify-between px-4 bg-gray-800 border-b border-gray-700 shrink-0" style="height: var(--header-height)">
        <div class="flex items-center gap-3">
            <a href="{{ url()->previous() }}" class="text-gray-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <p class="text-xs text-gray-400 leading-none">Email Builder</p>
                <p class="text-sm font-semibold text-white leading-tight truncate max-w-xs">{{ $email->subject }}</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            @if(session('saved'))
                <span class="text-xs text-green-400 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Sauvegardé
                </span>
            @endif

            <button
                wire:click="save"
                wire:loading.attr="disabled"
                class="flex items-center gap-2 px-4 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium rounded-lg transition-colors disabled:opacity-50"
            >
                <span wire:loading.remove wire:target="save">Sauvegarder</span>
                <span wire:loading wire:target="save">Sauvegarde...</span>
            </button>
        </div>
    </header>

    {{-- ===== BODY ===== --}}
    <div class="flex flex-1 overflow-hidden">

        {{-- ===== COLONNE GAUCHE — Blocs disponibles ===== --}}
        <aside class="flex flex-col bg-gray-800 border-r border-gray-700 shrink-0 eb-scrollbar overflow-y-auto" style="width: var(--sidebar-width)">
            <div class="p-3 border-b border-gray-700">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Blocs</p>
            </div>

            <div class="p-3 grid grid-cols-2 gap-2">
                @foreach($blockTypes as $blockType)
                    <button
                        wire:click="addBlock('{{ $blockType['type'] }}')"
                        class="flex flex-col items-center gap-1.5 p-3 bg-gray-700 hover:bg-gray-600 rounded-lg transition-colors text-center group"
                    >
                        <span class="text-xl">{{ $blockType['icon'] }}</span>
                        <span class="text-xs text-gray-300 group-hover:text-white font-medium">{{ $blockType['label'] }}</span>
                    </button>
                @endforeach
            </div>

            {{-- Variables disponibles --}}
            <div class="mt-auto p-3 border-t border-gray-700">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Variables</p>
                <div class="flex flex-wrap gap-1">
                    @foreach(['{first_name}', '{last_name}', '{email}', '{funnel_name}'] as $var)
                        <span
                            class="text-xs px-2 py-0.5 bg-gray-700 text-indigo-300 rounded cursor-pointer hover:bg-indigo-700 hover:text-white transition-colors"
                            title="Cliquez pour copier"
                            onclick="navigator.clipboard.writeText('{{ $var }}')"
                        >{{ $var }}</span>
                    @endforeach
                </div>
            </div>
        </aside>

        {{-- ===== COLONNE CENTRALE — Canvas email ===== --}}
        <main class="flex-1 overflow-y-auto email-preview eb-scrollbar" wire:click.self="deselectBlock">

            {{-- Modal choix de template --}}
            @if($showTemplates)
                <div class="flex items-center justify-center min-h-full p-8">
                    <div class="bg-gray-800 rounded-2xl p-8 max-w-2xl w-full border border-gray-700 shadow-2xl">
                        <h2 class="text-xl font-bold text-white mb-1">Choisir un point de départ</h2>
                        <p class="text-gray-400 text-sm mb-6">Sélectionnez un template ou partez de zéro.</p>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            {{-- Template Bienvenue --}}
                            <button
                                type="button"
                                wire:click="selectTemplate('welcome')"
                                class="flex flex-col gap-2 p-4 bg-gray-700 hover:bg-indigo-700 rounded-xl text-left transition-colors border border-gray-600 hover:border-indigo-500 group cursor-pointer"
                            >
                                <div class="flex items-center gap-2">
                                    <span class="text-2xl">👋</span>
                                    <span class="font-semibold text-white">Email de bienvenue</span>
                                </div>
                                <p class="text-xs text-gray-400 group-hover:text-indigo-200">Logo · Titre · Texte · Bouton CTA</p>
                            </button>

                            {{-- Template Offre --}}
                            <button
                                type="button"
                                wire:click="selectTemplate('offer')"
                                class="flex flex-col gap-2 p-4 bg-gray-700 hover:bg-red-700 rounded-xl text-left transition-colors border border-gray-600 hover:border-red-500 group cursor-pointer"
                            >
                                <div class="flex items-center gap-2">
                                    <span class="text-2xl">🎯</span>
                                    <span class="font-semibold text-white">Email d'offre</span>
                                </div>
                                <p class="text-xs text-gray-400 group-hover:text-red-200">Titre · Texte · Bouton rouge urgence</p>
                            </button>

                            {{-- Template Suivi --}}
                            <button
                                type="button"
                                wire:click="selectTemplate('followup')"
                                class="flex flex-col gap-2 p-4 bg-gray-700 hover:bg-green-700 rounded-xl text-left transition-colors border border-gray-600 hover:border-green-500 group cursor-pointer"
                            >
                                <div class="flex items-center gap-2">
                                    <span class="text-2xl">💬</span>
                                    <span class="font-semibold text-white">Email de suivi</span>
                                </div>
                                <p class="text-xs text-gray-400 group-hover:text-green-200">Titre · Texte personnel · Bouton répondre</p>
                            </button>

                            {{-- Template Relance --}}
                            <button
                                type="button"
                                wire:click="selectTemplate('relance')"
                                class="flex flex-col gap-2 p-4 bg-gray-700 hover:bg-orange-700 rounded-xl text-left transition-colors border border-gray-600 hover:border-orange-500 group cursor-pointer"
                            >
                                <div class="flex items-center gap-2">
                                    <span class="text-2xl">🔔</span>
                                    <span class="font-semibold text-white">Email de relance</span>
                                </div>
                                <p class="text-xs text-gray-400 group-hover:text-orange-200">Titre urgent · Texte · Double bouton</p>
                            </button>

                            {{-- Blank — pleine largeur pour bien le distinguer --}}
                            <button
                                type="button"
                                wire:click="startBlank"
                                class="col-span-2 flex items-center gap-3 p-4 bg-gray-900 hover:bg-gray-700 rounded-xl text-left transition-colors border border-gray-600 border-dashed hover:border-gray-400 group cursor-pointer"
                            >
                                <span class="text-2xl">✨</span>
                                <div>
                                    <p class="font-semibold text-white">Email vide — partir de zéro</p>
                                    <p class="text-xs text-gray-400 group-hover:text-gray-200">Canvas blanc, vous ajoutez les blocs vous-même</p>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>

            @else
                {{-- Canvas principal --}}
                <div class="py-8 px-4">
                    <div class="email-canvas shadow-xl rounded-lg overflow-hidden">

                        @if(empty($blocks))
                            <div class="flex flex-col items-center justify-center py-20 text-gray-400">
                                <svg class="w-12 h-12 mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <p class="text-sm">Ajoutez des blocs depuis le panneau de gauche</p>
                            </div>
                        @else
                            @foreach($blocks as $block)
                                <div
                                    wire:key="block-{{ $block['id'] }}"
                                    wire:click.stop="selectBlock('{{ $block['id'] }}')"
                                    class="block-wrapper relative {{ $selectedBlockId === $block['id'] ? 'selected' : '' }}"
                                >
                                    {{-- Actions du bloc --}}
                                    <div class="absolute right-1 top-1 z-10 flex gap-1 opacity-0 group-hover:opacity-100 {{ $selectedBlockId === $block['id'] ? 'opacity-100' : '' }} transition-opacity">
                                        <div class="flex gap-1 bg-gray-800 rounded-md px-1 py-0.5 shadow">
                                            <button wire:click.stop="moveUp('{{ $block['id'] }}')" class="p-0.5 text-gray-400 hover:text-white" title="Monter">↑</button>
                                            <button wire:click.stop="moveDown('{{ $block['id'] }}')" class="p-0.5 text-gray-400 hover:text-white" title="Descendre">↓</button>
                                            <button wire:click.stop="deleteBlock('{{ $block['id'] }}')" class="p-0.5 text-red-400 hover:text-red-300" title="Supprimer">✕</button>
                                        </div>
                                    </div>

                                    {{-- Rendu du bloc --}}
                                    @include('livewire.email-builder.blocks.' . $block['type'], [
                                        'content' => $block['content'],
                                        'render' => false,
                                    ])
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            @endif
        </main>

        {{-- ===== COLONNE DROITE — Panneau d'édition ===== --}}
        @if(!$showTemplates)
            <aside class="flex flex-col bg-gray-800 border-l border-gray-700 shrink-0 eb-scrollbar overflow-y-auto" style="width: var(--panel-width)">

                @php
                    $selectedBlock = collect($blocks)->firstWhere('id', $selectedBlockId);
                @endphp

                @if($selectedBlock)
                    <div class="p-3 border-b border-gray-700 flex items-center justify-between">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            Édition — {{ $selectedBlock['type'] }}
                        </p>
                        <button wire:click="deselectBlock" class="text-gray-500 hover:text-white">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="p-4 space-y-4">
                        @include('livewire.email-builder.partials.editor-' . $selectedBlock['type'], [
                            'block' => $selectedBlock,
                        ])
                    </div>

                @else
                    <div class="flex flex-col items-center justify-center flex-1 text-gray-500 p-6 text-center">
                        <svg class="w-10 h-10 mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        <p class="text-sm">Cliquez sur un bloc pour l'éditer</p>
                    </div>
                @endif

            </aside>
        @endif

    </div>
</div>
