{{-- Embed Block - Custom HTML/Embed code --}}
@php
    $html = $content['html'] ?? '';
    $maxWidth = $styles['maxWidth'] ?? '100%';
    $aspectRatio = $content['aspect_ratio'] ?? null; // 16:9, 4:3, 1:1, etc.
@endphp

<div class="w-full py-4" style="max-width: {{ $maxWidth }}; margin: 0 auto;">
    @if(!empty($aspectRatio))
        @php
            [$width, $height] = explode(':', $aspectRatio);
            $paddingPercent = ($height / $width) * 100;
        @endphp
        <div class="relative w-full" style="padding-bottom: {{ $paddingPercent }}%;">
            <div class="absolute inset-0">
                {!! $html !!}
            </div>
        </div>
    @else
        <div class="embed-container">
            {!! $html !!}
        </div>
    @endif
</div>