<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">Titre du formulaire</label>
    <input type="text" wire:model.live.debounce.300ms="content.title" placeholder="Titre du formulaire"
        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
</div>

<div class="space-y-4 pt-4">
    <label class="block text-sm font-medium text-gray-300">Champs du formulaire</label>
    @foreach($content['fields'] ?? [] as $index => $field)
        <div class="bg-gray-700 p-3 rounded-lg space-y-2 relative border border-gray-600">
            <button wire:click="removeFormField({{ $index }})"
                class="absolute top-2 right-2 text-red-400 hover:text-red-300 p-1">
                <x-heroicon-o-trash class="w-4 h-4" />
            </button>
            <div>
                <label class="text-xs text-gray-400">Type</label>
                <select wire:model.live="content.fields.{{ $index }}.type"
                    class="w-full px-2 py-1 bg-gray-600 border border-gray-500 rounded text-sm">
                    <option value="text">Texte</option>
                    <option value="email">Email</option>
                    <option value="tel">Téléphone</option>
                    <option value="whatsapp">WhatsApp</option>
                    <option value="number">Nombre</option>
                    <option value="textarea">Zone de texte</option>
                    <option value="select">Liste déroulante</option>
                    <option value="radio">Bouton radio (Choix unique)</option>
                    <option value="checkbox">Cases à cocher (Choix multiple)</option>
                </select>
            </div>
            <div>
                <label class="text-xs text-gray-400">Label</label>
                <input type="text" wire:model.live.debounce.300ms="content.fields.{{ $index }}.label"
                    class="w-full px-2 py-1 bg-gray-600 border border-gray-500 rounded text-sm">
            </div>
            <div>
                <label class="text-xs text-gray-400">Placeholder</label>
                <input type="text" wire:model.live.debounce.300ms="content.fields.{{ $index }}.placeholder"
                    class="w-full px-2 py-1 bg-gray-600 border border-gray-500 rounded text-sm">
            </div>
            @if(in_array($field['type'] ?? 'text', ['select', 'radio', 'checkbox']))
                <div>
                    <label class="text-xs text-gray-400">Options (séparées par des virgules)</label>
                    <input type="text" wire:model.live.debounce.300ms="content.fields.{{ $index }}.options_raw"
                        placeholder="Option 1, Option 2, Option 3"
                        class="w-full px-2 py-1 bg-gray-600 border border-gray-500 rounded text-sm">
                </div>
            @endif
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" wire:model.live="content.fields.{{ $index }}.required"
                    class="w-4 h-4 rounded bg-gray-600 border-gray-500">
                <span class="text-xs text-gray-300">Champ requis</span>
            </label>
        </div>
    @endforeach
    <button wire:click="addFormField"
        class="w-full py-2 bg-gray-700 hover:bg-gray-600 rounded-lg text-sm border border-dashed border-gray-500 transition">+
        Ajouter un champ</button>
</div>

<div class="pt-4">
    <label class="block text-sm font-medium text-gray-300 mb-1">Texte du bouton</label>
    <input type="text" wire:model.live.debounce.300ms="content.button_text" placeholder="Envoyer"
        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
</div>