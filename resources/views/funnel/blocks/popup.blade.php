{{-- Popup Block - Modal popup --}}
@php
    $title = $content['title'] ?? '';
    // Gestion du body qui peut être un wysiwyg html
    $body = $content['body'] ?? '';
    $buttonText = $content['button_text'] ?? 'Fermer';
    $buttonUrl = $content['button_url'] ?? null;
    $trigger = $content['trigger'] ?? 'exit_intent'; // exit_intent, delay, scroll
    $delay = $content['delay'] ?? 5000; // milliseconds
    $scrollPercent = $content['scroll_percent'] ?? 50;
    $showOnce = $content['show_once'] ?? true;
    $image = $content['image'] ?? null;

    $backgroundColor = $styles['backgroundColor'] ?? '#1F2937';
    // Use rgba for overlay if not set
    $overlayColor = $styles['overlayColor'] ?? 'rgba(0,0,0,0.8)';
    $textColor = $styles['color'] ?? '#FFFFFF';
@endphp

<div x-data="{ 
        open: false, 
        triggered: false,
        triggerType: '{{ $trigger }}',
        delay: {{ $delay }},
        scrollPercent: {{ $scrollPercent }},
        showOnce: {{ $showOnce ? 'true' : 'false' }},
        blockId: '{{ $block->id }}',
        
        init() {
            // Check localStorage if showOnce is true
            if (this.showOnce && localStorage.getItem('popup-' + this.blockId + '-shown')) {
                return;
            }
            
            // Setup Triggers
            if (this.triggerType === 'delay') {
                setTimeout(() => this.showPopup(), this.delay);
            } 
            else if (this.triggerType === 'scroll') {
                window.addEventListener('scroll', () => {
                    if (this.triggered) return;
                    const scrolled = (window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100;
                    if (scrolled >= this.scrollPercent) {
                        this.showPopup();
                    }
                }, { passive: true });
            } 
            else { // exit_intent
                // Desktop: Mouse leaves viewport top
                document.addEventListener('mouseleave', (e) => {
                    if (e.clientY < 10 && !this.triggered) {
                        this.showPopup();
                    }
                });

                // Mobile: Back button or fast scroll up (simulation)
                let lastScrollTop = window.scrollY;
                window.addEventListener('scroll', () => {
                    if (this.triggered) return;
                    
                    // Simple logic: if user scrolls up fast after being down a bit, might be leaving
                    const st = window.scrollY;
                    if (st < lastScrollTop - 50 && st > 100) { 
                        // Scrolling UP fast
                        // We could trigger here but it might be annoying.
                        // Better to use a fallback timer on mobile for 'exit intent'
                    }
                    lastScrollTop = st;
                }, { passive: true });

                // Fallback: Trigger after 20s if nothing happened (common practice for mobile exit intent)
                if(window.innerWidth < 768) {
                    setTimeout(() => {
                        if(!this.triggered) this.showPopup();
                    }, 20000); 
                }
            }
        },
        
        showPopup() {
            if (this.triggered) return;
            this.triggered = true;
            this.open = true;
            
            if (this.showOnce) {
                localStorage.setItem('popup-' + this.blockId + '-shown', 'true');
            }
        }
    }" 
    x-cloak 
    @keydown.escape.window="open = false"
    class="relative z-[100]" {{-- Ensure high z-index container --}}
>

    {{-- Overlay --}}
    <div x-show="open" 
        x-transition:enter="transition ease-out duration-300" 
        x-transition:enter-start="opacity-0 backdrop-blur-none"
        x-transition:enter-end="opacity-100 backdrop-blur-sm" 
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 backdrop-blur-sm" 
        x-transition:leave-end="opacity-0 backdrop-blur-none"
        class="fixed inset-0 z-[100] flex items-center justify-center p-4 backdrop-blur-sm" 
        style="background-color: {{ $overlayColor }};"
        @click.self="open = false">

        {{-- Popup content --}}
        <div x-show="open" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-90 translate-y-4" 
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200" 
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-90 translate-y-4" 
            class="relative max-w-lg w-full rounded-2xl p-6 md:p-8 shadow-2xl overflow-hidden border border-white/10 ring-1 ring-black/5"
            style="background-color: {{ $backgroundColor }}; color: {{ $textColor }}">

            {{-- Close button --}}
            <button @click="open = false"
                class="absolute top-3 right-3 p-2 rounded-full hover:bg-white/10 transition-colors z-10 opacity-70 hover:opacity-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

            <div class="relative z-10 flex flex-col items-center text-center">
                {{-- Image --}}
                @if(!empty($image))
                    <div class="w-full mb-6">
                        <img src="{{ $image }}" alt="" class="w-full max-h-48 object-cover rounded-xl shadow-md">
                    </div>
                @endif

                {{-- Title --}}
                @if(!empty($title))
                    <h3 class="text-2xl font-bold mb-3 leading-tight">{{ $title }}</h3>
                @endif

                {{-- Body --}}
                @if(!empty($body))
                    <div class="prose prose-invert prose-sm max-w-none mb-6 text-gray-300 opacity-90">
                        {!! $body !!}
                    </div>
                @endif

                {{-- Call to Action --}}
                @if(!empty($buttonText))
                    @if($buttonUrl)
                        <a href="{{ $buttonUrl }}"
                            class="block w-full py-3.5 px-6 rounded-xl font-bold text-center transition-all transform hover:scale-[1.02] active:scale-[0.98] shadow-lg hover:shadow-xl"
                            style="background-color: var(--primary, #3B82F6); color: white;">
                            {{ $buttonText }}
                        </a>
                    @else
                        <button @click="open = false"
                            class="block w-full py-3.5 px-6 rounded-xl font-bold transition-all transform hover:scale-[1.02] active:scale-[0.98] shadow-lg hover:shadow-xl"
                            style="background-color: var(--primary, #3B82F6); color: white;">
                            {{ $buttonText }}
                        </button>
                    @endif
                @endif
            </div>
            
            {{-- Background decoration (optional) --}}
            <div class="absolute inset-0 z-0 pointer-events-none opacity-20 bg-gradient-to-tr from-white/5 to-transparent"></div>
        </div>
    </div>
</div>