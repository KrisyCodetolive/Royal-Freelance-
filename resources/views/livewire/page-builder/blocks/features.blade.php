{{-- Features Block - PAGE BUILDER PREVIEW --}}
@php
    $title = $content['title'] ?? '';
    // Support pour features (nouveau format) ou items (ancien format)
    $features = $content['features'] ?? $content['items'] ?? [];

    $backgroundColor = $styles['backgroundColor'] ?? 'transparent';
    $textColor = $styles['color'] ?? 'inherit';
    $gridGap = $styles['gap'] ?? '2rem';
    $padding = $styles['padding'] ?? '2rem';
@endphp

<div class="w-full relative group/block"
    style="background-color: {{ $backgroundColor }}; color: {{ $textColor }}; padding: {{ $padding }};">
    <div class="max-w-6xl mx-auto">
        @if(!empty($title))
            <h2 class="text-3xl font-bold text-center mb-8">{{ $title }}</h2>
        @else
            <div class="text-center text-gray-500 italic mb-8 border border-dashed border-gray-600 p-2 rounded">
                Ajoutez un titre (optionnel)
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6" style="gap: {{ $gridGap }};">
            @forelse($features as $feature)
                <div class="flex flex-col items-center text-center p-4 bg-gray-800 rounded-xl border border-gray-700">
                    {{-- Icon --}}
                    @if(!empty($feature['icon']))
                        <div class="w-12 h-12 mb-3 rounded-full bg-blue-500/20 text-blue-400 flex items-center justify-center">
                            @if(str_starts_with($feature['icon'], 'heroicon-'))
                                <x-dynamic-component :component="$feature['icon']" class="w-6 h-6" />
                            @else
                                <span class="text-xl">{{ $feature['icon'] }}</span>
                            @endif
                        </div>
                    @endif

                    {{-- Title --}}
                    <h3 class="text-lg font-bold mb-2">{{ $feature['title'] ?? 'Titre de l\'avantage' }}</h3>

                    {{-- Text --}}
                    <div class="text-gray-400 text-sm">
                        {!! $feature['text'] ?? 'Description de l\'avantage...' !!}
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center py-8 text-gray-400 border-2 border-dashed border-gray-700 rounded-xl">
                    Aucun avantage ajouté. Cliquez pour configurer.
                </div>
            @endforelse
        </div>
    </div>
</div>