@php
    $color  = $content['color'] ?? '#e5e7eb';
    $margin = $content['margin'] ?? '20px';
@endphp

@if(isset($render) && $render)
    <div style="padding:0 24px;margin:{{ $margin }} 0;">
        <hr style="border:none;border-top:1px solid {{ $color }};margin:0;">
    </div>
@else
    <div style="padding: 0 24px; margin: 12px 0;">
        <hr style="border:none; border-top:1px solid {{ $color }}; margin:0;">
    </div>
@endif
