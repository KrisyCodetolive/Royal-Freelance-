<div x-data="{
    text:  '{{ addslashes($block['content']['text'] ?? '') }}',
    level: '{{ $block['content']['level'] ?? 'h2' }}',
    align: '{{ $block['content']['align'] ?? 'center' }}',
    color: '{{ $block['content']['color'] ?? '#1a1a1a' }}',
    save() {
        $wire.updateBlock('{{ $block['id'] }}', { text: this.text, level: this.level, align: this.align, color: this.color });
    }
}" class="space-y-4">

    <div>
        <label class="block text-xs text-gray-400 mb-1">Texte</label>
        <textarea
            x-model="text"
            @blur="save"
            rows="3"
            class="w-full bg-gray-700 border border-gray-600 text-white text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-indigo-500 resize-none"
        ></textarea>
    </div>

    <div>
        <label class="block text-xs text-gray-400 mb-1">Niveau</label>
        <select x-model="level" @change="save" class="w-full bg-gray-700 border border-gray-600 text-white text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-indigo-500">
            <option value="h1">H1 — Grand titre</option>
            <option value="h2">H2 — Titre moyen</option>
            <option value="h3">H3 — Sous-titre</option>
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

    <div>
        <label class="block text-xs text-gray-400 mb-1">Couleur</label>
        <div class="flex items-center gap-2">
            <input type="color" x-model="color" @change="save" class="w-8 h-8 rounded cursor-pointer border-0 bg-transparent">
            <input type="text" x-model="color" @blur="save" class="flex-1 bg-gray-700 border border-gray-600 text-white text-sm rounded-lg px-3 py-1.5 focus:outline-none focus:border-indigo-500">
        </div>
    </div>

</div>
