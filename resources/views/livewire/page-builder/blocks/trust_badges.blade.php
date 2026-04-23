{{-- Trust Badges Block --}}
@php
    $badges = $content['badges'] ?? [];
    $layout = $content['layout'] ?? 'horizontal'; // horizontal, vertical, grid
@endphp

<div class="py-6">
    <div class="flex {{ $layout === 'vertical' ? 'flex-col' : 'flex-wrap' }} justify-center gap-6 items-center">
        @foreach($badges as $badge)
            <div class="flex items-center gap-2 {{ $layout === 'grid' ? 'bg-white/5 px-4 py-2 rounded-lg' : '' }}">
                <span class="text-xl">{{ $badge['icon'] ?? '✓' }}</span>
                <span class="text-sm text-gray-300">{{ $badge['text'] ?? '' }}</span>
            </div>
        @endforeach
    </div>
</div>