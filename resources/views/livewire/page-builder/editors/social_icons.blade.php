<div>
    <label class="block text-sm font-medium text-gray-300 mb-2">Réseaux sociaux</label>

    @if(!empty($content['links']))
        <div class="space-y-2 mb-4">
            @foreach($content['links'] as $index => $link)
                <div class="flex gap-2 items-center">
                    <select wire:model.live="content.links.{{ $index }}.platform"
                        class="w-28 px-2 py-2 bg-gray-700 border border-gray-600 rounded text-sm">
                        <option value="facebook">Facebook</option>
                        <option value="instagram">Instagram</option>
                        <option value="youtube">YouTube</option>
                        <option value="twitter">Twitter</option>
                        <option value="linkedin">LinkedIn</option>
                        <option value="tiktok">TikTok</option>
                        <option value="whatsapp">WhatsApp</option>
                        <option value="telegram">Telegram</option>
                    </select>
                    <input type="url" wire:model.live.debounce.300ms="content.links.{{ $index }}.url" placeholder="https://..."
                        class="flex-1 px-3 py-2 bg-gray-700 border border-gray-600 rounded text-sm">
                    <button wire:click="removeLink({{ $index }})" class="text-red-400 hover:text-red-300 p-2">
                        ✕
                    </button>
                </div>
            @endforeach
        </div>
    @endif

    <button wire:click="addLink"
        class="w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 rounded-lg text-sm font-medium transition">
        + Ajouter un réseau
    </button>
</div>

<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">Taille des icônes</label>
    <select wire:model.live="content.size" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg">
        <option value="small">Petite</option>
        <option value="medium">Moyenne</option>
        <option value="large">Grande</option>
    </select>
</div>

<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">Style</label>
    <select wire:model.live="content.style" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg">
        <option value="filled">Rempli</option>
        <option value="outline">Contour</option>
        <option value="minimal">Minimal</option>
    </select>
</div>