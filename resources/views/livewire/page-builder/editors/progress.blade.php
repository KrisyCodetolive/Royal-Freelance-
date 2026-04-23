<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">Pourcentage</label>
    <input type="range" wire:model.live="content.percentage" min="0" max="100" class="w-full">
    <div class="flex justify-between text-xs text-gray-400 mt-1">
        <span>0%</span>
        <span class="font-bold text-blue-400">{{ $content['percentage'] ?? 0 }}%</span>
        <span>100%</span>
    </div>
</div>

<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">Label</label>
    <input type="text" wire:model.live.debounce.300ms="content.label" placeholder="Ex: Étape 1 sur 3"
        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
</div>

<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">Couleur</label>
    <div class="flex gap-2">
        <input type="color" wire:model.live="content.color"
            class="w-12 h-10 rounded cursor-pointer border border-gray-600">
        <input type="text" wire:model.live.debounce.300ms="content.color" placeholder="#6366F1"
            class="flex-1 px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg">
    </div>
</div>

<div>
    <label class="flex items-center gap-2">
        <input type="checkbox" wire:model.live="content.show_percentage"
            class="rounded border-gray-600 text-blue-500 focus:ring-blue-500">
        <span class="text-sm text-gray-300">Afficher le pourcentage</span>
    </label>
</div>