@php
    $title = $content['title'] ?? 'Titre du formulaire';
    $fields = $content['fields'] ?? [];
    $buttonText = $content['button_text'] ?? 'Envoyer';
    $privacyText = $content['privacy_text'] ?? '';
    $imageUrl = $content['image_url'] ?? '';
    $layout = $content['layout'] ?? 'center';

    $isSplit = !empty($imageUrl) && in_array($layout, ['left', 'right']);

    $containerStyle = collect([
        'background-color' => $styles['backgroundColor'] ?? '#1F2937',
        'padding' => $styles['padding'] ?? '24px',
        'border-radius' => $styles['borderRadius'] ?? '12px',
        'max-width' => $isSplit ? '1000px' : ($styles['maxWidth'] ?? '500px'),
        'margin' => $styles['margin'] ?? '0 auto',
        'border' => $styles['border'] ?? null,
    ])->filter()->map(fn($v, $k) => "$k: $v")->implode('; ');
@endphp

<div class="w-full" style="{{ $containerStyle }}">
    
    <div class="{{ $isSplit ? 'flex flex-col md:flex-row gap-8 items-center' : '' }}">
        
        {{-- Image (Visible if Right Layout or Center) --}}
        @if($imageUrl && ($layout === 'right' || $layout === 'center'))
            <div class="{{ $isSplit ? 'w-full md:w-1/2' : 'w-full mb-6' }}">
                <img src="{{ $imageUrl }}" alt="Illustration" class="w-full h-auto rounded-lg object-cover shadow-md">
            </div>
        @endif

        {{-- Form Container --}}
        <div class="{{ $isSplit ? 'w-full md:w-1/2' : 'w-full' }}">
            @if($title)
                <h3 class="text-xl md:text-2xl font-bold text-center mb-6" style="color: {{ $landing['text_color'] ?? '#FFFFFF' }}">
                    {{ $title }}
                </h3>
            @endif

            <div class="space-y-4">
                @foreach($fields as $field)
                    @php
                        $label = $field['label'] ?? 'Champ';
                        $type = $field['type'] ?? 'text';
                        $placeholder = $field['placeholder'] ?? '';
                        $required = $field['required'] ?? false;
                        $optionsStr = $field['options_raw'] ?? ''; // Use options_raw from editor
                        $options = !empty($optionsStr) ? array_map('trim', explode(',', $optionsStr)) : ['Option 1', 'Option 2'];
                    @endphp

                    <div>
                        <label class="block text-sm font-medium mb-1 opacity-90" style="color: {{ $landing['text_color'] ?? '#FFFFFF' }}">
                            {{ $label }} @if($required)<span class="text-red-400">*</span>@endif
                        </label>

                        @if($type === 'textarea')
                            <textarea readonly class="w-full px-4 py-2 bg-white/10 border border-gray-600 rounded-lg text-gray-300" rows="3">{{ $placeholder }}</textarea>
                        
                        @elseif($type === 'select')
                            <select disabled class="w-full px-4 py-2 bg-white/10 border border-gray-600 rounded-lg text-gray-300">
                                <option>Sélectionner...</option>
                                @foreach($options as $opt)
                                    <option>{{ $opt }}</option>
                                @endforeach
                            </select>

                        @elseif(in_array($type, ['radio']))
                            <div class="space-y-1">
                                @foreach($options as $opt)
                                    <div class="flex items-center">
                                        <input type="radio" disabled class="bg-gray-700 border-gray-600">
                                        <span class="ml-2 text-sm text-gray-300">{{ $opt }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @elseif(in_array($type, ['checkbox']))
                             <div class="space-y-1">
                                @foreach($options as $opt)
                                    <div class="flex items-center">
                                        <input type="checkbox" disabled class="bg-gray-700 border-gray-600 rounded">
                                        <span class="ml-2 text-sm text-gray-300">{{ $opt }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @elseif(in_array($type, ['tel', 'whatsapp']))
                            <div class="flex gap-2">
                                <div class="w-24 flex-shrink-0">
                                    <div class="w-full px-3 py-2 bg-white/10 border border-gray-600 rounded-lg text-gray-300 text-sm">
                                        +225
                                    </div>
                                </div>
                                <div class="flex-1 px-4 py-2 bg-white/10 border border-gray-600 rounded-lg text-gray-300">
                                    {{ $placeholder }}
                                </div>
                            </div>
                        @else
                            <input type="{{ $type === 'whatsapp' ? 'tel' : $type }}" readonly value="{{ $placeholder }}" 
                                   class="w-full px-4 py-2 bg-white/10 border border-gray-600 rounded-lg text-gray-300">
                        @endif
                    </div>
                @endforeach

                <div class="pt-2">
                    <button class="w-full py-3 px-6 rounded-lg font-bold text-center opacity-90 cursor-not-allowed"
                            style="background-color: {{ $branding['primary_color'] ?? '#3B82F6' }}; color: white;">
                        {{ $buttonText }}
                    </button>
                    @if($privacyText)
                         <p class="text-xs text-center mt-2 opacity-60" style="color: {{ $landing['text_color'] ?? '#FFFFFF' }}">
                             {{ $privacyText }}
                         </p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Image (Visible if Left Layout) --}}
        @if($imageUrl && $layout === 'left')
            <div class="{{ $isSplit ? 'w-full md:w-1/2' : 'w-full mb-6' }}">
               <img src="{{ $imageUrl }}" alt="Illustration" class="w-full h-auto rounded-lg object-cover shadow-md">
            </div>
        @endif

    </div>
</div>