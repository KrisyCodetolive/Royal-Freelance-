<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">URL du fichier audio</label>
    <input type="url" wire:model.live.debounce.300ms="content.url" placeholder="https://...mp3"
        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
</div>

<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">Titre (optionnel)</label>
    <input type="text" wire:model.live.debounce.300ms="content.title" placeholder="Mon podcast"
        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
</div>

<div>
    <label class="flex items-center gap-2">
        <input type="checkbox" wire:model.live="content.show_download"
            class="rounded border-gray-600 text-blue-500 focus:ring-blue-500">
        <span class="text-sm text-gray-300">Afficher bouton de téléchargement</span>
    </label>
</div>

<div>
    <label class="flex items-center gap-2">
        <input type="checkbox" wire:model.live="content.autoplay"
            class="rounded border-gray-600 text-blue-500 focus:ring-blue-500">
        <span class="text-sm text-gray-300">Lecture automatique</span>
    </label>
</div>