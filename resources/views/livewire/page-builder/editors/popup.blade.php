<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">Titre</label>
    <input type="text" wire:model.live.debounce.300ms="content.title" placeholder="🎁 Offre spéciale !"
        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
</div>

<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">Contenu</label>
    <textarea wire:model.live.debounce.500ms="content.body" rows="4" placeholder="<p>Votre message ici...</p>"
        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
    <p class="text-xs text-gray-400 mt-1">HTML autorisé</p>
</div>

<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">Texte du bouton</label>
    <input type="text" wire:model.live.debounce.300ms="content.button_text" placeholder="Fermer"
        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
</div>

<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">URL du bouton (optionnel)</label>
    <input type="url" wire:model.live.debounce.300ms="content.button_url" placeholder="https://..."
        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
    <p class="text-xs text-gray-400 mt-1">Laissez vide pour simplement fermer</p>
</div>

<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">URL de l'image (optionnel)</label>
    <input type="url" wire:model.live.debounce.300ms="content.image" placeholder="https://..."
        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
</div>

<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">Déclencheur</label>
    <select wire:model.live="content.trigger" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg">
        <option value="exit_intent">Intention de sortie</option>
        <option value="delay">Après un délai</option>
        <option value="scroll">Au scroll</option>
    </select>
</div>

@if(($content['trigger'] ?? 'exit_intent') === 'delay')
    <div>
        <label class="block text-sm font-medium text-gray-300 mb-1">Délai (secondes)</label>
        <input type="number" wire:model.live.debounce.300ms="content.delay_seconds" placeholder="5" min="1" max="60"
            class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
    </div>
@endif

@if(($content['trigger'] ?? 'exit_intent') === 'scroll')
    <div>
        <label class="block text-sm font-medium text-gray-300 mb-1">Pourcentage de scroll</label>
        <input type="range" wire:model.live="content.scroll_percent" min="10" max="90" step="10" class="w-full">
        <div class="text-center text-sm text-gray-400">{{ $content['scroll_percent'] ?? 50 }}%</div>
    </div>
@endif

<div>
    <label class="flex items-center gap-2">
        <input type="checkbox" wire:model.live="content.show_once"
            class="rounded border-gray-600 text-blue-500 focus:ring-blue-500">
        <span class="text-sm text-gray-300">Afficher une seule fois</span>
    </label>
</div>