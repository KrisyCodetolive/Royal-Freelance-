<div x-data="{
    text:       '{{ addslashes($block['content']['text'] ?? 'Cliquez ici') }}',
    url:        '{{ addslashes($block['content']['url'] ?? '#') }}',
    align:      '{{ $block['content']['align'] ?? 'center' }}',
    bg_color:   '{{ $block['content']['bg_color'] ?? '#6366f1' }}',
    text_color: '{{ $block['content']['text_color'] ?? '#ffffff' }}',
    save() {
        $wire.updateBlock('{{ $block['id'] }}', {
            text: this.text, url: this.url, align: this.align,
            bg_color: this.bg_color, text_color: this.text_color
        });
    }
}" class="space-y-4">

    {{-- Aperçu du bouton --}}
    <div class="text-center py-3">
        <span
            :style="`background-color: ${bg_color}; color: ${text_color};`"
            class="inline-block px-6 py-2.5 rounded-lg font-bold text-sm"
            x-text="text"
        ></span>
    </div>

    <div>
        <label class="block text-xs text-gray-400 mb-1">Texte du bouton</label>
        <input
            type="text"
            x-model="text"
            @blur="save"
            class="w-full bg-gray-700 border border-gray-600 text-white text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-indigo-500"
        >
    </div>

    <div>
        <label class="block text-xs text-gray-400 mb-1">Lien URL</label>
        <input
            type="text"
            x-model="url"
            @blur="save"
            placeholder="https://example.com"
            class="w-full bg-gray-700 border border-gray-600 text-white text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-indigo-500"
        >
    </div>

    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="block text-xs text-gray-400 mb-1">Fond</label>
            <div class="flex items-center gap-2">
                <input type="color" x-model="bg_color" @change="save" class="w-8 h-8 rounded cursor-pointer border-0 bg-transparent">
                <input type="text" x-model="bg_color" @blur="save" class="flex-1 bg-gray-700 border border-gray-600 text-white text-xs rounded-lg px-2 py-1.5 focus:outline-none focus:border-indigo-500">
            </div>
        </div>
        <div>
            <label class="block text-xs text-gray-400 mb-1">Texte</label>
            <div class="flex items-center gap-2">
                <input type="color" x-model="text_color" @change="save" class="w-8 h-8 rounded cursor-pointer border-0 bg-transparent">
                <input type="text" x-model="text_color" @blur="save" class="flex-1 bg-gray-700 border border-gray-600 text-white text-xs rounded-lg px-2 py-1.5 focus:outline-none focus:border-indigo-500">
            </div>
        </div>
    </div>

    {{-- Couleurs rapides --}}
    <div>
        <label class="block text-xs text-gray-400 mb-1">Couleurs rapides</label>
        <div class="flex gap-2 flex-wrap">
            @foreach(['#6366f1' => 'Indigo', '#ef4444' => 'Rouge', '#10b981' => 'Vert', '#f59e0b' => 'Orange', '#3b82f6' => 'Bleu', '#1f2937' => 'Noir'] as $hex => $name)
                <button
                    @click="bg_color = '{{ $hex }}'; save()"
                    class="w-7 h-7 rounded-full border-2 border-gray-600 hover:border-white transition-colors"
                    style="background-color: {{ $hex }};"
                    title="{{ $name }}"
                ></button>
            @endforeach
        </div>
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
