<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">Style</label>
    <select wire:model.live="content.style" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
        <option value="solid">Solide</option>
        <option value="dashed">Pointillé</option>
        <option value="dotted">Points</option>
    </select>
</div>

<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">Épaisseur (px)</label>
    <input type="number"
           wire:model.live.debounce.300ms="content.width"
           min="1"
           max="10"
           placeholder="1"
           class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
</div>

<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">Largeur (%)</label>
    <input type="number"
           wire:model.live.debounce.300ms="content.max_width"
           min="10"
           max="100"
           placeholder="100"
           class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
</div>
