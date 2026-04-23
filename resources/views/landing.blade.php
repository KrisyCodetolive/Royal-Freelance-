<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth overflow-x-hidden">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <title>Royal LeadPro - L'Excellence Commerciale</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:300,400,500,600,700|playfair-display:400,500,600,700,800"
        rel="stylesheet" />
    <style>
        [x-cloak] {
            display: none !important;
        }

        /* Prevent horizontal overflow */
        html,
        body {
            overflow-x: hidden;
            max-width: 100vw;
        }

        .hero-pattern {
            background-color: #ffffff;
            background-image: radial-gradient(var(--color-primary-500) 0.5px, transparent 0.5px), radial-gradient(var(--color-primary-500) 0.5px, #ffffff 0.5px);
            background-size: 20px 20px;
            background-position: 0 0, 10px 10px;
            opacity: 0.1;
        }

        .text-gradient-gold {
            background: linear-gradient(135deg, var(--color-primary-700) 0%, var(--color-primary-500) 50%, var(--color-primary-600) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Ensure all sections don't overflow */
        section {
            max-width: 100vw;
            overflow-x: hidden;
        }

        /* Mobile-first responsive utilities */
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
    class="font-[Outfit] antialiased bg-white text-slate-900 selection:bg-amber-500 selection:text-white overflow-x-hidden">

    <!-- Navigation -->
    @include('components.landing.nav')

    <!-- Hero Section -->
    <x-landing.hero />

    <!-- Coach Loukou Section -->
    <x-landing.coach />

    <!-- Royal Freelance Section -->
    <x-landing.royalfreelance />

    <!-- Problem/Solution Section -->
    <x-landing.why />

    <!-- 1000 Vendeurs Initiative Section -->
    <x-landing.1000vendeur />

    <!-- How It Works (Steps) -->
    <x-landing.how />

    <!-- Expanded Features Section -->
    <x-landing.features />

    <!-- FAQ Section -->
    <x-landing.faq />

    <!-- CTA Finale -->
    <x-landing.cta />

    <!-- Coach Banner Section -->
    <x-landing.banner />

    <!-- Footer -->
    <x-landing.footer />

</body>

</html>