@php
    use App\Helpers\BlockStyleHelper;
    
    $url = $content['url'] ?? '';
    $alt = $content['alt'] ?? '';
    $caption = $content['caption'] ?? '';
    $overlay = $content['overlay'] ?? false;
    $overlayColor = $content['overlay_color'] ?? 'rgba(0,0,0,0.5)';
    $filter = $content['filter'] ?? 'none'; // none, grayscale, sepia, blur
    $hoverEffect = $content['hover_effect'] ?? 'zoom'; // zoom, brightness, none
    $aspectRatio = $content['aspect_ratio'] ?? 'auto'; // auto, 16/9, 4/3, 1/1
    
    $defaultStyles = [
        'maxWidth' => '100%',
        'border-radius' => $styles['borderRadius'] ?? '8px',
        'margin' => $styles['margin'] ?? '0 auto',
        'borderRadius' => '8px',
        'margin' => '20px auto',
    ];
    
    $containerStyle = BlockStyleHelper::generateStyle($styles, $defaultStyles);
    $animation = BlockStyleHelper::getAnimationAttribute($styles);
    
    // Image filters
    $filterStyle = match($filter) {
        'grayscale' => 'filter: grayscale(100%);',
        'sepia' => 'filter: sepia(80%);',
        'blur' => 'filter: blur(2px);',
        default => '',
    };
    
    // Aspect ratio container
    $aspectStyle = '';
    if ($aspectRatio !== 'auto') {
        $aspectStyle = "aspect-ratio: {$aspectRatio}; overflow: hidden;";
    }
    
    // Hover effect class
    $hoverClass = match($hoverEffect) {
        'zoom' => 'image-hover-zoom',
        'brightness' => 'image-hover-brightness',
        default => '',
    };
@endphp

<style>
    .image-hover-zoom img {
        transition: transform 0.3s ease;
    }
    .image-hover-zoom:hover img {
        transform: scale(1.05);
    }
    .image-hover-brightness img {
        transition: filter 0.3s ease;
    }
    .image-hover-brightness:hover img {
        filter: brightness(1.2);
    }
</style>

<div class="text-center">
    @if($url)
        <figure style="{{ $containerStyle }}; margin: 0; position: relative;" {!! $animation !!} class="{{ $hoverClass }}">
            <div style="{{ $aspectStyle }}">
                <img src="{{ $url }}" 
                     alt="{{ $alt }}" 
                     style="width: 100%; height: 100%; object-fit: cover; display: block; {{ $filterStyle }}">
                
                @if($overlay)
                    <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: {{ $overlayColor }}; pointer-events: none;"></div>
                @endif
            </div>
            
            @if($caption)
                <figcaption style="text-align: center; font-size: 0.875rem; margin-top: 12px; opacity: 0.8; padding: 0 1rem;">
                    {{ $caption }}
                </figcaption>
            @endif
        </figure>
    @else
        <div style="padding: 60px 40px; text-align: center; background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(147, 51, 234, 0.1) 100%); border: 2px dashed rgba(59, 130, 246, 0.3); border-radius: 12px; margin: 20px auto; max-width: 600px;">
            <svg class="mx-auto mb-4" width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="opacity: 0.3; margin: 0 auto 1rem;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <p style="opacity: 0.5; margin: 0;">Aucune image sélectionnée</p>
        </div>
    @endif
</div>