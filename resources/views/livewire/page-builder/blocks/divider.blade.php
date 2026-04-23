@php
    $style = collect([
        'border-color' => $styles['borderColor'] ?? '#374151',
        'border-width' => $styles['borderWidth'] ?? '1px',
        'margin' => $styles['margin'] ?? '20px 0',
    ])->filter()->map(fn($v, $k) => "$k: $v")->implode('; ');
@endphp

<hr style="{{ $style }}; border-style: solid; border-top: none;">