<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">Configuration des colonnes</label>
    <select wire:model.live="content.columns_layout"
        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg">
        <option value="1/2,1/2">2 colonnes (50% / 50%)</option>
        <option value="1/3,2/3">2 colonnes (33% / 66%)</option>
        <option value="2/3,1/3">2 colonnes (66% / 33%)</option>
        <option value="1/3,1/3,1/3">3 colonnes égales</option>
        <option value="1/4,1/2,1/4">3 colonnes (25% / 50% / 25%)</option>
        <option value="1/4,1/4,1/4,1/4">4 colonnes égales</option>
    </select>
</div>

<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">Espacement</label>
    <select wire:model.live="styles.gap" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg">
        <option value="8px">Petit (8px)</option>
        <option value="16px">Moyen (16px)</option>
        <option value="24px">Standard (24px)</option>
        <option value="32px">Large (32px)</option>
        <option value="48px">Extra-large (48px)</option>
    </select>
</div>

<div class="p-3 bg-blue-500/10 border border-blue-500/20 rounded-lg">
    <p class="text-xs text-blue-300">
        💡 Glissez des blocs dans chaque colonne pour les remplir.
        Les colonnes s'empilent automatiquement sur mobile.
    </p>
</div>