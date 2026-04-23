@php
    $text = $content['text'] ?? 'Super produit !';
    $author = $content['author'] ?? 'Client';
    $role = $content['role'] ?? '';
    $avatar = $content['avatar'] ?? '';

    $style = collect([
        'background-color' => $styles['backgroundColor'] ?? '#1F2937',
        'padding' => $styles['padding'] ?? '20px',
        'border-radius' => $styles['borderRadius'] ?? '12px',
        'max-width' => $styles['maxWidth'] ?? '500px',
        'margin' => $styles['margin'] ?? '0 auto',
    ])->filter()->map(fn($v, $k) => "$k: $v")->implode('; ');
@endphp

<div class="text-center" style="{{ $style }}">
    <div class="flex flex-col items-center gap-4">
        <div class="text-4xl">⭐⭐⭐⭐⭐</div>

        <p class="text-lg italic" style="color: {{ $branding['text_color'] ?? '#FFFFFF' }}">
            "{{ $text }}"
        </p>

        <div class="flex items-center gap-3">
            @if($avatar)
                <img src="{{ $avatar }}" alt="{{ $author }}" class="w-12 h-12 rounded-full object-cover">
            @else
                <div
                    class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center text-white font-bold">
                    {{ substr($author, 0, 1) }}
                </div>
            @endif
            <div class="text-left">
                <p class="font-semibold" style="color: {{ $branding['text_color'] ?? '#FFFFFF' }}">{{ $author }}</p>
                @if($role)
                    <p class="text-sm text-gray-400">{{ $role }}</p>
                @endif
            </div>
        </div>
    </div>
</div>