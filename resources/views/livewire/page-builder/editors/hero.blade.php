<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">Titre</label>
    <input type="text"
           wire:model.live.debounce.300ms="content.title"
           placeholder="Votre Titre"
           class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
</div>

<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">Sous-titre</label>
    <textarea wire:model.live.debounce.300ms="content.subtitle"
              rows="3"
              placeholder="Votre sous-titre..."
              class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
</div>

<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">Texte du bouton</label>
    <input type="text"
           wire:model.live.debounce.300ms="content.button_text"
           placeholder="Bouton"
           class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
</div>

<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">URL Image</label>
    <input type="url"
           wire:model.live.debounce.300ms="content.image_url"
           placeholder="https://..."
           class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
</div>

<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">Layout</label>
    <select wire:model.live="content.layout" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
        <option value="left">Image à gauche</option>
        <option value="right">Image à droite</option>
        <option value="center">Centré avec fond</option>
    </select>
</div>
