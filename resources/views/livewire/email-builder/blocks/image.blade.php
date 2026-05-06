@php
    $url   = $content['url'] ?? '';
    $alt   = $content['alt'] ?? '';
    $align = $content['align'] ?? 'center';
    $width = $content['width'] ?? '100%';
@endphp

@if(isset($render) && $render)
    <div style="padding:8px 24px;text-align:{{ $align }};">
        @if($url)
            <img src="{{ $url }}" alt="{{ $alt }}" style="max-width:{{ $width }};height:auto;display:inline-block;">
        @endif
    </div>
@else
    <div style="padding: 8px 24px; text-align: {{ $align }};">
        @if($url)
            <img src="{{ $url }}" alt="{{ $alt }}" style="max-width:{{ $width }}; height:auto; display:inline-block;">
        @else
            <div style="background:#f3f4f6; border:2px dashed #d1d5db; border-radius:8px; padding:32px; text-align:center; color:#9ca3af; font-family:Arial,sans-serif; font-size:14px;">
                🖼 Image (URL à définir dans le panneau)
            </div>
        @endif
    </div>
@endif
