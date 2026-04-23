<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">Image</label>
    
    @if(!empty($content['url']))
        <div class="mb-3 relative group">
            <img src="{{ $content['url'] }}" 
                 class="w-full h-48 object-cover rounded-lg border-2 border-gray-600">
            <button type="button" wire:click="$set('content.url', '')" 
                    class="absolute top-2 right-2 p-2 bg-red-600 hover:bg-red-700 rounded-lg opacity-0 group-hover:opacity-100 transition">
                <x-heroicon-o-trash class="w-4 h-4" />
            </button>
        </div>
    @endif
    
    @if(empty($content['url']))
        <div class="mb-3 border-2 border-dashed border-gray-600 rounded-lg p-8 text-center bg-gray-700/50 hover:bg-gray-700 transition cursor-pointer">
            <input type="file" 
                   wire:model="imageUpload" 
                   id="imageUpload-{{ $block->id }}"
                   accept="image/*"
                   class="hidden">
            <label for="imageUpload-{{ $block->id }}" class="cursor-pointer">
                <x-heroicon-o-photo class="w-12 h-12 mx-auto text-gray-400 mb-2" />
                <p class="text-sm text-gray-300">Cliquez pour uploader</p>
                <p class="text-xs text-gray-500 mt-1">ou glissez-déposez une image</p>
            </label>
        </div>
    @endif
    
    <input type="url"
           wire:model.live.debounce.300ms="content.url"
           placeholder="https://... (URL externe)"
           class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
</div>

<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">Légende</label>
    <input type="text"
           wire:model.live.debounce.300ms="content.caption"
           placeholder="Légende de l'image"
           class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
</div>

<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">Texte alternatif</label>
    <input type="text"
           wire:model.live.debounce.300ms="content.alt"
           placeholder="Description de l'image"
           class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
</div>
