{{-- Pricing Block - Pricing Table --}}
@php
    $plans = $content['plans'] ?? [];
@endphp

<div class="w-full py-8">
    <div class="flex flex-col md:flex-row gap-6 justify-center items-stretch">
        @foreach($plans as $index => $plan)
            @php
                $isPopular = $plan['is_popular'] ?? false;
            @endphp
            <div class="flex-1 max-w-sm mx-auto w-full rounded-2xl p-6 
                            {{ $isPopular ? 'bg-gradient-to-br from-indigo-900/60 to-purple-900/60 border-2 border-indigo-500 scale-105' : 'bg-white/5 border border-white/10' }}
                            transition-all hover:scale-[1.02]">

                {{-- Popular badge --}}
                @if($isPopular)
                    <div class="text-center mb-4">
                        <span class="bg-indigo-500 text-white text-xs font-bold px-3 py-1 rounded-full">
                            ⭐ PLUS POPULAIRE
                        </span>
                    </div>
                @endif

                {{-- Plan name --}}
                <h3 class="text-xl font-bold text-center mb-2">{{ $plan['name'] ?? 'Plan' }}</h3>

                {{-- Description --}}
                @if(!empty($plan['description']))
                    <p class="text-sm text-gray-400 text-center mb-4">{{ $plan['description'] }}</p>
                @endif

                {{-- Price --}}
                <div class="text-center mb-6">
                    @if(!empty($plan['old_price']))
                        <span class="text-gray-500 line-through text-lg">{{ $plan['old_price'] }}</span>
                    @endif
                    <div class="text-4xl font-bold {{ $isPopular ? 'text-indigo-400' : 'text-white' }}">
                        {{ $plan['price'] ?? '0€' }}
                    </div>
                    @if(!empty($plan['period']))
                        <span class="text-sm text-gray-400">{{ $plan['period'] }}</span>
                    @endif
                </div>

                {{-- Features --}}
                @if(!empty($plan['features']))
                    <ul class="space-y-3 mb-6">
                        @foreach($plan['features'] as $feature)
                            <li class="flex items-start gap-2 text-sm">
                                <span class="text-green-400 flex-shrink-0">✓</span>
                                <span>{{ $feature }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif

                {{-- Button --}}
                <button
                    class="w-full py-3 px-6 rounded-lg font-bold transition-all
                                   {{ $isPopular ? 'bg-indigo-500 hover:bg-indigo-600 text-white' : 'bg-white/10 hover:bg-white/20 text-white' }}">
                    {{ $plan['button_text'] ?? 'Choisir' }}
                </button>
            </div>
        @endforeach
    </div>
</div>