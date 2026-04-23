@php
    use App\Helpers\BlockStyleHelper;
    
    $html = $content['html'] ?? '<p>Votre texte ici...</p>';
    $columns = $content['columns'] ?? 1;
    $borderedBox = $content['bordered_box'] ?? false;
    
    $defaultStyles = [
        'color' => $branding['text_color'] ?? '#FFFFFF',
        'fontSize' => '1rem',
        'lineHeight' => '1.6',
        'maxWidth' => '800px',
        'margin' => '0 auto',
    ];
    
    $textStyle = BlockStyleHelper::generateStyle($styles, $defaultStyles);
    $animation = BlockStyleHelper::getAnimationAttribute($styles);
    
    // Column layout
    $columnStyle = '';
    if ($columns > 1) {
        $columnStyle = "column-count: {$columns}; column-gap: 2rem;";
    }
    
    // Bordered box style
    $boxStyle = '';
    $wrapperStyle = '';
    if ($borderedBox) {
        $boxBorderColor = $styles['boxBorderColor'] ?? ($branding['primary_color'] ?? '#F59E0B');
        $boxBorderWidth = $styles['boxBorderWidth'] ?? '2px';
        $boxBorderRadius = $styles['boxBorderRadius'] ?? '12px';
        $boxPadding = $styles['boxPadding'] ?? '20px 30px';
        $boxStyle = "border: {$boxBorderWidth} solid {$boxBorderColor}; border-radius: {$boxBorderRadius}; padding: {$boxPadding};";
        $wrapperStyle = "display: flex; justify-content: center; align-items: center;";
    }
@endphp

@if($borderedBox)
    <div style="{{ $wrapperStyle }}">
        <div class="prose prose-invert max-w-none" 
             style="{{ $textStyle }}; {{ $columnStyle }}; {{ $boxStyle }}" 
             {!! $animation !!}>
            {!! str($html)->markdown(['renderer' => ['soft_break' => "<br>\n"]]) !!}
        </div>
    </div>
@else
    <div class="prose prose-invert max-w-none" 
         style="{{ $textStyle }}; {{ $columnStyle }}" 
         {!! $animation !!}>
        {!! str($html)->markdown(['renderer' => ['soft_break' => "<br>\n"]]) !!}
    </div>
@endif