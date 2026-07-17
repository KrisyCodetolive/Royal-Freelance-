<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name')) - Espace Commercial</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:300,400,500,600,700" rel="stylesheet" />
    <style>
        [x-cloak] {
            display: none !important;
        }

        .fade-in {
            animation: fadeIn 0.6s ease-out forwards;
            opacity: 0;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-full font-[Outfit] text-slate-900 antialiased">
    <div class="flex min-h-full">
        <!-- Section Gauche : Branding & Inspiration -->
        <div class="relative hidden w-0 lg:block lg:w-[55%] xl:w-[50%]">
            <!-- Image de fond premium -->
            <img class="absolute inset-0 h-full w-full object-cover" src="{{ asset('assets/coach.jpg') }}"
                alt="Collaboration Commerciale">

            <!-- Dégradé Overlay -->
            <div
                class="absolute inset-0 bg-gradient-to-br from-indigo-900/95 via-purple-900/80 to-slate-900/90 mix-blend-multiply">
            </div>

            <!-- Contenu Branding -->
            <div class="absolute inset-0 flex flex-col justify-between p-12 text-white">
                <!-- Logo Top Left -->
                <div class="fade-in" style="animation-delay: 0.1s;">
                    <a href="/" class="inline-flex items-center gap-2 group">
                        <img src="{{ asset('assets/RoyalLeadPro/logoLeadProBlanc.svg') }}" class="h-10 w-auto" alt="Royal LeadPro">
                    </a>
                </div>

                <!-- Hero Text & Social Proof -->
                <div class="mb-10 fade-in" style="animation-delay: 0.3s;">
                    <h2 class="text-4xl xl:text-5xl font-bold tracking-tight leading-tight mb-6">
                        Développez votre <br>
                        <span
                            class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-200 to-purple-200">Empire
                            Commercial.</span>
                    </h2>
                    <p class="text-lg text-indigo-100/90 max-w-xl mb-12 font-light leading-relaxed hidden xl:block">
                        Accédez à une suite d'outils de génération de leads conçue pour l'excellence.
                        Rejoignez l'élite des commerciaux qui transforment chaque opportunité en succès.
                    </p>

                    <!-- Témoignage Card -->
                    <div
                        class="relative bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20 shadow-2xl">
                        <div class="flex items-start gap-4">
                            <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80"
                                class="h-12 w-12 rounded-full ring-2 ring-white/50 object-cover" alt="Avatar">
                            <div>
                                <div class="flex text-yellow-400 mb-1">
                                    ★★★★★
                                </div>
                                <blockquote class="text-sm xl:text-base font-medium text-white mb-2">
                                    "Depuis que j'utilise cette plateforme, mon taux de conversion a augmenté de 150%.
                                    C'est l'outil que j'attendais."
                                </blockquote>
                                <div class="text-xs xl:text-sm">
                                    <span class="font-bold text-white">Sophie M.</span>
                                    <span class="text-indigo-200 mx-1">•</span>
                                    <span class="text-indigo-200">Directrice Commerciale</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Links -->
                <div class="flex gap-6 text-sm text-indigo-200/60 fade-in" style="animation-delay: 0.5s;">
                    <a href="{{ route('guest.privacy') }}"
                        class="hover:text-white transition-colors">Confidentialité</a>
                    <a href="{{ route('guest.terms') }}" class="hover:text-white transition-colors">Conditions</a>
                    <a href="{{ route('guest.support') }}" class="hover:text-white transition-colors">Support</a>
                </div>
            </div>
        </div>

        <!-- Section Droite : Formulaire -->
        <div
            class="flex flex-1 flex-col justify-center px-4 py-12 sm:px-6 lg:px-12 xl:px-24 bg-gray-50 relative overflow-y-auto">
            <!-- Mobile Logo -->
            <div class="lg:hidden absolute top-6 left-6">
                <a href="/" class="flex items-center gap-2">
                    <img src="{{ asset('assets/RoyalLeadPro/lOGOsvg_lOGOsvg.svg') }}" class="h-10 w-auto" alt="Royal LeadPro">
                </a>
            </div>

            <div class="mx-auto w-full max-w-sm lg:w-96 fade-in" style="animation-delay: 0.2s;">
                <!-- Header Formulaire -->
                <div class="mb-10 text-center lg:text-left">
                    <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Espace Membre</h1>
                    <p class="mt-2 text-sm text-slate-600">
                        Connectez-vous pour accéder à vos outils.
                    </p>
                </div>

                @if(session('status'))
                    <div
                        class="mb-6 rounded-lg bg-emerald-50 p-4 border border-emerald-100 flex gap-3 items-start animate-fade-in-up">
                        <svg class="h-5 w-5 text-emerald-500 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        <p class="text-sm font-medium text-emerald-800">{{ session('status') }}</p>
                    </div>
                @endif

                @if($errors->any())
                    <div
                        class="mb-6 rounded-lg bg-red-50 p-4 border border-red-100 flex gap-3 items-start animate-fade-in-up">
                        <svg class="h-5 w-5 text-red-500 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                        <div class="text-sm font-medium text-red-800">
                            <p>Merci de corriger les erreurs suivantes :</p>
                            <ul class="list-disc list-inside mt-1 text-xs text-red-700">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <!-- Contenu Formulaire -->
                <div class="bg-white px-8 py-8 shadow-2xl shadow-slate-200/50 rounded-2xl ring-1 ring-slate-900/5">
                    @yield('content')
                </div>

                <p class="mt-8 text-center text-xs text-slate-400">
                    &copy; {{ date('Y') }} Royal LeadPro. Tous droits réservés.
                </p>
            </div>
        </div>
    </div>
</body>

</html>