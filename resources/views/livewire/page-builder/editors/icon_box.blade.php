<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">Icône / Emoji</label>
    <input type="text" wire:model.live.debounce.300ms="content.icon" placeholder="✨ ou heroicon-o-star"
        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
    <p class="text-xs text-gray-400 mt-1">Utilisez un emoji ou un nom Heroicon</p>
</div>

<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">Titre</label>
    <input type="text" wire:model.live.debounce.300ms="content.title" placeholder="Mon titre"
        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
</div>

<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">Texte</label>
    <textarea wire:model.live.debounce.500ms="content.text" rows="3" placeholder="Description..."
        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
</div>

<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">Alignement</label>
    <select wire:model.live="content.alignment" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg">
        <option value="center">Centre</option>
        <option value="left">Gauche</option>
        <option value="right">Droite</option>
    </select>
</div>