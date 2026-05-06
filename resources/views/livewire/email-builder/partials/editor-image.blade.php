<div x-data="{
    url:   '{{ addslashes($block['content']['url'] ?? '') }}',
    alt:   '{{ addslashes($block['content']['alt'] ?? '') }}',
    align: '{{ $block['content']['align'] ?? 'center' }}',
    width: '{{ $block['content']['width'] ?? '100%' }}',
    save() {
        $wire.updateBlock('{{ $block['id'] }}', { url: this.url, alt: this.alt, align: this.align, width: this.width });
    }
}" class="space-y-4">

    <div>
        <label class="block text-xs text-gray-400 mb-1">URL de l'image</label>
        <input
            type="url"
            x-model="url"
            @blur="save"
            placeholder="https://example.com/image.jpg"
            class="w-full bg-gray-700 border border-gray-600 text-white text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-indigo-500"
        >
        <p class="text-xs text-gray-500 mt-1">Hébergez l'image sur imgur.com ou votre CDN</p>
    </div>

    <div x-show="url" class="rounded-lg overflow-hidden border border-gray-600">
        <img :src="url" alt="aperçu" class="w-full h-auto object-contain max-h-40">
    </div>

    <div>
        <label class="block text-xs text-gray-400 mb-1">Texte alternatif (alt)</label>
        <input
            type="text"
            x-model="alt"
            @blur="save"
            placeholder="Description de l'image"
            class="w-full bg-gray-700 border border-gray-600 text-white text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-indigo-500"
        >
    </div>

    <div>
        <label class="block text-xs text-gray-400 mb-1">Largeur</label>
        <select x-model="width" @change="save" class="w-full bg-gray-700 border border-gray-600 text-white text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-indigo-500">
            <option value="100%">100% (pleine largeur)</option>
            <option value="75%">75%</option>
            <option value="50%">50%</option>
            <option value="200px">200px</option>
            <option value="150px">150px (logo)</option>
        </select>
    </div>

    <div>
        <label class="block text-xs text-gray-400 mb-1">Alignement</label>
        <div class="flex gap-2">
            @foreach(['left' => '←', 'center' => '↔', 'right' => '→'] as $val => $icon)
                <button
                    @click="align = '{{ $val }}'; save()"
                    :class="align === '{{ $val }}' ? 'bg-indigo-600 text-white' : 'bg-gray-700 text-gray-400 hover:bg-gray-600'"
                    class="flex-1 py-1.5 rounded-lg text-sm font-medium transition-colors"
                >{{ $icon }}</button>
            @endforeach
        </div>
    </div>

</div>
