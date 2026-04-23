<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">Titre</label>
    <input type="text"
           wire:model.live.debounce.300ms="content.title"
           placeholder="Offre expire dans :"
           class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
</div>

<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">Durée (heures)</label>
    <input type="number"
           wire:model.live.debounce.300ms="content.hours"
           min="1"
           placeholder="24"
           class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
    <p class="text-xs text-gray-400 mt-1">Le compte à rebours démarre à partir de maintenant</p>
</div>

{{-- Format Options --}}
<div class="space-y-3 p-3 bg-gray-900/50 rounded-lg border border-gray-700">
    <label class="block text-xs font-medium text-gray-400">Format d'affichage</label>
    
    <div>
        <label class="block text-xs font-medium text-gray-400 mb-1">Style</label>
        <select wire:model.live="content.format" 
                class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-sm">
            <option value="boxes">Boxes (Recommandé)</option>
            <option value="inline">Inline (00j 00h 00m 00s)</option>
            <option value="minimal">Minimal (00:00:00:00)</option>
        </select>
    </div>

    <div>
        <label class="block text-xs font-medium text-gray-400 mb-1">Taille</label>
        <select wire:model.live="content.size" 
                class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-sm">
            <option value="small">Petit</option>
            <option value="medium">Moyen</option>
            <option value="large">Grand</option>
        </select>
    </div>

    <div class="flex items-center gap-2">
        <input type="checkbox" wire:model.live="content.show_labels" 
               id="countdown-show-labels" class="rounded bg-gray-700 border-gray-600">
        <label for="countdown-show-labels" class="text-sm text-gray-300">Afficher les labels (Jours, Heures...)</label>
    </div>
</div>
