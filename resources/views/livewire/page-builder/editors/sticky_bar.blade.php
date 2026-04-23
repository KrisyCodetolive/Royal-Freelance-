<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">Texte de la barre</label>
    <input type="text" wire:model.live.debounce.300ms="content.text" placeholder="🔥 Offre spéciale jusqu'à ce soir !"
        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
</div>

<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">Texte du bouton</label>
    <input type="text" wire:model.live.debounce.300ms="content.button_text" placeholder="En savoir plus →"
        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
</div>

<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">URL du bouton</label>
    <input type="url" wire:model.live.debounce.300ms="content.button_url" placeholder="https://... ou #section"
        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
</div>

<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">Position</label>
    <select wire:model.live="content.position" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg">
        <option value="top">En haut</option>
        <option value="bottom">En bas</option>
    </select>
</div>

<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">Couleur de fond</label>
    <div class="flex gap-2">
        <input type="color" wire:model.live="styles.backgroundColor"
            class="w-12 h-10 rounded cursor-pointer border border-gray-600">
        <input type="text" wire:model.live.debounce.300ms="styles.backgroundColor" placeholder="#1E40AF"
            class="flex-1 px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg">
    </div>
</div>

<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">Couleur du bouton</label>
    <div class="flex gap-2">
        <input type="color" wire:model.live="content.button_color"
            class="w-12 h-10 rounded cursor-pointer border border-gray-600">
        <input type="text" wire:model.live.debounce.300ms="content.button_color" placeholder="#F59E0B"
            class="flex-1 px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg">
    </div>
</div>