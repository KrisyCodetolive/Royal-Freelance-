<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth overflow-x-hidden">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <title>Royal LeadPro - La plateforme SaaS de génération de leads</title>
    <meta name="description"
        content="Créez vos tunnels de vente, capturez et suivez vos leads, automatisez vos séquences email. Royal LeadPro s'adapte à votre équipe, du solo au workspace complet.">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:300,400,500,600,700|playfair-display:400,500,600,700,800"
        rel="stylesheet" />
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
    class="font-[Outfit] antialiased bg-white text-slate-900 selection:bg-amber-500 selection:text-white overflow-x-hidden">

    <!-- Navigation -->
    @include('components.leadpro.nav')

    <!-- Hero -->
    <x-leadpro.hero />

    <!-- Comment ça marche -->
    <x-leadpro.how />

    <!-- Fonctionnalités -->
    <x-leadpro.features />

    <!-- Tarifs (dynamique) -->
    <x-leadpro.pricing :plans="$plans" />

    <!-- FAQ -->
    <x-leadpro.faq />

    <!-- CTA finale -->
    <x-leadpro.cta />

    <!-- Footer -->
    <x-leadpro.footer />

</body>

</html>
