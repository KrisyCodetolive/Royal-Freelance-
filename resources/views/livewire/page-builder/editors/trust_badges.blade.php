<div>
    <label class="block text-sm font-medium text-gray-300 mb-2">Badges de confiance</label>

    @if(!empty($content['badges']))
        <div class="space-y-2 mb-4">
            @foreach($content['badges'] as $index => $badge)
                <div class="flex gap-2 items-center">
                    <input type="text" wire:model.live.debounce.300ms="content.badges.{{ $index }}.icon" placeholder="✓"
                        class="w-16 px-3 py-2 bg-gray-700 border border-gray-600 rounded text-sm text-center">
                    <input type="text" wire:model.live.debounce.300ms="content.badges.{{ $index }}.text"
                        placeholder="Texte du badge"
                        class="flex-1 px-3 py-2 bg-gray-700 border border-gray-600 rounded text-sm">
                    <button wire:click="removeBadge({{ $index }})" class="text-red-400 hover:text-red-300 p-2">
                        ✕
                    </button>
                </div>
            @endforeach
        </div>
    @endif

    <button wire:click="addBadge"
        class="w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 rounded-lg text-sm font-medium transition">
        + Ajouter un badge
    </button>
</div>

<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">Disposition</label>
    <select wire:model.live="content.layout" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg">
        <option value="horizontal">Horizontal</option>
        <option value="vertical">Vertical</option>
        <option value="grid">Grille</option>
    </select>
</div>