<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">Code HTML / Embed</label>
    <textarea wire:model.live.debounce.500ms="content.html" rows="6" placeholder="<iframe src='...'></iframe>"
        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 font-mono text-xs"></textarea>
    <p class="text-xs text-gray-400 mt-1">Collez votre code d'embed (YouTube, Soundcloud, etc.)</p>
</div>

<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">Ratio d'aspect</label>
    <select wire:model.live="content.aspect_ratio"
        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg">
        <option value="">Auto</option>
        <option value="16:9">16:9 (Vidéo)</option>
        <option value="4:3">4:3</option>
        <option value="1:1">1:1 (Carré)</option>
        <option value="9:16">9:16 (Vertical)</option>
    </select>
</div>

<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">Largeur max</label>
    <input type="text" wire:model.live.debounce.300ms="styles.maxWidth" placeholder="800px ou 100%"
        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
</div>