@php
    $align = $content['align'] ?? 'left';
    $color = $content['color'] ?? '#333333';
    $text  = $content['text'] ?? '';
@endphp

@if(isset($render) && $render)
    <p style="margin:0;padding:8px 24px;font-family:Arial,sans-serif;font-size:15px;line-height:1.7;color:{{ $color }};text-align:{{ $align }};">
        {!! nl2br(e($text)) !!}
    </p>
@else
    <div style="padding: 8px 24px; text-align: {{ $align }};">
        <p style="margin:0; font-size:15px; line-height:1.7; color:{{ $color }}; font-family:Arial,sans-serif; white-space:pre-wrap;">{{ $text }}</p>
    </div>
@endif
