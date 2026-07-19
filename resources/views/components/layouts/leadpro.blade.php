@props([
    'title' => 'Royal LeadPro - La plateforme SaaS de génération de leads',
    'description' => "Créez vos tunnels de vente, capturez et suivez vos leads, automatisez vos séquences email. Royal LeadPro s'adapte à votre équipe, du solo au workspace complet.",
])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth overflow-x-hidden">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <title>{{ $title }}</title>
    <meta name="description" content="{{ $description }}">
    <meta name="theme-color" content="#09090b">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:300,400,500,600,700|playfair-display:400,500,600,700,800"
        rel="stylesheet" />
    <script>
        // Applique le thème avant le premier paint pour éviter le flash.
        // Dark par défaut (identité de marque), la préférence est mémorisée.
        (function () {
            var stored = localStorage.getItem('royalleadpro-theme');
            var isDark = stored ? stored === 'dark' : true;
            document.documentElement.classList.toggle('dark', isDark);
        })();

        function toggleTheme() {
            var isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('royalleadpro-theme', isDark ? 'dark' : 'light');
        }
    </script>
    <style>
        [x-cloak] {
            display: none !important;
        }

        html,
        body {
            overflow-x: hidden;
            max-width: 100vw;
        }

        .hero-pattern {
            background-image: radial-gradient(var(--color-primary-500) 0.5px, transparent 0.5px), radial-gradient(var(--color-primary-500) 0.5px, transparent 0.5px);
            background-size: 24px 24px;
            background-position: 0 0, 12px 12px;
            opacity: 0.12;
        }

        .dark .hero-pattern {
            opacity: 0.18;
        }

        .hero-grid-overlay {
            --grid-opacity: 0.16;
            background-image:
                linear-gradient(to right, var(--color-primary-500) 1px, transparent 1px),
                linear-gradient(to bottom, var(--color-primary-500) 1px, transparent 1px);
            background-size: 40px 40px;
            opacity: 0;
            -webkit-mask-image: radial-gradient(ellipse 65% 60% at 50% 0%, black 0%, black 25%, transparent 80%);
            mask-image: radial-gradient(ellipse 65% 60% at 50% 0%, black 0%, black 25%, transparent 80%);
            animation: hero-grid-fade-in 2.4s ease-out .2s forwards;
        }

        .dark .hero-grid-overlay {
            --grid-opacity: 0.25;
        }

        @keyframes hero-grid-fade-in {
            from { opacity: 0; }
            to { opacity: var(--grid-opacity); }
        }

        @media (prefers-reduced-motion: reduce) {
            .hero-grid-overlay {
                animation: none;
                opacity: var(--grid-opacity);
            }
        }

        .video-grid-reveal {
            --grid-opacity: 0.16;
            background-image:
                linear-gradient(to right, var(--color-primary-500) 1px, transparent 1px),
                linear-gradient(to bottom, var(--color-primary-500) 1px, transparent 1px);
            background-size: 40px 40px;
            opacity: 0;
            -webkit-mask-image: radial-gradient(ellipse 65% 60% at 50% 100%, black 0%, black 25%, transparent 80%);
            mask-image: radial-gradient(ellipse 65% 60% at 50% 100%, black 0%, black 25%, transparent 80%);
            animation: hero-grid-fade-in 2.4s ease-out .2s forwards;
        }

        .dark .video-grid-reveal {
            --grid-opacity: 0.25;
        }

        @media (prefers-reduced-motion: reduce) {
            .video-grid-reveal {
                animation: none;
                opacity: var(--grid-opacity);
            }
        }

        .hero-blob {
            border-radius: 42% 58% 65% 35% / 45% 40% 60% 55%;
            animation: hero-blob-breathe 15s ease-in-out infinite;
            transform-origin: center;
        }

        @keyframes hero-blob-breathe {
            0%, 100% { transform: scale(1) translate(0, 0); }
            50% { transform: scale(1.06) translate(-12px, 10px); }
        }

        @keyframes hero-fade-up {
            from { opacity: 0; transform: translateY(34px); filter: blur(12px); }
            to { opacity: 1; transform: translateY(0); filter: blur(0); }
        }

        @keyframes hero-fade-scale {
            from { opacity: 0; transform: translateY(44px) scale(.94); filter: blur(16px); }
            to { opacity: 1; transform: translateY(0) scale(1); filter: blur(0); }
        }

        .hero-animate {
            opacity: 0;
            filter: blur(12px);
            animation: hero-fade-up 1.4s cubic-bezier(0.22, 1, 0.36, 1) forwards;
        }

        .hero-animate-scale {
            opacity: 0;
            filter: blur(16px);
            animation: hero-fade-scale 1.6s cubic-bezier(0.22, 1, 0.36, 1) forwards;
        }

        .hero-delay-1 { animation-delay: .1s; }
        .hero-delay-2 { animation-delay: .4s; }
        .hero-delay-3 { animation-delay: .75s; }
        .hero-delay-4 { animation-delay: 1.1s; }
        .hero-delay-5 { animation-delay: 1.4s; }
        .hero-delay-6 { animation-delay: 1.7s; }

        .cta-glow {
            background-image: linear-gradient(110deg,
                var(--color-primary-600) 0%,
                var(--color-primary-400) 25%,
                var(--color-primary-600) 50%,
                var(--color-primary-400) 75%,
                var(--color-primary-600) 100%);
            background-size: 250% 100%;
            animation: cta-shimmer 19s ease-in-out infinite, cta-glow-pulse 11s ease-in-out infinite;
        }

        .cta-glow:hover {
            background-image: linear-gradient(110deg,
                var(--color-primary-500) 0%,
                var(--color-primary-300) 25%,
                var(--color-primary-500) 50%,
                var(--color-primary-300) 75%,
                var(--color-primary-500) 100%);
        }

        @keyframes cta-shimmer {
            0% { background-position: 0% 50%; }
            22% { background-position: 65% 50%; }
            48% { background-position: 30% 50%; }
            71% { background-position: 100% 50%; }
            100% { background-position: 15% 50%; }
        }

        @keyframes cta-glow-pulse {
            0% { box-shadow: 0 8px 24px -6px rgba(212, 152, 30, .35); }
            18% { box-shadow: 0 8px 30px -5px rgba(212, 152, 30, .55); }
            40% { box-shadow: 0 8px 22px -6px rgba(212, 152, 30, .4); }
            63% { box-shadow: 0 8px 36px -4px rgba(212, 152, 30, .68); }
            85% { box-shadow: 0 8px 26px -6px rgba(212, 152, 30, .45); }
            100% { box-shadow: 0 8px 24px -6px rgba(212, 152, 30, .35); }
        }

        @media (prefers-reduced-motion: reduce) {
            .hero-animate,
            .hero-animate-scale {
                animation: none;
                opacity: 1;
                transform: none;
                filter: none;
            }

            .cta-glow {
                animation: none;
            }

            .hero-blob {
                animation: none;
            }
        }

        .text-gradient-gold {
            background: linear-gradient(135deg, var(--color-primary-700) 0%, var(--color-primary-400) 50%, var(--color-primary-600) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .dark .text-gradient-gold {
            background: linear-gradient(135deg, var(--color-primary-300) 0%, var(--color-primary-400) 50%, var(--color-primary-500) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(15, 15, 20, 0.06);
        }

        .dark .glass-card {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.07);
        }

        section {
            max-width: 100vw;
            overflow-x: hidden;
        }

        @media (max-width: 640px) {
            .xs\:hidden {
                display: none;
            }

            .xs\:inline {
                display: inline;
            }
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="font-[Outfit] antialiased bg-white dark:bg-zinc-950 text-slate-900 dark:text-zinc-50 selection:bg-amber-500 selection:text-white overflow-x-hidden transition-colors duration-300">

    {{ $slot }}

</body>

</html>
