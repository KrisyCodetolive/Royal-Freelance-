{{-- CTA Templates --}}
<div>
    <label class="block text-xs font-medium text-gray-400 mb-2">CTA à haute conversion</label>
    <div class="grid grid-cols-1 gap-1">
        <button type="button" wire:click="$set('content.text', 'JE VEUX MON ACCÈS MAINTENANT ➜')"
            class="px-3 py-2 text-xs bg-green-600 hover:bg-green-700 rounded text-left transition font-bold">
            "JE VEUX MON ACCÈS MAINTENANT"
        </button>
        <button type="button" wire:click="$set('content.text', 'OUI, JE RÉSERVE MA PLACE 🔥')"
            class="px-3 py-2 text-xs bg-orange-600 hover:bg-orange-700 rounded text-left transition font-bold">
            "OUI, JE RÉSERVE MA PLACE"
        </button>
        <button type="button" wire:click="$set('content.text', 'OBTENIR [RÉSULTAT] AUJOURD\'HUI')"
            class="px-3 py-2 text-xs bg-blue-600 hover:bg-blue-700 rounded text-left transition font-bold">
            "OBTENIR [RÉSULTAT] AUJOURD'HUI"
        </button>
        <button type="button" wire:click="$set('content.text', 'COMMENCER GRATUITEMENT ✓')"
            class="px-3 py-2 text-xs bg-gray-700 hover:bg-gray-600 rounded text-left transition">
            "COMMENCER GRATUITEMENT"
        </button>
    </div>
</div>

<div>
    <label class="block text-sm font-medium text-gray-300 mb-2">Texte du bouton</label>
    <input type="text" wire:model.live.debounce.300ms="content.text" placeholder="Cliquer ici"
        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
    <p class="text-xs text-gray-400 mt-1">💡 CTA 1ère personne convertissent +90% mieux</p>
</div>

{{-- Button Size Selector --}}
<div class="p-3 bg-gray-900/50 rounded-lg border border-gray-700">
    <label class="block text-xs font-medium text-gray-400 mb-2">Taille du bouton</label>
    <div class="flex gap-2">
        @foreach(['small' => 'Petit', 'medium' => 'Moyen', 'large' => 'Grand'] as $sizeValue => $sizeLabel)
            <button type="button" wire:click="$set('content.size', '{{ $sizeValue }}')"
                class="flex-1 py-2 px-3 text-sm rounded-lg transition {{ ($content['size'] ?? 'medium') === $sizeValue ? 'bg-blue-600' : 'bg-gray-700 hover:bg-gray-600' }}">
                {{ $sizeLabel }}
            </button>
        @endforeach
    </div>
</div>

{{-- Action Type Selector --}}
<div class="p-3 bg-gray-900/50 rounded-lg border border-gray-700 space-y-3">
    <label class="block text-xs font-medium text-gray-400">Type d'action</label>

    <select wire:model.live="content.action_type"
        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
        <option value="url">🌐 Lien externe ou URL</option>
        <option value="page">📄 Page du tunnel</option>
        <option value="whatsapp">💬 Discussion WhatsApp</option>
        <option value="whatsapp_group">👥 Groupe WhatsApp</option>
        <option value="shop">🛒 Commande/Boutique</option>
    </select>

    @php
        $actionType = $content['action_type'] ?? 'url';
        $tunnelPages = $block->page->funnel->pages ?? collect();
        $funnel = $block->page->funnel;
    @endphp

    {{-- URL Externe --}}
    @if($actionType === 'url')
        <div>
            <label class="block text-xs font-medium text-gray-400 mb-1">URL de destination</label>
            <input type="url" wire:model.live.debounce.300ms="content.url" placeholder="https://..."
                class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-sm focus:ring-2 focus:ring-blue-500">
        </div>
    @endif

    {{-- Page du Tunnel --}}
    @if($actionType === 'page')
        <div>
            <label class="block text-xs font-medium text-gray-400 mb-1">Page du tunnel</label>
            <select wire:model.live="content.page_id"
                class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-sm">
                <option value="">-- Sélectionner une page --</option>
                @foreach($tunnelPages as $funnelPage)
                    <option value="{{ $funnelPage->id }}">{{ $funnelPage->title }}</option>
                @endforeach
            </select>
            <p class="text-xs text-gray-500 mt-1">Redirige vers une autre page de ce tunnel</p>
        </div>
    @endif

    {{-- WhatsApp Discussion --}}
    @if($actionType === 'whatsapp')
        <div class="space-y-2">
            @if($funnel->whatsapp_url)
                <div class="p-2 bg-green-900/20 border border-green-700 rounded">
                    <p class="text-xs text-green-400 font-medium mb-1">📱 Configuration WhatsApp du tunnel</p>
                    <p class="text-xs text-gray-300">Numéro: {{ $funnel->whatsapp_url }}</p>
                    @if($funnel->whatsapp_message)
                        <p class="text-xs text-gray-300">Message: {{ $funnel->whatsapp_message }}</p>
                    @endif
                </div>
            @endif

            <div>
                <label class="block text-xs font-medium text-gray-400 mb-1">Numéro WhatsApp</label>
                <input type="tel" wire:model.live.debounce.300ms="content.whatsapp_number"
                    placeholder="{{ $funnel->whatsapp_url ?: '+33612345678' }}"
                    value="{{ $content['whatsapp_number'] ?? $funnel->whatsapp_url }}"
                    class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-sm focus:ring-2 focus:ring-blue-500">
                <p class="text-xs text-gray-500 mt-1">
                    @if($funnel->whatsapp_url)
                        Utilisé si aucun commercial n'est attribué
                    @else
                        Format international avec +
                    @endif
                    <br>💡 Le numéro du commercial sera utilisé automatiquement s'il existe.
                </p>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-400 mb-1">Message pré-rempli (optionnel)</label>
                <textarea wire:model.live.debounce.300ms="content.whatsapp_message" rows="2"
                    placeholder="{{ $funnel->whatsapp_message ?: 'Bonjour, je suis intéressé par...' }}"
                    class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-sm focus:ring-2 focus:ring-blue-500">{{ $content['whatsapp_message'] ?? $funnel->whatsapp_message }}</textarea>
            </div>
        </div>
    @endif

    {{-- WhatsApp Groupe --}}
    @if($actionType === 'whatsapp_group')
        <div>
            <label class="block text-xs font-medium text-gray-400 mb-1">Lien d'invitation du groupe</label>
            <input type="url" wire:model.live.debounce.300ms="content.whatsapp_group_url"
                placeholder="https://chat.whatsapp.com/..."
                class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-sm focus:ring-2 focus:ring-blue-500">
            <p class="text-xs text-gray-500 mt-1">Copiez le lien d'invitation depuis WhatsApp</p>
        </div>
    @endif

    {{-- Boutique/Commande --}}
    @if($actionType === 'shop')
        <div>
            @if($funnel->payment_url)
                <div class="p-2 bg-blue-900/20 border border-blue-700 rounded mb-2">
                    <p class="text-xs text-blue-400 font-medium mb-1">💳 URL de paiement du tunnel</p>
                    <p class="text-xs text-gray-300">{{ $funnel->payment_url }}</p>
                </div>
            @endif

            <label class="block text-xs font-medium text-gray-400 mb-1">Lien de commande</label>
            <input type="url" wire:model.live.debounce.300ms="content.shop_url"
                placeholder="{{ $funnel->payment_url ?: 'https://votre-boutique.com/produit' }}"
                value="{{ $content['shop_url'] ?? $funnel->payment_url }}"
                class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-sm focus:ring-2 focus:ring-blue-500">
            <p class="text-xs text-gray-500 mt-1">
                @if($funnel->payment_url)
                    Utilise l'URL de paiement du tunnel par défaut
                @else
                    Lien vers votre page de paiement ou boutique
                @endif
            </p>
        </div>
    @endif
</div>

<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">Ouvrir dans</label>
    <select wire:model.live="content.target"
        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
        <option value="_self">Même onglet</option>
        <option value="_blank">Nouvel onglet</option>
    </select>
</div>

<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">Icône</label>
    <select wire:model.live="content.icon"
        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
        <option value="">Aucune</option>
        <option value="whatsapp">WhatsApp</option>
        <option value="arrow">Flèche</option>
        <option value="check">Check</option>
        <option value="arrow-right">Flèche droite</option>
        <option value="shopping-cart">Panier</option>
        <option value="users">Groupe</option>
    </select>
</div>