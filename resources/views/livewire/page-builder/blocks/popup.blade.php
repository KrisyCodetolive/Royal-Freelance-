{{-- Popup Block - PAGE BUILDER PREVIEW VERSION --}}
{{-- Cette version affiche le popup en mode "preview statique" pour permettre la sélection dans le builder --}}
@php
    $title = $content['title'] ?? 'Popup';
    $body = $content['body'] ?? '';
    $buttonText = $content['button_text'] ?? 'Fermer';
    $buttonUrl = $content['button_url'] ?? null;
    $trigger = $content['trigger'] ?? 'exit_intent';
    $delay = $content['delay'] ?? 5000;
    $scrollPercent = $content['scroll_percent'] ?? 50;
    $image = $content['image'] ?? null;

    $backgroundColor = $styles['backgroundColor'] ?? '#1F2937';

    $triggerLabel = match ($trigger) {
        'exit_intent' => 'Intent de sortie',
        'delay' => 'Après ' . ($delay / 1000) . 's',
        'scroll' => 'Scroll ' . $scrollPercent . '%',
        default => 'Exit intent'
    };
@endphp

{{-- Preview indicator --}}
<div class="relative">
    {{-- Badge to indicate it's a popup --}}
    <div class="absolute -top-6 left-0 text-xs bg-amber-600 text-white px-2 py-0.5 rounded-t flex items-center gap-1">
        🪟 Popup Modal ({{ $triggerLabel }})
    </div>

    {{-- Popup preview - NOT as overlay in builder --}}
    <div class="relative rounded-2xl p-6 shadow-2xl border-2 border-dashed border-amber-500/50"
        style="background-color: {{ $backgroundColor }};">

        {{-- Close button preview --}}
        <span class="absolute top-4 right-4 p-2 bg-white/10 rounded-full opacity-50">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </span>

        {{-- Image --}}
        @if(!empty($image))
            <img src="{{ $image }}" alt="" class="w-full h-32 object-cover rounded-xl mb-4">
        @else
            <div class="w-full h-24 bg-gray-700 rounded-xl mb-4 flex items-center justify-center text-gray-500 text-sm">
                📷 Image optionnelle
            </div>
        @endif

        {{-- Title --}}
        <h3 class="text-xl font-bold text-center mb-3">{{ $title }}</h3>

        {{-- Body --}}
        @if(!empty($body))
            <div class="text-center text-gray-300 mb-4 text-sm">
                {!! $body !!}
            </div>
        @else
            <div class="text-center text-gray-500 mb-4 text-sm italic">
                Contenu du popup...
            </div>
        @endif

        {{-- Button --}}
        @if(!empty($buttonText))
            <span class="block w-full py-2 px-4 bg-blue-600 text-white font-bold text-center rounded-lg text-sm">
                {{ $buttonText }}
            </span>
        @endif
    </div>

    {{-- Info about trigger --}}
    <div class="mt-2 text-xs text-gray-500 text-center">
        ℹ️ Ce popup apparaîtra : {{ $triggerLabel }}
    </div>
</div>