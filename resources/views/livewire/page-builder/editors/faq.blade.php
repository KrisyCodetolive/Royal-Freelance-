<div>
    <label class="block text-sm font-medium text-gray-300 mb-2">Titre</label>
    <input type="text" wire:model.live.debounce.300ms="content.title" placeholder="❓ Questions Fréquentes"
        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
</div>

<div>
    <label class="block text-sm font-medium text-gray-300 mb-2">Questions / Réponses</label>

    @if(!empty($content['items']))
        <div class="space-y-3 mb-4">
            @foreach($content['items'] as $index => $item)
                <div class="bg-gray-800 p-3 rounded-lg border border-gray-600">
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-xs text-gray-400">Question {{ $index + 1 }}</span>
                        <button wire:click="removeItem({{ $index }})" class="text-red-400 hover:text-red-300 text-xs">
                            Supprimer
                        </button>
                    </div>
                    <input type="text" wire:model.live.debounce.300ms="content.items.{{ $index }}.question"
                        placeholder="Question..."
                        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded mb-2 text-sm">
                    <textarea wire:model.live.debounce.500ms="content.items.{{ $index }}.answer" placeholder="Réponse..."
                        rows="2" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-sm"></textarea>
                </div>
            @endforeach
        </div>
    @endif

    <button wire:click="addItem"
        class="w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 rounded-lg text-sm font-medium transition">
        + Ajouter une question
    </button>
</div>