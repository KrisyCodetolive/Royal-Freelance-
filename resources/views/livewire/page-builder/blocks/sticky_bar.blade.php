{{-- Sticky Bar Block - PAGE BUILDER PREVIEW VERSION --}}
{{-- Cette version n'utilise PAS position:fixed pour permettre la sélection dans le builder --}}
@php
    $text = $content['text'] ?? '';
    $buttonText = $content['button_text'] ?? 'En savoir plus';
    $buttonUrl = $content['button_url'] ?? '#';
    $position = $content['position'] ?? 'top';
    $backgroundColor = $styles['backgroundColor'] ?? '#1E40AF';
    $textColor = $styles['color'] ?? '#FFFFFF';
    $buttonColor = $content['button_color'] ?? '#F59E0B';
@endphp

{{-- Preview indicator --}}
<div class="relative">
    {{-- Badge to indicate it's a sticky bar --}}
    <div class="absolute -top-6 left-0 text-xs bg-purple-600 text-white px-2 py-0.5 rounded-t">
        📌 Barre Fixe ({{ $position === 'top' ? 'Haut' : 'Bas' }})
    </div>

    {{-- Sticky bar content - NOT fixed in builder --}}
    <div class="relative py-3 px-4 border-2 border-dashed border-purple-500/50"
        style="background-color: {{ $backgroundColor }}; color: {{ $textColor }};">

        <div
            class="max-w-6xl mx-auto flex flex-col sm:flex-row items-center justify-center gap-3 text-center sm:text-left">
            {{-- Text --}}
            <p class="text-sm sm:text-base font-medium">{{ $text }}</p>

            {{-- Button --}}
            @if(!empty($buttonText))
                <span class="shrink-0 px-4 py-2 rounded-lg font-bold text-sm"
                    style="background-color: {{ $buttonColor }}; color: #000;">
                    {{ $buttonText }}
                </span>
            @endif

            {{-- Close button preview --}}
            <span class="absolute top-1/2 -translate-y-1/2 right-4 p-1 opacity-50">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </span>
        </div>
    </div>
</div>