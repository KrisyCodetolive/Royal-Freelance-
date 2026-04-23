<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">URL de la vidéo</label>
    @if(!empty($content['url']))
        <div class="mb-3 relative group">
            <div class="aspect-video bg-gray-800 rounded-lg border-2 border-gray-600 flex items-center justify-center">
                @if(str_contains($content['url'], 'youtube.com') || str_contains($content['url'], 'youtu.be'))
                    <div class="text-center">
                        <x-heroicon-o-video-camera class="w-12 h-12 mx-auto text-red-500 mb-2" />
                        <p class="text-sm text-gray-400">Vidéo YouTube</p>
                    </div>
                @elseif(str_contains($content['url'], 'vimeo.com'))
                    <div class="text-center">
                        <x-heroicon-o-video-camera class="w-12 h-12 mx-auto text-blue-500 mb-2" />
                        <p class="text-sm text-gray-400">Vidéo Vimeo</p>
                    </div>
                @else
                    <div class="text-center">
                        <x-heroicon-o-video-camera class="w-12 h-12 mx-auto text-gray-500 mb-2" />
                        <p class="text-sm text-gray-400">Vidéo</p>
                    </div>
                @endif
            </div>
            <button type="button" wire:click="$set('content.url', '')" 
                    class="absolute top-2 right-2 p-2 bg-red-600 hover:bg-red-700 rounded-lg opacity-0 group-hover:opacity-100 transition">
                <x-heroicon-o-trash class="w-4 h-4" />
            </button>
        </div>
    @endif
    <input type="url"
           wire:model.live.debounce.500ms="content.url"
           placeholder="https://youtube.com/watch?v=... ou https://vimeo.com/..."
           class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
    <p class="text-xs text-gray-500 mt-1">Supporte YouTube, Vimeo ou vidéo directe (MP4, WebM)</p>
</div>

<div>
    <label class="block text-sm font-medium text-gray-300 mb-2">Options de lecture</label>
    <div class="space-y-2">
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox"
                   wire:model.live="content.autoplay"
                   class="w-4 h-4 rounded bg-gray-700 border-gray-600 text-blue-600 focus:ring-blue-500">
            <span class="text-sm">Lecture automatique</span>
        </label>
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox"
                   wire:model.live="content.controls"
                   class="w-4 h-4 rounded bg-gray-700 border-gray-600 text-blue-600 focus:ring-blue-500">
            <span class="text-sm">Afficher les contrôles</span>
        </label>
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox"
                   wire:model.live="content.loop"
                   class="w-4 h-4 rounded bg-gray-700 border-gray-600 text-blue-600 focus:ring-blue-500">
            <span class="text-sm">Lecture en boucle</span>
        </label>
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox"
                   wire:model.live="content.muted"
                   class="w-4 h-4 rounded bg-gray-700 border-gray-600 text-blue-600 focus:ring-blue-500">
            <span class="text-sm">Muet par défaut</span>
        </label>
    </div>
</div>
