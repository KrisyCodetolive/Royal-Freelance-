{{-- Progress Block - Progress Bar --}}
@php
    $percentage = $content['percentage'] ?? 0;
    $label = $content['label'] ?? '';
    $showPercentage = $content['show_percentage'] ?? true;
    $color = $content['color'] ?? '#6366F1';
@endphp

<div class="w-full py-4">
    {{-- Label --}}
    @if(!empty($label))
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium">{{ $label }}</span>
            @if($showPercentage)
                <span class="text-sm font-bold" style="color: {{ $color }}">{{ $percentage }}%</span>
            @endif
        </div>
    @endif

    {{-- Progress bar --}}
    <div class="w-full bg-gray-700 rounded-full h-3 overflow-hidden">
        <div class="h-full rounded-full transition-all duration-500 ease-out"
            style="width: {{ min(100, max(0, $percentage)) }}%; background-color: {{ $color }};">
        </div>
    </div>

    {{-- Percentage below (if no label) --}}
    @if(empty($label) && $showPercentage)
        <p class="text-center text-sm mt-2" style="color: {{ $color }}">{{ $percentage }}%</p>
    @endif
</div>