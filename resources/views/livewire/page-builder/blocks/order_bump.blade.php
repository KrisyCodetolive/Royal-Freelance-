{{-- Order Bump Block - Upsell checkbox --}}
@php
    $title = $content['title'] ?? 'Offre spéciale !';
    $description = $content['description'] ?? '';
    $price = $content['price'] ?? '';
    $image = $content['image'] ?? null;
    $checked = $content['default_checked'] ?? false;
@endphp

<div class="w-full py-4">
    <div
        class="bg-gradient-to-r from-amber-900/40 to-orange-900/40 border-2 border-dashed border-amber-500/50 rounded-xl p-6 relative overflow-hidden">
        {{-- Corner badge --}}
        <div class="absolute -top-1 -left-1 bg-amber-500 text-black text-xs font-bold px-3 py-1 rounded-br-lg">
            🎁 OFFRE SPÉCIALE
        </div>

        <div class="flex flex-col md:flex-row gap-4 items-start md:items-center pt-4">
            {{-- Checkbox --}}
            <label class="flex items-start gap-4 cursor-pointer flex-1">
                <input type="checkbox" name="order_bump" value="1" {{ $checked ? 'checked' : '' }}
                    class="mt-1 w-5 h-5 rounded border-amber-500 text-amber-500 focus:ring-amber-500 cursor-pointer">

                <div class="flex-1">
                    {{-- Title --}}
                    <p class="font-bold text-lg text-amber-400 mb-1">
                        ✅ OUI ! Ajouter {{ $title }}
                    </p>

                    {{-- Description --}}
                    @if(!empty($description))
                        <p class="text-sm text-gray-300 mb-2">{{ $description }}</p>
                    @endif

                    {{-- Price --}}
                    @if(!empty($price))
                        <p class="text-sm">
                            <span class="text-gray-400">Seulement</span>
                            <span class="text-amber-400 font-bold text-lg">{{ $price }}</span>
                            <span class="text-gray-400">de plus</span>
                        </p>
                    @endif
                </div>

                {{-- Image --}}
                @if(!empty($image))
                    <img src="{{ $image }}" alt="{{ $title }}" class="w-20 h-20 object-cover rounded-lg hidden md:block">
                @endif
            </label>
        </div>
    </div>
</div>