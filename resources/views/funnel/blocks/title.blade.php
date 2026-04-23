@php
    use App\Helpers\BlockStyleHelper;

    $text = $content['text'] ?? 'Titre';
    $level = $content['level'] ?? 'h1';
    $alignment = $content['alignment'] ?? 'center';
    $gradient = $content['gradient'] ?? false;
    $underline = $content['underline'] ?? false;
    $borderedBox = $content['bordered_box'] ?? false;

    // Default sizes per level
    $defaultSize = match ($level) {
        'h1' => '3rem',
        'h2' => '2.5rem',
        'h3' => '2rem',
        'h4' => '1.75rem',
        'h5' => '1.5rem',
        'h6' => '1.25rem',
        default => '3rem',
    };

    $defaultStyles = [
        'color' => $branding['text_color'] ?? '#FFFFFF',
        'fontSize' => $defaultSize,
        'fontWeight' => 'bold',
        'margin' => '20px 0',
        'textAlign' => $alignment,
        'lineHeight' => '1.2',
    ];

    $titleStyle = BlockStyleHelper::generateStyle($styles, $defaultStyles);
    $animation = BlockStyleHelper::getAnimationAttribute($styles);

    // Gradient text
    if ($gradient) {
        $gradientColors = $styles['gradientColors'] ?? 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
        $titleStyle .= "; background: {$gradientColors}; -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text";
    }

    // Container for underline effect
    $containerClass = '';
    $underlineStyle = '';
    if ($underline) {
        $underlineColor = $styles['underlineColor'] ?? ($branding['primary_color'] ?? '#3B82F6');
        $underlineStyle = "display: inline-block; position: relative; padding-bottom: 10px;";
        $afterStyle = "content: ''; position: absolute; bottom: 0; left: 0; width: 100%; height: 4px; background: {$underlineColor}; border-radius: 2px;";
    }

    // Bordered box style
    $boxStyle = '';
    if ($borderedBox) {
        $boxBorderColor = $styles['boxBorderColor'] ?? ($branding['primary_color'] ?? '#F59E0B');
        $boxBorderWidth = $styles['boxBorderWidth'] ?? '2px';
        $boxBorderRadius = $styles['boxBorderRadius'] ?? '12px';
        $boxPadding = $styles['boxPadding'] ?? '20px 30px';
        $boxStyle = "border: {$boxBorderWidth} solid {$boxBorderColor}; border-radius: {$boxBorderRadius}; padding: {$boxPadding}; display: inline-block; width: auto;";
    }
@endphp

@if($borderedBox)
    <div style="text-align: {{ $alignment }}; margin: {{ $styles['margin'] ?? '20px 0' }};">
        <div style="{{ $boxStyle }}">
            @if($underline)
                <{{ $level }} style="{{ $titleStyle }}; {{ $underlineStyle }}; margin: 0;" {!! $animation !!}>
                    {!! nl2br(e($text)) !!}
                    <span style="{{ $afterStyle }}"></span>
                </{{ $level }}>
            @else
                <{{ $level }} style="{{ $titleStyle }}; margin: 0;" {!! $animation !!}>
                    {!! nl2br(e($text)) !!}
                </{{ $level }}>
            @endif
        </div>
    </div>
@elseif($underline)
    <div style="text-align: {{ $alignment }}; margin: {{ $styles['margin'] ?? '20px 0' }};">
        <{{ $level }} style="{{ $titleStyle }}; {{ $underlineStyle }}" {!! $animation !!}>
            {!! nl2br(e($text)) !!}
            <span style="{{ $afterStyle }}"></span>
        </{{ $level }}>
    </div>
@else
    <{{ $level }} style="{{ $titleStyle }}" {!! $animation !!}>
        {!! nl2br(e($text)) !!}
    </{{ $level }}>
@endif