{{-- Presets Marketing --}}
<div>
    <label class="block text-xs font-medium text-gray-400 mb-2">Templates de titre</label>
    <div class="grid grid-cols-1 gap-1">
        <button type="button" wire:click="$set('content.text', 'Découvrez Comment [Résultat] en Seulement [Temps]')"
            class="px-3 py-2 text-xs bg-gray-700 hover:bg-gray-600 rounded text-left transition">
            "Découvrez Comment..."
        </button>
        <button type="button"
            wire:click="$set('content.text', '[Nombre] Secrets Pour [Résultat] Que Personne Ne Vous Dit')"
            class="px-3 py-2 text-xs bg-gray-700 hover:bg-gray-600 rounded text-left transition">
            "[X] Secrets Pour..."
        </button>
        <button type="button" wire:click="$set('content.text', 'La Méthode Complète Pour [Résultat] Sans [Objection]')"
            class="px-3 py-2 text-xs bg-gray-700 hover:bg-gray-600 rounded text-left transition">
            "La Méthode Complète..."
        </button>
    </div>
</div>

<div>
    <label class="block text-sm font-medium text-gray-300 mb-2">Votre titre</label>
    <textarea wire:model.live.debounce.500ms="content.text" rows="3" placeholder="Un titre qui capte l'attention..."
        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
    <p class="text-xs text-gray-400 mt-1">💡 Les sauts de ligne sont gérés automatiquement</p>
</div>

<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">Niveau</label>
    <select wire:model.live="content.level"
        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
        <option value="h1">H1 - Titre principal</option>
        <option value="h2">H2 - Sous-titre</option>
        <option value="h3">H3 - Titre tertiaire</option>
    </select>
</div>

<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">Alignement</label>
    <div class="flex gap-2">
        @foreach(['left' => 'Gauche', 'center' => 'Centre', 'right' => 'Droite'] as $value => $label)
            <button wire:click="$set('content.alignment', '{{ $value }}')"
                class="flex-1 py-2 px-3 text-sm rounded-lg transition {{ ($content['alignment'] ?? 'center') === $value ? 'bg-blue-600' : 'bg-gray-700 hover:bg-gray-600' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>
</div>

{{-- Style Options --}}
<div class="space-y-3 p-3 bg-gray-900/50 rounded-lg border border-gray-700">
    <label class="block text-xs font-medium text-gray-400">Options de style</label>

    <div class="flex items-center gap-2">
        <input type="checkbox" wire:model.live="content.bordered_box" id="title-bordered-box"
            class="rounded bg-gray-700 border-gray-600">
        <label for="title-bordered-box" class="text-sm text-gray-300">Bordure stylisée (box)</label>
    </div>

    <div class="flex items-center gap-2">
        <input type="checkbox" wire:model.live="content.gradient" id="title-gradient"
            class="rounded bg-gray-700 border-gray-600">
        <label for="title-gradient" class="text-sm text-gray-300">Texte dégradé (gradient)</label>
    </div>

    <div class="flex items-center gap-2">
        <input type="checkbox" wire:model.live="content.underline" id="title-underline"
            class="rounded bg-gray-700 border-gray-600">
        <label for="title-underline" class="text-sm text-gray-300">Soulignement</label>
    </div>
</div>