{{-- Master Block Actions Panel --}}
@php
    $isMasterBlock = $block->is_master_block ?? false;
    $hasLinkedMaster = $block->master_block_id ?? false;
    $masterBlock = $hasLinkedMaster ? $block->masterBlock : null;
@endphp

<div class="space-y-4">
    {{-- Status Badge --}}
    @if($isMasterBlock)
        <div class="bg-purple-500/20 border border-purple-500/30 rounded-lg p-4">
            <div class="flex items-center gap-2 mb-3">
                <x-heroicon-o-cube class="w-5 h-5 text-purple-400" />
                <span class="font-bold text-purple-400">Master Block</span>
            </div>
            <p class="text-sm text-gray-400 mb-4">
                Ce bloc est un master block. Les modifications seront propagées à toutes ses instances.
            </p>

            {{-- Master Block Name --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-300 mb-1">Nom du Master Block</label>
                <p class="text-white font-medium">{{ $block->master_block_name ?? 'Sans nom' }}</p>
            </div>

            {{-- Instance Count --}}
            @php
                $instanceCount = $block->instances()->count();
            @endphp
            <div class="flex items-center justify-between text-sm mb-4">
                <span class="text-gray-400">Instances utilisées :</span>
                <span class="text-white font-bold">{{ $instanceCount }}</span>
            </div>

            {{-- Actions --}}
            @if($instanceCount > 0)
                <button wire:click="$parent.syncMasterToAllInstances({{ $block->id }})"
                    class="w-full px-4 py-2 bg-purple-600 hover:bg-purple-700 rounded-lg transition text-sm flex items-center justify-center gap-2">
                    <x-heroicon-o-arrow-path class="w-4 h-4" />
                    Synchroniser {{ $instanceCount }} instance(s)
                </button>
            @endif
        </div>
    @elseif($hasLinkedMaster)
        <div class="bg-blue-500/20 border border-blue-500/30 rounded-lg p-4">
            <div class="flex items-center gap-2 mb-3">
                <x-heroicon-o-link class="w-5 h-5 text-blue-400" />
                <span class="font-bold text-blue-400">Instance de Master Block</span>
            </div>
            <p class="text-sm text-gray-400 mb-4">
                Ce bloc est lié au master block : <strong
                    class="text-white">{{ $masterBlock->master_block_name ?? 'Sans nom' }}</strong>
            </p>

            <div class="space-y-2">
                <button wire:click="$parent.syncBlockFromMaster({{ $block->id }})"
                    class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded-lg transition text-sm flex items-center justify-center gap-2">
                    <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
                    Synchroniser depuis le master
                </button>

                <button wire:click="$parent.detachFromMaster({{ $block->id }})"
                    class="w-full px-4 py-2 bg-gray-600 hover:bg-gray-700 rounded-lg transition text-sm flex items-center justify-center gap-2"
                    onclick="return confirm('Détacher ce bloc ? Il deviendra indépendant.')">
                    <x-heroicon-o-link-slash class="w-4 h-4" />
                    Détacher du master
                </button>
            </div>
        </div>
    @else
        {{-- Convert to Master Block --}}
        <div x-data="{ showConvert: false, masterName: '' }" class="space-y-3">
            <button @click="showConvert = !showConvert"
                class="w-full px-4 py-3 bg-gray-700 hover:bg-gray-600 rounded-lg transition text-sm flex items-center justify-between">
                <span class="flex items-center gap-2">
                    <x-heroicon-o-cube class="w-4 h-4 text-purple-400" />
                    Convertir en Master Block
                </span>
                <x-heroicon-o-chevron-down class="w-4 h-4 transition-transform"
                    x-bind:class="{ 'rotate-180': showConvert }" />
            </button>

            <div x-show="showConvert" x-collapse class="space-y-3 p-4 bg-gray-800 rounded-lg">
                <p class="text-sm text-gray-400">
                    Les master blocks peuvent être réutilisés sur plusieurs pages.
                    Modifier le master mettra à jour toutes ses instances.
                </p>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Nom du master block</label>
                    <input type="text" x-model="masterName" placeholder="Ex: Footer Standard, CTA Principal..."
                        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>

                <button
                    @click="if(masterName.trim()) { $wire.$parent.convertToMasterBlock({{ $block->id }}, masterName); showConvert = false; }"
                    x-bind:disabled="!masterName.trim()"
                    x-bind:class="{ 'opacity-50 cursor-not-allowed': !masterName.trim() }"
                    class="w-full px-4 py-2 bg-purple-600 hover:bg-purple-700 rounded-lg transition text-sm flex items-center justify-center gap-2">
                    <x-heroicon-o-cube class="w-4 h-4" />
                    Créer le Master Block
                </button>
            </div>
        </div>
    @endif
</div>