@php
    use App\Helpers\BlockStyleHelper;

    $title = $content['title'] ?? 'Offre expire dans :';
    $hours = (int) ($content['hours'] ?? 24);
    $targetDate = $content['target_date'] ?? now()->addHours($hours)->toIso8601String();
    $format = $content['format'] ?? 'boxes'; // boxes, inline, minimal
    $size = $content['size'] ?? 'medium';
    $showLabels = $content['show_labels'] ?? true;

    // Size presets
    $sizeStyles = match ($size) {
        'small' => ['fontSize' => '1.5rem', 'padding' => '12px'],
        'large' => ['fontSize' => '3rem', 'padding' => '24px'],
        default => ['fontSize' => '2rem', 'padding' => '16px'],
    };

    $defaultStyles = array_merge($sizeStyles, [
        'backgroundColor' => '#EF4444',
        'color' => '#FFFFFF',
        'borderRadius' => '12px',
        'maxWidth' => '600px',
        'margin' => '0 auto',
        'textAlign' => 'center',
    ]);

    $containerStyle = BlockStyleHelper::generateStyle($styles, $defaultStyles);
    $animation = BlockStyleHelper::getAnimationAttribute($styles);
@endphp

<div style="{{ $containerStyle }}" x-data="countdown('{{ $targetDate }}')" x-init="start()" {!! $animation !!}>

    @if($title)
        <p class="text-sm font-medium mb-4" style="font-size: {{ $size === 'large' ? '1.25rem' : '1rem' }};">{{ $title }}
        </p>
    @endif

    @if($format === 'boxes')
        <div class="flex justify-center gap-3" style="flex-wrap: wrap;">
            <div class="countdown-box"
                style="background: rgba(0,0,0,0.2); padding: 1rem; border-radius: 8px; min-width: 80px;">
                <div class="countdown-value" x-text="days"
                    style="font-size: {{ $sizeStyles['fontSize'] }}; font-weight: bold;">00</div>
                @if($showLabels)
                    <div class="countdown-label"
                        style="font-size: 0.75rem; text-transform: uppercase; opacity: 0.8; margin-top: 0.25rem;">Jours</div>
                @endif
            </div>
            <div class="countdown-separator"
                style="font-size: {{ $sizeStyles['fontSize'] }}; font-weight: bold; display: flex; align-items: center;">:
            </div>
            <div class="countdown-box"
                style="background: rgba(0,0,0,0.2); padding: 1rem; border-radius: 8px; min-width: 80px;">
                <div class="countdown-value" x-text="hours"
                    style="font-size: {{ $sizeStyles['fontSize'] }}; font-weight: bold;">00</div>
                @if($showLabels)
                    <div class="countdown-label"
                        style="font-size: 0.75rem; text-transform: uppercase; opacity: 0.8; margin-top: 0.25rem;">Heures</div>
                @endif
            </div>
            <div class="countdown-separator"
                style="font-size: {{ $sizeStyles['fontSize'] }}; font-weight: bold; display: flex; align-items: center;">:
            </div>
            <div class="countdown-box"
                style="background: rgba(0,0,0,0.2); padding: 1rem; border-radius: 8px; min-width: 80px;">
                <div class="countdown-value" x-text="minutes"
                    style="font-size: {{ $sizeStyles['fontSize'] }}; font-weight: bold;">00</div>
                @if($showLabels)
                    <div class="countdown-label"
                        style="font-size: 0.75rem; text-transform: uppercase; opacity: 0.8; margin-top: 0.25rem;">Minutes</div>
                @endif
            </div>
            <div class="countdown-separator"
                style="font-size: {{ $sizeStyles['fontSize'] }}; font-weight: bold; display: flex; align-items: center;">:
            </div>
            <div class="countdown-box"
                style="background: rgba(0,0,0,0.2); padding: 1rem; border-radius: 8px; min-width: 80px;">
                <div class="countdown-value" x-text="seconds"
                    style="font-size: {{ $sizeStyles['fontSize'] }}; font-weight: bold;">00</div>
                @if($showLabels)
                    <div class="countdown-label"
                        style="font-size: 0.75rem; text-transform: uppercase; opacity: 0.8; margin-top: 0.25rem;">Secondes</div>
                @endif
            </div>
        </div>
    @elseif($format === 'inline')
        <div style="font-size: {{ $sizeStyles['fontSize'] }}; font-weight: bold;">
            <span x-text="days">00</span>j <span x-text="hours">00</span>h <span x-text="minutes">00</span>m <span
                x-text="seconds">00</span>s
        </div>
    @else
        <div class="flex justify-center gap-2" style="font-size: {{ $sizeStyles['fontSize'] }}; font-weight: bold;">
            <span x-text="days">00</span>:<span x-text="hours">00</span>:<span x-text="minutes">00</span>:<span
                x-text="seconds">00</span>
        </div>
    @endif
</div>

<script>
    function countdown(targetDate) {
        return {
            days: '00',
            hours: '00',
            minutes: '00',
            seconds: '00',
            interval: null,
            start() {
                this.update();
                this.interval = setInterval(() => this.update(), 1000);
            },
            update() {
                const target = new Date(targetDate).getTime();
                const now = new Date().getTime();
                const diff = target - now;

                if (diff <= 0) {
                    this.days = this.hours = this.minutes = this.seconds = '00';
                    clearInterval(this.interval);
                    return;
                }

                this.days = String(Math.floor(diff / (1000 * 60 * 60 * 24))).padStart(2, '0');
                this.hours = String(Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))).padStart(2, '0');
                this.minutes = String(Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60))).padStart(2, '0');
                this.seconds = String(Math.floor((diff % (1000 * 60)) / 1000)).padStart(2, '0');
            }
        };
    }
</script>