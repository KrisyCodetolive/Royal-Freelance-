{{-- Section Block - Container for other blocks --}}
@php
    $paddingTop = $styles['paddingTop'] ?? '60px';
    $paddingBottom = $styles['paddingBottom'] ?? '60px';
    $backgroundColor = $styles['backgroundColor'] ?? 'transparent';
@endphp

<section class="w-full"
    style="padding-top: {{ $paddingTop }}; padding-bottom: {{ $paddingBottom }}; background-color: {{ $backgroundColor }};">
    <div class="max-w-6xl mx-auto px-4">
        {{-- Section children will be rendered here by the parent loop --}}
        @if(!empty($content['children']))
            @foreach($content['children'] as $child)
                <div class="section-child">
                    {{-- Render child block --}}
                </div>
            @endforeach
        @else
            <div class="text-center text-gray-400 py-8">
                <p>Section vide - Ajoutez des blocs</p>
            </div>
        @endif
    </div>
</section>