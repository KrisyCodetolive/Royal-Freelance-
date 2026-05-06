<div x-data="{
    height: '{{ $block['content']['height'] ?? '30px' }}',
    save() {
        $wire.updateBlock('{{ $block['id'] }}', { height: this.height });
    }
}" class="space-y-4">

    <div>
        <label class="block text-xs text-gray-400 mb-1">Hauteur de l'espace</label>
        <select x-model="height" @change="save" class="w-full bg-gray-700 border border-gray-600 text-white text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-indigo-500">
            <option value="10px">Très petit (10px)</option>
            <option value="20px">Petit (20px)</option>
            <option value="30px">Normal (30px)</option>
            <option value="40px">Grand (40px)</option>
            <option value="60px">Très grand (60px)</option>
        </select>
    </div>

    <div class="text-xs text-gray-500 text-center p-3 bg-gray-700 rounded-lg">
        L'espace ajoute du vide vertical entre les blocs.
    </div>

</div>
