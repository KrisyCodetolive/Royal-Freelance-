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

    // Liste complète des pays pour le sélecteur d'indicatif
    $countries = [
        ['code' => 'CI', 'prefix' => '+225', 'name' => 'Côte d\'Ivoire'],
        ['code' => 'SN', 'prefix' => '+221', 'name' => 'Sénégal'],
        ['code' => 'ML', 'prefix' => '+223', 'name' => 'Mali'],
        ['code' => 'BF', 'prefix' => '+226', 'name' => 'Burkina Faso'],
        ['code' => 'TG', 'prefix' => '+228', 'name' => 'Togo'],
        ['code' => 'BJ', 'prefix' => '+229', 'name' => 'Bénin'],
        ['code' => 'CM', 'prefix' => '+237', 'name' => 'Cameroun'],
        ['code' => 'GA', 'prefix' => '+241', 'name' => 'Gabon'],
        ['code' => 'GN', 'prefix' => '+224', 'name' => 'Guinée'],
        ['code' => 'NE', 'prefix' => '+227', 'name' => 'Niger'],
        ['code' => 'CD', 'prefix' => '+243', 'name' => 'RD Congo'],
        ['code' => 'CG', 'prefix' => '+242', 'name' => 'Congo'],
        ['code' => 'TD', 'prefix' => '+235', 'name' => 'Tchad'],
        ['code' => 'CF', 'prefix' => '+236', 'name' => 'Centrafrique'],
        ['code' => 'MR', 'prefix' => '+222', 'name' => 'Mauritanie'],
        ['code' => 'MA', 'prefix' => '+212', 'name' => 'Maroc'],
        ['code' => 'DZ', 'prefix' => '+213', 'name' => 'Algérie'],
        ['code' => 'TN', 'prefix' => '+216', 'name' => 'Tunisie'],
        ['code' => 'MG', 'prefix' => '+261', 'name' => 'Madagascar'],
        ['code' => 'FR', 'prefix' => '+33', 'name' => 'France'],
        ['code' => 'BE', 'prefix' => '+32', 'name' => 'Belgique'],
        ['code' => 'CH', 'prefix' => '+41', 'name' => 'Suisse'],
        ['code' => 'CA', 'prefix' => '+1', 'name' => 'Canada'],
        ['code' => 'US', 'prefix' => '+1', 'name' => 'USA'],
        ['code' => 'GB', 'prefix' => '+44', 'name' => 'UK'],
        ['code' => 'DE', 'prefix' => '+49', 'name' => 'Allemagne'],
        ['code' => 'ES', 'prefix' => '+34', 'name' => 'Espagne'],
        ['code' => 'IT', 'prefix' => '+39', 'name' => 'Italie'],
        ['code' => 'PT', 'prefix' => '+351', 'name' => 'Portugal'],
        ['code' => 'AE', 'prefix' => '+971', 'name' => 'Émirats'],
        ['code' => 'TR', 'prefix' => '+90', 'name' => 'Turquie'],
        ['code' => 'NG', 'prefix' => '+234', 'name' => 'Nigéria'],
        ['code' => 'GH', 'prefix' => '+233', 'name' => 'Ghana'],
        ['code' => 'KE', 'prefix' => '+254', 'name' => 'Kenya'],
        ['code' => 'RW', 'prefix' => '+250', 'name' => 'Rwanda'],
        ['code' => 'ZA', 'prefix' => '+27', 'name' => 'Afrique du Sud'],
        ['code' => 'AO', 'prefix' => '+244', 'name' => 'Angola'],
        ['code' => 'MZ', 'prefix' => '+258', 'name' => 'Mozambique'],
        ['code' => 'CV', 'prefix' => '+238', 'name' => 'Cap-Vert'],
        ['code' => 'DJ', 'prefix' => '+253', 'name' => 'Djibouti'],
        ['code' => 'ET', 'prefix' => '+251', 'name' => 'Éthiopie'],
        ['code' => 'GQ', 'prefix' => '+240', 'name' => 'Guinée Éq.'],
        ['code' => 'LY', 'prefix' => '+218', 'name' => 'Libye'],
        ['code' => 'SC', 'prefix' => '+248', 'name' => 'Seychelles'],
        ['code' => 'MU', 'prefix' => '+230', 'name' => 'Maurice'],
        ['code' => 'RE', 'prefix' => '+262', 'name' => 'Réunion'],
    ];
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
                <h3 class="text-xl md:text-2xl font-bold text-center mb-6"
                    style="color: {{ $landing['text_color'] ?? '#FFFFFF' }}">
                    {{ $title }}
                </h3>
            @endif

            <form action="{{ route('funnel.submit', ['funnelSlug' => $funnel->slug, 'pageSlug' => $page->slug]) }}"
                method="POST" class="space-y-4">
                @csrf
                @if(session('commercial_ref'))
                    <input type="hidden" name="commercial_ref" value="{{ session('commercial_ref') }}">
                @endif

                @foreach($fields as $index => $field)
                    @php
                        $fieldName = 'field_' . ($index + 1); // Forcer l'unicité par l'index (1-basé)
                        $label = $field['label'] ?? 'Champ';
                        $type = $field['type'] ?? 'text';
                        $placeholder = $field['placeholder'] ?? '';
                        $required = $field['required'] ?? false;
                        $optionsStr = $field['options_raw'] ?? '';
                        $options = !empty($optionsStr) ? array_map('trim', explode(',', $optionsStr)) : ['Option 1', 'Option 2'];
                    @endphp

                    <div>
                        <label for="{{ $fieldName }}" class="block text-sm font-medium mb-1 opacity-90"
                            style="color: {{ $landing['text_color'] ?? '#FFFFFF' }}">
                            {{ $label }} @if($required)<span class="text-red-400">*</span>@endif
                        </label>

                        @if($type === 'textarea')
                            <textarea name="{{ $fieldName }}" id="{{ $fieldName }}"
                                class="w-full px-4 py-2 bg-white/10 border border-gray-600 rounded-lg text-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all"
                                rows="3" placeholder="{{ $placeholder }}" {{ $required ? 'required' : '' }}></textarea>

                        @elseif($type === 'select')
                            <select name="{{ $fieldName }}" id="{{ $fieldName }}"
                                class="w-full px-4 py-2 bg-white/10 border border-gray-600 rounded-lg text-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all"
                                {{ $required ? 'required' : '' }}>
                                <option value="">Sélectionner...</option>
                                @foreach($options as $opt)
                                    <option value="{{ $opt }}">{{ $opt }}</option>
                                @endforeach
                            </select>

                        @elseif(in_array($type, ['radio']))
                            <div class="space-y-1">
                                @foreach($options as $opt)
                                    <div class="flex items-center">
                                        <input type="radio" name="{{ $fieldName }}" value="{{ $opt }}"
                                            class="bg-gray-700 border-gray-600 params-radio" {{ $required ? 'required' : '' }}>
                                        <span class="ml-2 text-sm text-gray-300">{{ $opt }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @elseif(in_array($type, ['checkbox']))
                            <div class="space-y-1">
                                @foreach($options as $opt)
                                    <div class="flex items-center">
                                        <input type="checkbox" name="{{ $fieldName }}[]" value="{{ $opt }}"
                                            class="bg-gray-700 border-gray-600 rounded" {{ $required ? 'required' : '' }}>
                                        <span class="ml-2 text-sm text-gray-300">{{ $opt }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @elseif(in_array($type, ['tel', 'whatsapp']))
                            <div class="flex gap-1 sm:gap-2">
                                <div class="w-20 sm:w-28 flex-shrink-0">
                                    <select name="{{ $fieldName }}_country" 
                                        class="w-full px-1 sm:px-2 py-2 bg-white/10 border border-gray-600 rounded-lg text-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all appearance-none cursor-pointer text-xs">
                                        @foreach($countries as $country)
                                            <option value="{{ $country['prefix'] }}" {{ old($fieldName . '_country') == $country['prefix'] ? 'selected' : ($country['code'] == 'CI' ? 'selected' : '') }}>
                                                {{ $country['code'] }} ({{ $country['prefix'] }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <input type="tel" name="{{ $fieldName }}" id="{{ $fieldName }}"
                                    value="{{ old($fieldName) }}" placeholder="{{ $placeholder }}"
                                    class="flex-1 min-w-0 px-3 sm:px-4 py-2 bg-white/10 border border-gray-600 rounded-lg text-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all"
                                    {{ $required ? 'required' : '' }}>
                            </div>
                        @else
                            <input type="{{ $type === 'whatsapp' ? 'tel' : $type }}" name="{{ $fieldName }}" id="{{ $fieldName }}"
                                value="{{ old($fieldName) }}" placeholder="{{ $placeholder }}"
                                class="w-full px-4 py-2 bg-white/10 border border-gray-600 rounded-lg text-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all"
                                {{ $required ? 'required' : '' }}>
                        @endif

                        @error($fieldName)
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                @endforeach

                <div class="pt-2">
                    <button type="submit"
                        class="w-full py-3 px-6 rounded-lg font-bold text-center hover:opacity-90 transition-opacity transform hover:scale-[1.02] duration-200"
                        style="background-color: {{ $branding['primary_color'] ?? '#3B82F6' }}; color: white; box-shadow: 0 4px 14px 0 rgba(0,0,0,0.39);">
                        {{ $buttonText }}
                    </button>
                    @if($privacyText)
                        <p class="text-xs text-center mt-2 opacity-60"
                            style="color: {{ $landing['text_color'] ?? '#FFFFFF' }}">
                            {{ $privacyText }}
                        </p>
                    @endif
                </div>
            </form>
        </div>

        {{-- Image (Visible if Left Layout) --}}
        @if($imageUrl && $layout === 'left')
            <div class="{{ $isSplit ? 'w-full md:w-1/2' : 'w-full mb-6' }}">
                <img src="{{ $imageUrl }}" alt="Illustration" class="w-full h-auto rounded-lg object-cover shadow-md">
            </div>
        @endif

    </div>
</div>