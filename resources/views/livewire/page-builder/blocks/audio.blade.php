{{-- Audio Block - Audio player --}}
@php
    $url = $content['url'] ?? '';
    $title = $content['title'] ?? '';
    $showDownload = $content['show_download'] ?? false;
    $autoplay = $content['autoplay'] ?? false;
@endphp

<div class="w-full py-4">
    @if(!empty($title))
        <h4 class="text-lg font-bold mb-3 text-center">🎵 {{ $title }}</h4>
    @endif

    <div class="bg-white/5 rounded-xl p-4 border border-white/10">
        <audio controls class="w-full" {{ $autoplay ? 'autoplay' : '' }} preload="metadata">
            <source src="{{ $url }}" type="audio/mpeg">
            Votre navigateur ne supporte pas l'élément audio.
        </audio>

        @if($showDownload && !empty($url))
            <div class="mt-3 text-center">
                <a href="{{ $url }}" download
                    class="inline-flex items-center gap-2 text-sm text-blue-400 hover:text-blue-300 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Télécharger l'audio
                </a>
            </div>
        @endif
    </div>
</div>