{{-- Sticky Bar Block - Fixed top/bottom bar --}}
@php
    $text = $content['text'] ?? '';
    $buttonText = $content['button_text'] ?? 'En savoir plus';
    $buttonUrl = $content['button_url'] ?? '#';
    $position = $content['position'] ?? 'top'; // top or bottom
    $backgroundColor = $styles['backgroundColor'] ?? '#1E40AF';
    $textColor = $styles['color'] ?? '#FFFFFF';
    $buttonColor = $content['button_color'] ?? '#F59E0B';

    // Attribution commerciale pour WhatsApp (si le bouton de la barre est un lien WA)
    if (!empty($buttonUrl) && str_contains($buttonUrl, 'wa.me') && !empty($commercialWhatsApp)) {
        $cleanPhone = preg_replace('/[^0-9]/', '', $commercialWhatsApp);
        $msg = $commercialWhatsAppMessage ?? ($funnel->whatsapp_message ?? 'Bonjour!');
        $buttonUrl = "https://wa.me/{$cleanPhone}?text=" . urlencode($msg);
    }
@endphp

<div class="fixed {{ $position === 'top' ? 'top-0' : 'bottom-0' }} left-0 right-0 z-50 py-3 px-4"
    style="background-color: {{ $backgroundColor }}; color: {{ $textColor }};" x-data="{ visible: true }"
    x-show="visible" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 {{ $position === 'top' ? '-translate-y-full' : 'translate-y-full' }}"
    x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 {{ $position === 'top' ? '-translate-y-full' : 'translate-y-full' }}">

    <div class="max-w-6xl mx-auto flex flex-col sm:flex-row items-center justify-center gap-3 text-center sm:text-left">
        {{-- Text --}}
        <p class="text-sm sm:text-base font-medium">{{ $text }}</p>

        {{-- Button --}}
        @if(!empty($buttonText))
            <a href="{{ $buttonUrl }}"
                class="shrink-0 px-4 py-2 rounded-lg font-bold text-sm transition-transform hover:scale-105"
                style="background-color: {{ $buttonColor }}; color: #000;">
                {{ $buttonText }}
            </a>
        @endif

        {{-- Close button --}}
        <button @click="visible = false"
            class="absolute top-1/2 -translate-y-1/2 right-4 p-1 hover:opacity-70 transition-opacity hidden sm:block">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
</div>