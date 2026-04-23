<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">Padding vertical</label>
    <input type="text" wire:model.live.debounce.300ms="styles.paddingTop" placeholder="60px"
        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
</div>

<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">Couleur de fond</label>
    <div class="flex gap-2">
        <input type="color" wire:model.live="styles.backgroundColor"
            class="w-12 h-10 rounded cursor-pointer border border-gray-600">
        <input type="text" wire:model.live.debounce.300ms="styles.backgroundColor" placeholder="transparent"
            class="flex-1 px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg">
    </div>
</div>

<div class="p-3 bg-blue-500/10 border border-blue-500/20 rounded-lg">
    <p class="text-xs text-blue-300">
        💡 Une section est un conteneur qui peut contenir d'autres blocs.
        Utilisez-la pour organiser votre page en zones distinctes.
    </p>
</div>