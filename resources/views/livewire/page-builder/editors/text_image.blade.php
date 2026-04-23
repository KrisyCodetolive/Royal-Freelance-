<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">Titre</label>
    <input type="text"
           wire:model.live.debounce.300ms="content.title"
           placeholder="Titre"
           class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
</div>

<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">Texte</label>
    <textarea wire:model.live.debounce.500ms="content.text"
              rows="6"
              placeholder="Votre texte..."
              class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
</div>

<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">URL Image</label>
    <input type="url"
           wire:model.live.debounce.300ms="content.image_url"
           placeholder="https://..."
           class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
</div>

<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">Position Image</label>
    <select wire:model.live="content.image_position" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
        <option value="right">Droite</option>
        <option value="left">Gauche</option>
    </select>
</div>
