@php
    $title = $content['title'] ?? 'Votre Titre';
    $subtitle = $content['subtitle'] ?? '';
    $buttonText = $content['button_text'] ?? 'Bouton';
    $imageUrl = $content['image_url'] ?? '';
    $layout = $content['layout'] ?? 'left'; // left, right, center

    $overlayStyle = 'background: rgba(0,0,0,0.5);';
    if ($layout === 'center' && $imageUrl) {
        $containerStyle = "background-image: url('$imageUrl'); background-size: cover; background-position: center; position: relative; color: white;";
    } else {
        $containerStyle = $styles['backgroundColor'] ? "background-color: {$styles['backgroundColor']};" : '';
    }
@endphp

<div class="w-full relative overflow-hidden"
    style="{{ $containerStyle }}; padding: {{ $styles['padding'] ?? '60px 20px' }}; min-height: {{ $styles['minHeight'] ?? '400px' }}">

    @if($layout === 'center' && $imageUrl)
        <div class="absolute inset-0 z-0" style="{{ $overlayStyle }}"></div>
    @endif

    <div
        class="relative z-10 container mx-auto flex flex-col md:flex-row items-center gap-8 {{ $layout === 'right' ? 'md:flex-row-reverse' : '' }}">

        {{-- Text Content --}}
        <div class="flex-1 space-y-6 {{ $layout === 'center' ? 'text-center w-full' : '' }}">
            <h1 class="text-4xl md:text-5xl font-bold leading-tight"
                style="color: {{ $landing['text_color'] ?? 'inherit' }}">
                {{ $title }}
            </h1>

            @if($subtitle)
                <p class="text-xl opacity-90" style="color: {{ $landing['text_color'] ?? 'inherit' }}">
                    {{ $subtitle }}
                </p>
            @endif

            @if($buttonText)
                <div class="{{ $layout === 'center' ? 'flex justify-center' : '' }}">
                    <button class="px-8 py-4 rounded-lg font-semibold transform hover:scale-105 transition duration-200"
                        style="background-color: {{ $branding['primary_color'] ?? '#3B82F6' }}; color: white;">
                        {{ $buttonText }}
                    </button>
                </div>
            @endif
        </div>

        {{-- Image (Side Layouts) --}}
        @if($layout !== 'center' && $imageUrl)
            <div class="flex-1 w-full relative">
                <img src="{{ $imageUrl }}" alt="Hero Image"
                    class="w-full h-auto rounded-lg shadow-2xl object-cover transform hover:scale-[1.02] transition duration-500">
            </div>
        @elseif($layout !== 'center' && !$imageUrl)
            {{-- Placeholder for image --}}
            <div
                class="flex-1 w-full bg-white/10 border-2 border-dashed border-white/20 rounded-lg flex items-center justify-center h-64">
                <span class="text-white/50">Image Hero</span>
            </div>
        @endif
    </div>
</div>