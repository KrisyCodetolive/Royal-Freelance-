<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name')) - Royal LeadPro</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:300,400,500,600,700|playfair-display:400,500,600,700"
        rel="stylesheet" />
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="flex flex-col min-h-full font-[Outfit] text-slate-900 antialiased">

    <!-- Navbar -->
    @include('components.landing.nav')

    <!-- Contenu Principal -->
    <main class="flex-grow pt-20">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 py-16">
            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-100">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex gap-6 text-sm text-slate-500">
                    <a href="{{ route('guest.privacy') }}" class="hover:text-amber-600 transition-colors">Politique de
                        Confidentialité</a>
                    <a href="{{ route('guest.terms') }}" class="hover:text-amber-600 transition-colors">CGU</a>
                    <a href="{{ route('guest.support') }}" class="hover:text-amber-600 transition-colors">Support</a>
                </div>
                <p class="text-xs text-slate-400">&copy; {{ date('Y') }} Royal LeadPro. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

</body>

</html>