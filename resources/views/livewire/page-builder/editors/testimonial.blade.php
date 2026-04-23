<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">Nom</label>
    <input type="text"
           wire:model.live.debounce.300ms="content.name"
           placeholder="Jean Dupont"
           class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
</div>

<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">Rôle/Entreprise</label>
    <input type="text"
           wire:model.live.debounce.300ms="content.role"
           placeholder="CEO, Entreprise"
           class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
</div>

<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">Témoignage</label>
    <textarea wire:model.live.debounce.500ms="content.text"
              rows="5"
              placeholder="Ce produit a changé ma vie..."
              class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
</div>

<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">URL Photo</label>
    <input type="url"
           wire:model.live.debounce.300ms="content.avatar"
           placeholder="https://..."
           class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
</div>

<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">Note (étoiles)</label>
    <select wire:model.live="content.rating" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
        <option value="5">⭐⭐⭐⭐⭐ (5/5)</option>
        <option value="4">⭐⭐⭐⭐ (4/5)</option>
        <option value="3">⭐⭐⭐ (3/5)</option>
    </select>
</div>
