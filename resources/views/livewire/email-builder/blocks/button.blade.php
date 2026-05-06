@php
    $text       = $content['text'] ?? 'Cliquez ici';
    $url        = $content['url'] ?? '#';
    $align      = $content['align'] ?? 'center';
    $bgColor    = $content['bg_color'] ?? '#6366f1';
    $textColor  = $content['text_color'] ?? '#ffffff';
@endphp

@if(isset($render) && $render)
    <div style="padding:16px 24px;text-align:{{ $align }};">
        <a href="{{ $url }}" style="display:inline-block;padding:12px 28px;background-color:{{ $bgColor }};color:{{ $textColor }};font-family:Arial,sans-serif;font-size:15px;font-weight:bold;text-decoration:none;border-radius:6px;">
            {{ $text }}
        </a>
    </div>
@else
    <div style="padding: 16px 24px; text-align: {{ $align }};">
        <span style="display:inline-block; padding:12px 28px; background-color:{{ $bgColor }}; color:{{ $textColor }}; font-family:Arial,sans-serif; font-size:15px; font-weight:bold; border-radius:6px; cursor:default;">
            {{ $text }}
        </span>
    </div>
@endif
