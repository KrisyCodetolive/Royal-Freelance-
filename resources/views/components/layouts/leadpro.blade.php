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
            background-image:
                linear-gradient(to right, var(--color-primary-500) 1px, transparent 1px),
                linear-gradient(to bottom, var(--color-primary-500) 1px, transparent 1px);
            background-size: 40px 40px;
            opacity: 0.16;
            -webkit-mask-image: radial-gradient(ellipse 65% 60% at 50% 0%, black 0%, black 25%, transparent 80%);
            mask-image: radial-gradient(ellipse 65% 60% at 50% 0%, black 0%, black 25%, transparent 80%);
        }

        .dark .hero-grid-overlay {
            opacity: 0.25;
        }

        .hero-blob {
            border-radius: 42% 58% 65% 35% / 45% 40% 60% 55%;
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
