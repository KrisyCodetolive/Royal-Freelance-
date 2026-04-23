@php
    $title = $content['title'] ?? '';
    $text = $content['text'] ?? '';
    $imageUrl = $content['image_url'] ?? '';
    $position = $content['image_position'] ?? 'right'; // left, right
@endphp

<div class="w-full"
    style="padding: {{ $styles['padding'] ?? '40px 20px' }}; background-color: {{ $styles['backgroundColor'] ?? 'transparent' }}">
    <div
        class="container mx-auto flex flex-col md:flex-row items-center gap-12 {{ $position === 'left' ? 'md:flex-row-reverse' : '' }}">

        {{-- Text Content --}}
        <div class="flex-1 space-y-4">
            @if($title)
                <h2 class="text-3xl font-bold mb-4" style="color: {{ $branding['text_color'] ?? 'inherit' }}">
                    {{ $title }}
                </h2>
            @endif

            <div class="prose max-w-none" style="color: {{ $branding['text_color'] ?? 'inherit' }}">
                {!! $text !!}
            </div>
        </div>

        {{-- Image --}}
        <div class="flex-1 w-full relative">
            @if($imageUrl)
                <img src="{{ $imageUrl }}" alt="Illustration" class="w-full h-auto rounded-xl shadow-lg object-cover">
            @else
                <div
                    class="w-full aspect-video bg-gray-800 border-2 border-dashed border-gray-600 rounded-xl flex items-center justify-center">
                    <span class="text-gray-400 flex flex-col items-center">
                        <x-heroicon-o-photo class="w-12 h-12 mb-2" />
                        Ajouter une image
                    </span>
                </div>
            @endif
        </div>

    </div>
</div>