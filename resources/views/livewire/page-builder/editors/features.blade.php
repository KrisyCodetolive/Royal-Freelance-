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
