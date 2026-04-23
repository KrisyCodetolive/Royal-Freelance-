{{-- Icon Box Block - Icon + Title + Text --}}
@php
    $icon = $content['icon'] ?? '✨';
    $title = $content['title'] ?? 'Titre';
    $text = $content['text'] ?? '';
    $alignment = $content['alignment'] ?? 'center';

    $padding = $styles['padding'] ?? '24px';
    $backgroundColor = $styles['backgroundColor'] ?? 'rgba(255,255,255,0.05)';
    $borderRadius = $styles['borderRadius'] ?? '16px';
@endphp

<div class="icon-box"
    style="padding: {{ $padding }}; background-color: {{ $backgroundColor }}; border-radius: {{ $borderRadius }}; text-align: {{ $alignment }};">

    {{-- Icon --}}
    <div class="text-4xl mb-3">
        @if(str_starts_with($icon, 'heroicon-'))
            <x-dynamic-component :component="$icon" class="w-12 h-12 mx-auto text-blue-400" />
        @else
            {!! $icon !!}
        @endif
    </div>

    {{-- Title --}}
    <h4 class="text-lg font-bold mb-2">{{ $title }}</h4>

    {{-- Text --}}
    @if(!empty($text))
        <p class="text-sm text-gray-400">{!! $text !!}</p>
    @endif
</div>