<div x-data="{
    color:  '{{ $block['content']['color'] ?? '#e5e7eb' }}',
    margin: '{{ $block['content']['margin'] ?? '20px' }}',
    save() {
        $wire.updateBlock('{{ $block['id'] }}', { color: this.color, margin: this.margin });
    }
}" class="space-y-4">

    <div>
        <label class="block text-xs text-gray-400 mb-1">Couleur de la ligne</label>
        <div class="flex items-center gap-2">
            <input type="color" x-model="color" @change="save" class="w-8 h-8 rounded cursor-pointer border-0 bg-transparent">
            <input type="text" x-model="color" @blur="save" class="flex-1 bg-gray-700 border border-gray-600 text-white text-sm rounded-lg px-3 py-1.5 focus:outline-none focus:border-indigo-500">
        </div>
    </div>

    <div>
        <label class="block text-xs text-gray-400 mb-1">Marge verticale</label>
        <select x-model="margin" @change="save" class="w-full bg-gray-700 border border-gray-600 text-white text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-indigo-500">
            <option value="10px">Petite (10px)</option>
            <option value="20px">Normale (20px)</option>
            <option value="30px">Grande (30px)</option>
            <option value="40px">Très grande (40px)</option>
        </select>
    </div>

    <div class="py-2" :style="`border-top: 1px solid ${color};`"></div>

</div>
