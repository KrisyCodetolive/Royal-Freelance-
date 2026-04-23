@php
    $settings = $page->settings ?? [];
@endphp
<!DOCTYPE html>
<html lang="{{ $settings['language'] ?? 'fr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="{{ ($settings['hideFromSearch'] ?? false) ? 'noindex, nofollow' : 'index, follow' }}">

    {{-- SEO Meta --}}
    <title>{{ $settings['seoTitle'] ?? $page->meta_title ?? $page->title ?? $funnel->name }}</title>
    <meta name="description" content="{{ $settings['seoDescription'] ?? $page->meta_description ?? $funnel->meta_description ?? '' }}">
    @if($settings['seoKeywords'] ?? false)
        <meta name="keywords" content="{{ $settings['seoKeywords'] }}">
    @endif
    @if($settings['seoAuthor'] ?? false)
        <meta name="author" content="{{ $settings['seoAuthor'] }}">
    @endif

    {{-- Open Graph --}}
    <meta property="og:title" content="{{ $settings['seoTitle'] ?? $page->meta_title ?? $page->title ?? $funnel->name }}">
    <meta property="og:description" content="{{ $settings['seoDescription'] ?? $page->meta_description ?? $funnel->meta_description ?? '' }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    @if($settings['seoImage'] ?? false)
        <meta property="og:image" content="{{ $settings['seoImage'] }}">
    @endif

    {{-- Favicon --}}
    @if($funnel->branding['favicon'] ?? false)
        <link rel="icon" href="{{ $funnel->branding['favicon'] }}">
    @endif

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- Tailwind CSS via CDN for public pages --}}
    <script src="https://cdn.tailwindcss.com?plugins=typography,forms"></script>

    {{-- Alpine.js for popup interactions --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Facebook Pixel --}}
    @if($settings['facebookEvent'] ?? false)
        <script>
            !function(f,b,e,v,n,t,s)
            {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window, document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', 'YOUR_PIXEL_ID');
            fbq('track', '{{ $settings['facebookEvent'] }}');
        </script>
    @endif

    {{-- Google Analytics --}}
    @if($settings['googleAnalyticsId'] ?? false)
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $settings['googleAnalyticsId'] }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ $settings['googleAnalyticsId'] }}');
        </script>
    @endif

    {{-- Custom Header Code --}}
    @if($settings['headerCode'] ?? false)
        {!! $settings['headerCode'] !!}
    @endif

    <style>
        body {
            font-family: {{ $settings['defaultFont'] ?? $branding['font_family'] ?? 'Inter' }}, sans-serif;
            background-color: {{ $settings['backgroundColor'] ?? $branding['background_color'] ?? '#0F172A' }};
            color: {{ $settings['textColor'] ?? $branding['text_color'] ?? '#FFFFFF' }};
            font-size: {{ $settings['defaultFontSize'] ?? '16' }}px;
            line-height: {{ $settings['defaultLineHeight'] ?? '24' }}px;
            text-align: {{ $settings['defaultAlignment'] ?? 'left' }};
            min-height: 100vh;
            @if($settings['backgroundImage'] ?? false)
                background-image: url('{{ $settings['backgroundImage'] }}');
                background-size: cover;
                background-position: center;
                background-attachment: fixed;
                @if($settings['backgroundBlur'] ?? 0 > 0)
                    backdrop-filter: blur({{ $settings['backgroundBlur'] }}px);
                @endif
            @endif
        }

        a {
            color: {{ $settings['linkColor'] ?? '#3B82F6' }};
        }

        h1, h2, h3, h4, h5, h6 {
            @if(($settings['headingFont'] ?? 'same') !== 'same')
                font-family: {{ $settings['headingFont'] }}, sans-serif;
            @endif
            color: {{ $settings['headingColor'] ?? $settings['textColor'] ?? '#FFFFFF' }};
            text-align: {{ $settings['headingAlignment'] ?? 'center' }};
        }

        .btn-primary {
            background-color: {{ $branding['primary_color'] ?? '#3B82F6' }};
            color: #FFFFFF;
        }

        .btn-primary:hover {
            opacity: 0.9;
        }

        input:focus, textarea:focus, select:focus {
            outline: none;
            ring: 2px solid {{ $branding['primary_color'] ?? '#3B82F6' }};
        }

        /* Smooth animations */
        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="antialiased">
    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="fixed top-4 right-4 z-50 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg fade-in"
             x-data="{ show: true }"
             x-show="show"
             x-init="setTimeout(() => show = false, 5000)">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="fixed top-4 right-4 z-50 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg fade-in"
             x-data="{ show: true }"
             x-show="show"
             x-init="setTimeout(() => show = false, 5000)">
            {{ session('error') }}
        </div>
    @endif

    {{-- Logo Header (if available) --}}
    @if($branding['logo'] ?? false)
        <header class="py-4 text-center">
            <img src="{{ $branding['logo'] }}" alt="{{ $funnel->name }}" class="h-12 mx-auto">
        </header>
    @endif

    {{-- Main Content --}}
    <main class="max-w-4xl mx-auto px-4 py-8 md:py-16">
        <div class="space-y-6 fade-in">
            @foreach($blocks as $block)
                @php
                    $blockType = $block->type->value ?? $block->type;
                @endphp

                <div class="block-item">
                    @include('funnel.blocks.' . $blockType, [
                        'block' => $block,
                        'content' => $block->content ?? [],
                        'styles' => $block->styles ?? [],
                        'branding' => $branding,
                        'funnel' => $funnel,
                        'page' => $page,
                        'commercialWhatsApp' => $commercialWhatsApp ?? null,
                        'commercialWhatsAppMessage' => $commercialWhatsAppMessage ?? null,
                    ])
                </div>
            @endforeach
        </div>
    </main>

    {{-- Footer --}}
    <footer class="py-8 text-center text-sm text-gray-500">
        <p>© {{ date('Y') }} {{ $funnel->tenant?->name ?? 'All rights reserved' }}</p>
        @if($funnel->settings['show_privacy_link'] ?? false)
            <p class="mt-2">
                <a href="#" class="hover:underline">Politique de confidentialité</a>
            </p>
        @endif
    </footer>

    {{-- Custom Footer Code --}}
    @if($settings['footerCode'] ?? false)
        {!! $settings['footerCode'] !!}
    @endif

    {{-- Tracking Scripts --}}
    @if($funnel->settings['tracking_scripts'] ?? false)
        {!! $funnel->settings['tracking_scripts'] !!}
    @endif

    {{-- Enhanced Visitor Tracking --}}
    <script>
        (function() {
            // Track page entry time
            const pageEntryTime = Date.now();
            let maxScrollDepth = 0;
            let screenResolution = `${window.screen.width}x${window.screen.height}`;
            
            // Calculate scroll depth
            function calculateScrollDepth() {
                const windowHeight = window.innerHeight;
                const documentHeight = document.documentElement.scrollHeight;
                const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                const scrollPercentage = Math.round((scrollTop + windowHeight) / documentHeight * 100);
                
                if (scrollPercentage > maxScrollDepth) {
                    maxScrollDepth = Math.min(scrollPercentage, 100);
                }
            }
            
            // Track scroll depth
            let scrollTimeout;
            window.addEventListener('scroll', function() {
                clearTimeout(scrollTimeout);
                scrollTimeout = setTimeout(calculateScrollDepth, 100);
            }, { passive: true });
            
            // Initial calculation
            calculateScrollDepth();
            
            // Send tracking data to server
            function sendTrackingData() {
                const timeSpentSeconds = Math.round((Date.now() - pageEntryTime) / 1000);
                
                // Only send if user spent at least 3 seconds
                if (timeSpentSeconds < 3) return;
                
                const data = {
                    time_spent_seconds: timeSpentSeconds,
                    scroll_depth_percentage: maxScrollDepth,
                    screen_resolution: screenResolution,
                    page_id: {{ $page->id }},
                    lead_id: {{ $currentLead->id ?? 'null' }}
                };
                
                // Use sendBeacon for reliability (works even when page is closing)
                if (navigator.sendBeacon) {
                    const formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    formData.append('data', JSON.stringify(data));
                    navigator.sendBeacon('{{ url("/api/tracking/page-engagement") }}', formData);
                } else {
                    // Fallback to synchronous XHR
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', '{{ url("/api/tracking/page-engagement") }}', false);
                    xhr.setRequestHeader('Content-Type', 'application/json');
                    xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
                    xhr.send(JSON.stringify(data));
                }
            }
            
            // Send data before page unload
            window.addEventListener('beforeunload', sendTrackingData);
            
            // Send data periodically (every 30 seconds) for long sessions
            setInterval(function() {
                if (document.hasFocus()) {
                    sendTrackingData();
                }
            }, 30000);
            
            // Send data when user submits a form
            document.addEventListener('submit', function(e) {
                sendTrackingData();
            });
            
            // Store screen resolution in lead data (first visit only)
            if ({{ $currentLead && !$currentLead->screen_resolution ? 'true' : 'false' }}) {
                fetch('{{ url("/api/tracking/update-lead-data") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        screen_resolution: screenResolution,
                        lead_id: {{ $currentLead->id ?? 'null' }}
                    })
                }).catch(() => {});
            }
        })();
    </script>
</body>
</html>