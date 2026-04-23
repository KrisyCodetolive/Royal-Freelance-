{{-- Copywriting Shortcuts --}}
<div class="mb-3">
    <label class="block text-xs font-medium text-gray-400 mb-2">Éléments de persuasion & Formatage</label>
    <div class="flex flex-wrap gap-1">
        <button type="button"
            wire:click="$set('content.html', $get('content.html') . '<p><strong>✓ Garantie satisfait ou remboursé 30 jours</strong></p>')"
            class="px-2 py-1 text-xs bg-green-600/20 hover:bg-green-600/40 text-green-300 rounded">
            + Garantie
        </button>
        <button type="button"
            wire:click="$set('content.html', $get('content.html') . '<p><strong>⚡ Accès immédiat</strong></p>')"
            class="px-2 py-1 text-xs bg-yellow-600/20 hover:bg-yellow-600/40 text-yellow-300 rounded">
            + Accès immédiat
        </button>
        <button type="button"
            wire:click="$set('content.html', $get('content.html') . '<p>🎁 <em>Bonus exclusif inclus</em></p>')"
            class="px-2 py-1 text-xs bg-purple-600/20 hover:bg-purple-600/40 text-purple-300 rounded">
            + Bonus
        </button>
        <button type="button"
            wire:click="$set('content.html', $get('content.html') . '\n\n- Point 1\n- Point 2\n- Point 3')"
            class="px-2 py-1 text-xs bg-indigo-600/20 hover:bg-indigo-600/40 text-indigo-300 rounded">
            + Liste à puces
        </button>
        <button type="button"
            wire:click="$set('content.html', $get('content.html') . '\n\n1. Point 1\n2. Point 2\n3. Point 3')"
            class="px-2 py-1 text-xs bg-indigo-600/20 hover:bg-indigo-600/40 text-indigo-300 rounded">
            + Liste numérotée
        </button>
    </div>
</div>

<div>
    <label class="block text-sm font-medium text-gray-300 mb-2">Texte de vente</label>
    <textarea wire:model.live.debounce.1000ms="content.html" rows="10"
        placeholder="Votre texte persuasif... (Sauts de lignes gérés automatiquement)"
        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 font-mono text-sm"></textarea>
    <div class="flex items-start gap-2 mt-2 p-2 bg-blue-500/10 border border-blue-500/20 rounded text-xs text-blue-300">
        <span>💡</span>
        <div>
            <strong>Astuce:</strong> Les sauts de ligne sont gérés et vous pouvez créer des listes en tapant
            <code>- point</code> ou <code>1. point</code>. Le texte supporte le style Markdown (!).
        </div>
    </div>
</div>

{{-- Style Options --}}
<div class="space-y-3 p-3 bg-gray-900/50 rounded-lg border border-gray-700">
    <label class="block text-xs font-medium text-gray-400">Options de style</label>

    <div class="flex items-center gap-2">
        <input type="checkbox" wire:model.live="content.bordered_box" id="text-bordered-box"
            class="rounded bg-gray-700 border-gray-600">
        <label for="text-bordered-box" class="text-sm text-gray-300">Bordure stylisée (box)</label>
    </div>

    <div>
        <label class="block text-xs font-medium text-gray-400 mb-1">Colonnes</label>
        <select wire:model.live="content.columns"
            class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-sm">
            <option value="1">1 colonne</option>
            <option value="2">2 colonnes</option>
            <option value="3">3 colonnes</option>
        </select>
    </div>
</div>