@php
    $height = $content['height'] ?? '30px';
@endphp

@if(isset($render) && $render)
    <div style="height:{{ $height }};line-height:{{ $height }};font-size:1px;">&nbsp;</div>
@else
    <div style="height:{{ $height }}; background: repeating-linear-gradient(45deg, transparent, transparent 4px, rgba(99,102,241,0.07) 4px, rgba(99,102,241,0.07) 8px); display:flex; align-items:center; justify-content:center;">
        <span style="font-size:11px; color:#9ca3af; font-family:Arial,sans-serif;">↕ Espace — {{ $height }}</span>
    </div>
@endif
