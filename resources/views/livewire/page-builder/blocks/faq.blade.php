{{-- FAQ Block - Accordion FAQ --}}
@php
    $title = $content['title'] ?? '❓ Questions Fréquentes';
    $items = $content['items'] ?? [];
    $maxWidth = $styles['maxWidth'] ?? '800px';
@endphp

<div class="w-full py-6" style="max-width: {{ $maxWidth }}; margin: 0 auto;">
    {{-- Title --}}
    @if(!empty($title))
        <h3 class="text-2xl font-bold text-center mb-6">{{ $title }}</h3>
    @endif

    {{-- FAQ Items --}}
    <div class="space-y-3" x-data="{ openItem: null }">
        @foreach($items as $index => $item)
            <div class="bg-white/5 rounded-xl border border-white/10 overflow-hidden">
                <button @click="openItem = openItem === {{ $index }} ? null : {{ $index }}"
                    class="w-full flex items-center justify-between p-4 text-left hover:bg-white/5 transition-colors">
                    <span class="font-bold pr-4">{{ $item['question'] ?? 'Question' }}</span>
                    <svg class="w-5 h-5 flex-shrink-0 transition-transform"
                        :class="{ 'rotate-180': openItem === {{ $index }} }" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="openItem === {{ $index }}" x-collapse class="px-4 pb-4 text-gray-400">
                    <p>{{ $item['answer'] ?? 'Réponse' }}</p>
                </div>
            </div>
        @endforeach
    </div>
</div>