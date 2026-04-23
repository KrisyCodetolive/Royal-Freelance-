<div>
    <label class="block text-sm font-medium text-gray-300 mb-2">Plans tarifaires</label>

    @if(!empty($content['plans']))
        <div class="space-y-4 mb-4">
            @foreach($content['plans'] as $index => $plan)
                <div
                    class="bg-gray-800 p-4 rounded-lg border {{ ($plan['is_popular'] ?? false) ? 'border-blue-500' : 'border-gray-600' }}">
                    <div class="flex justify-between items-start mb-3">
                        <span class="text-sm font-bold">Plan {{ $index + 1 }}</span>
                        <button wire:click="removePlan({{ $index }})" class="text-red-400 hover:text-red-300 text-xs">
                            Supprimer
                        </button>
                    </div>

                    <div class="space-y-2">
                        <input type="text" wire:model.live.debounce.300ms="content.plans.{{ $index }}.name"
                            placeholder="Nom du plan"
                            class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-sm">

                        <div class="grid grid-cols-2 gap-2">
                            <input type="text" wire:model.live.debounce.300ms="content.plans.{{ $index }}.price"
                                placeholder="Prix (ex: 97€)"
                                class="px-3 py-2 bg-gray-700 border border-gray-600 rounded text-sm">
                            <input type="text" wire:model.live.debounce.300ms="content.plans.{{ $index }}.period"
                                placeholder="Période (ex: /mois)"
                                class="px-3 py-2 bg-gray-700 border border-gray-600 rounded text-sm">
                        </div>

                        <input type="text" wire:model.live.debounce.300ms="content.plans.{{ $index }}.old_price"
                            placeholder="Ancien prix (optionnel)"
                            class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-sm">

                        <input type="text" wire:model.live.debounce.300ms="content.plans.{{ $index }}.description"
                            placeholder="Description courte"
                            class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-sm">

                        <textarea wire:model.live.debounce.500ms="content.plans.{{ $index }}.features_text"
                            placeholder="Fonctionnalités (une par ligne)" rows="3"
                            class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-sm"></textarea>

                        <input type="text" wire:model.live.debounce.300ms="content.plans.{{ $index }}.button_text"
                            placeholder="Texte du bouton"
                            class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-sm">

                        <label class="flex items-center gap-2">
                            <input type="checkbox" wire:model.live="content.plans.{{ $index }}.is_popular"
                                class="rounded border-gray-600 text-blue-500 focus:ring-blue-500">
                            <span class="text-sm text-gray-300">⭐ Mettre en avant (populaire)</span>
                        </label>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <button wire:click="addPlan"
        class="w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 rounded-lg text-sm font-medium transition">
        + Ajouter un plan
    </button>
</div>