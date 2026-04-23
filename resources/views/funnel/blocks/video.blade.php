@php
    use App\Helpers\BlockStyleHelper;
    
    $url = $content['url'] ?? '';
    $autoplay = $content['autoplay'] ?? false;
    $controls = $content['controls'] ?? true;
    $loop = $content['loop'] ?? false;
    $muted = $content['muted'] ?? false;
    $showSuggestions = $content['show_suggestions'] ?? false; // Désactivé par défaut
    $aspectRatio = $content['aspect_ratio'] ?? '16/9';
    
    // Convert YouTube/Vimeo URLs to embed with optimized parameters
    $embedUrl = $url;
    $params = [];
    
    if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $url, $matches)) {
        $embedUrl = "https://www.youtube.com/embed/{$matches[1]}";
        
        // YouTube parameters
        if ($autoplay) $params[] = 'autoplay=1';
        if ($muted) $params[] = 'mute=1';
        if ($loop) $params[] = "loop=1&playlist={$matches[1]}";
        if (!$controls) $params[] = 'controls=0';
        
        // Limiter les suggestions aux vidéos de la même chaîne ou les désactiver
        $params[] = $showSuggestions ? 'rel=1' : 'rel=0';
        
        // Améliorations intégration
        $params[] = 'modestbranding=1'; // Masquer logo YouTube
        $params[] = 'iv_load_policy=3'; // Masquer annotations
        $params[] = 'playsinline=1'; // Lecture inline sur mobile
        
        if (!empty($params)) {
            $embedUrl .= '?' . implode('&', $params);
        }
        
    } elseif (preg_match('/vimeo\.com\/(\d+)/', $url, $matches)) {
        $embedUrl = "https://player.vimeo.com/video/{$matches[1]}";
        
        // Vimeo parameters
        if ($autoplay) $params[] = 'autoplay=1';
        if ($muted) $params[] = 'muted=1';
        if ($loop) $params[] = 'loop=1';
        
        // Options Vimeo
        $params[] = 'byline=0'; // Masquer auteur
        $params[] = 'portrait=0'; // Masquer avatar
        $params[] = 'title=0'; // Masquer titre
        
        if (!empty($params)) {
            $embedUrl .= '?' . implode('&', $params);
        }
    }
    
    $defaultStyles = [
        'maxWidth' => '800px',
        'borderRadius' => '12px',
        'margin' => '0 auto',
    ];
    
    $containerStyle = BlockStyleHelper::generateStyle($styles, $defaultStyles);
    $animation = BlockStyleHelper::getAnimationAttribute($styles);
@endphp

<div class="w-full" style="{{ $containerStyle }}" {!! $animation !!}>
    @if($url)
        <div class="relative w-full overflow-hidden" 
             style="aspect-ratio: {{ $aspectRatio }}; border-radius: {{ $styles['borderRadius'] ?? '12px' }};">
            <iframe src="{{ $embedUrl }}" 
                    class="absolute inset-0 w-full h-full" 
                    frameborder="0"
                    loading="lazy"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    referrerpolicy="strict-origin-when-cross-origin"
                    allowfullscreen></iframe>
        </div>
    @else
        <div class="rounded-lg p-12 text-center" 
             style="aspect-ratio: {{ $aspectRatio }}; background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(147, 51, 234, 0.1) 100%); border: 2px dashed rgba(239, 68, 68, 0.3); display: flex; flex-direction: column; align-items: center; justify-content: center;">
            <svg class="w-16 h-16 mb-4" style="opacity: 0.3;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-gray-400 font-medium">Aucune vidéo</p>
            <p class="text-sm text-gray-500 mt-1">Ajoutez une URL YouTube ou Vimeo</p>
        </div>
    @endif
</div>