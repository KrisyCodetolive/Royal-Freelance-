{{-- Features Block --}}
@php
    $title = $content['title'] ?? '';
    // Support pour features (nouveau format) ou items (ancien format)
    $features = $content['features'] ?? $content['items'] ?? [];
    
    $backgroundColor = $styles['backgroundColor'] ?? 'transparent';
    $textColor = $styles['color'] ?? 'inherit';
    $gridGap = $styles['gap'] ?? '2rem';
    $padding = $styles['padding'] ?? '2rem';
@endphp

<div class="w-full" style="background-color: {{ $backgroundColor }}; color: {{ $textColor }}; padding: {{ $padding }};">
    <div class="max-w-6xl mx-auto">
        @if(!empty($title))
            <h2 class="text-3xl font-bold text-center mb-12">{{ $title }}</h2>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8" style="gap: {{ $gridGap }};">
            @foreach($features as $feature)
                <div class="flex flex-col items-center text-center p-6 bg-white/5 rounded-xl hover:bg-white/10 transition-colors border border-white/10">
                    {{-- Icon --}}
                    @if(!empty($feature['icon']))
                        <div class="w-16 h-16 mb-4 rounded-full bg-blue-500/20 text-blue-400 flex items-center justify-center text-2xl">
                            @if(str_starts_with($feature['icon'], 'heroicon-'))
                                <x-dynamic-component :component="$feature['icon']" class="w-8 h-8" />
                            @else
                                {{ $feature['icon'] }}
                            @endif
                        </div>
                    @endif

                    {{-- Title --}}
                    @if(!empty($feature['title']))
                        <h3 class="text-xl font-bold mb-3">{{ $feature['title'] }}</h3>
                    @endif

                    {{-- Text --}}
                    @if(!empty($feature['text']))
                        <div class="text-gray-400 text-sm leading-relaxed">
                            {!! $feature['text'] !!}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>
