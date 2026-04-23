<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">Hauteur (px)</label>
    <input type="number"
           wire:model.live.debounce.300ms="content.height"
           min="10"
           max="500"
           placeholder="40"
           class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
    <p class="text-xs text-gray-400 mt-1">Crée un espace vertical entre les blocs</p>
</div>

<div class="flex gap-2">
    @foreach([20, 40, 60, 80, 100] as $height)
        <button type="button" 
                wire:click="$set('content.height', {{ $height }})"
                class="flex-1 px-2 py-1.5 text-xs bg-gray-700 hover:bg-gray-600 rounded transition {{ ($content['height'] ?? 40) == $height ? 'ring-2 ring-blue-500' : '' }}">
            {{ $height }}px
        </button>
    @endforeach
</div>
