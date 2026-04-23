<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">Titre de l'offre</label>
    <input type="text" wire:model.live.debounce.300ms="content.title" placeholder="Version Audio du Livre"
        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
</div>

<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">Description</label>
    <textarea wire:model.live.debounce.500ms="content.description" rows="3"
        placeholder="Pourquoi ajouter cette offre..."
        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
</div>

<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">Prix additionnel</label>
    <input type="text" wire:model.live.debounce.300ms="content.price" placeholder="17€"
        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
</div>

<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">URL de l'image (optionnel)</label>
    <input type="url" wire:model.live.debounce.300ms="content.image" placeholder="https://..."
        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
</div>

<div>
    <label class="flex items-center gap-2">
        <input type="checkbox" wire:model.live="content.default_checked"
            class="rounded border-gray-600 text-amber-500 focus:ring-amber-500">
        <span class="text-sm text-gray-300">Coché par défaut</span>
    </label>
</div>