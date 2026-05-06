@php
    $tag   = $content['level'] ?? 'h2';
    $align = $content['align'] ?? 'center';
    $color = $content['color'] ?? '#1a1a1a';
    $text  = $content['text'] ?? '';

    $sizes = ['h1' => '28px', 'h2' => '22px', 'h3' => '18px'];
    $size  = $sizes[$tag] ?? '22px';
@endphp

@if(isset($render) && $render)
    <{{ $tag }} style="margin:0;padding:16px 24px;font-family:Arial,sans-serif;font-size:{{ $size }};font-weight:bold;color:{{ $color }};text-align:{{ $align }};">
        {!! $text !!}
    </{{ $tag }}>
@else
    <div style="padding: 12px 24px; text-align: {{ $align }};">
        <{{ $tag }} style="margin:0; font-size:{{ $size }}; font-weight:bold; color:{{ $color }}; font-family: Arial, sans-serif;">
            {!! nl2br(e($text)) !!}
        </{{ $tag }}>
    </div>
@endif
