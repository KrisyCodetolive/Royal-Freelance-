<!-- Alpine.js for mobile menu toggle -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

@php
    // Les ancres #fonctionnalites/#tarifs n'existent que sur la home
    // (royalleadpro-landing.blade.php) : depuis une autre page, on renvoie
    // vers la page dédiée correspondante plutôt qu'une ancre qui n'existe
    // pas là où on se trouve. #faq n'a pas de page dédiée, direction la home.
    $onHome = request()->routeIs('home');
    $featuresLink = $onHome ? '#fonctionnalites' : route('features');
    $pricingLink = $onHome ? '#tarifs' : route('pricing');
    $faqLink = $onHome ? '#faq' : route('home') . '#faq';
@endphp

<nav class="fixed w-full z-50 transition-all duration-300 bg-white/80 dark:bg-zinc-950/70 backdrop-blur-xl border-b border-slate-100 dark:border-white/[0.06]"
    x-data="{ scrolled: false, mobileMenuOpen: false }" @scroll.window="scrolled = (window.pageYOffset > 20)">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 sm:h-20">
            <!-- Logo -->
            <div class="flex items-center flex-shrink-0">
                <a href="{{ route('home') }}" class="flex items-center">
                    <img src="{{ asset('assets/RoyalLeadPro/lOGOsvg_lOGOsvg.svg') }}" class="h-7 sm:h-20 w-auto dark:hidden"
                        alt="Royal LeadPro">
                    <img src="{{ asset('assets/RoyalLeadPro/logoLeadProBlanc.svg') }}" class="h-7 sm:h-9 w-auto hidden dark:block"
                        alt="Royal LeadPro">
                </a>
            </div>

            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center gap-8">
                <a href="{{ $featuresLink }}" class="text-sm font-medium text-slate-600 dark:text-zinc-400 hover:text-slate-900 dark:hover:text-white transition-colors">Fonctionnalités</a>
                <a href="{{ $pricingLink }}" class="text-sm font-medium text-slate-600 dark:text-zinc-400 hover:text-slate-900 dark:hover:text-white transition-colors">Tarifs</a>
                <a href="{{ $faqLink }}" class="text-sm font-medium text-slate-600 dark:text-zinc-400 hover:text-slate-900 dark:hover:text-white transition-colors">FAQ</a>

                <!-- Theme toggle -->
                <button @click="toggleTheme()" type="button" aria-label="Changer de thème"
                    class="flex items-center justify-center w-9 h-9 rounded-full text-slate-500 dark:text-zinc-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-white/10 transition-colors">
                    <svg class="w-[18px] h-[18px] hidden dark:block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                            d="M12 3v1.5m0 15V21m9-9h-1.5m-15 0H3m15.364-6.364l-1.06 1.06M6.696 17.304l-1.06 1.06m12.728 0l-1.06-1.06M6.696 6.696l-1.06-1.06M17 12a5 5 0 11-10 0 5 5 0 0110 0z" />
                    </svg>
                    <svg class="w-[18px] h-[18px] dark:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                            d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>

                @auth
                    <div class="flex items-center gap-4">
                        <span class="text-sm text-slate-600 dark:text-zinc-400">Bonjour, <span
                                class="font-semibold text-slate-900 dark:text-white">{{ Auth::user()->name }}</span></span>

                        <a href="{{ Auth::user()->isAdmin() ? '/admin' : route('commercial.dashboard') }}"
                            class="text-sm font-medium text-slate-900 dark:text-white hover:text-amber-600 dark:hover:text-amber-400 transition-colors">
                            Mon Espace
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="px-5 py-2.5 text-sm font-bold text-white dark:text-zinc-950 bg-slate-900 dark:bg-white rounded-lg shadow-lg shadow-slate-900/20 dark:shadow-none hover:bg-slate-800 dark:hover:bg-zinc-200 hover:shadow-xl hover:shadow-slate-900/30 transition-all duration-300 transform hover:-translate-y-0.5">
                                Déconnexion
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}"
                        class="text-sm font-medium text-slate-900 dark:text-white hover:text-amber-600 dark:hover:text-amber-400 transition-colors">Connexion</a>
                    <a href="{{ route('tenant.register') }}"
                        class="px-5 py-2.5 text-sm font-bold text-white dark:text-zinc-950 bg-slate-900 dark:bg-white rounded-lg shadow-lg shadow-slate-900/20 dark:shadow-none hover:bg-slate-800 dark:hover:bg-zinc-200 hover:shadow-xl hover:shadow-slate-900/30 transition-all duration-300 transform hover:-translate-y-0.5 whitespace-nowrap">
                        Commencer gratuitement
                    </a>
                @endauth
            </div>

            <!-- Mobile Menu Button -->
            <div class="md:hidden flex items-center gap-1 flex-shrink-0">
                <button @click="toggleTheme()" type="button" aria-label="Changer de thème"
                    class="flex items-center justify-center w-9 h-9 rounded-full text-slate-500 dark:text-zinc-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-white/10 transition-colors">
                    <svg class="w-[18px] h-[18px] hidden dark:block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                            d="M12 3v1.5m0 15V21m9-9h-1.5m-15 0H3m15.364-6.364l-1.06 1.06M6.696 17.304l-1.06 1.06m12.728 0l-1.06-1.06M6.696 6.696l-1.06-1.06M17 12a5 5 0 11-10 0 5 5 0 0110 0z" />
                    </svg>
                    <svg class="w-[18px] h-[18px] dark:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                            d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-slate-900 dark:text-white hover:text-amber-600 dark:hover:text-amber-400 p-2 -mr-2">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path x-show="mobileMenuOpen" x-cloak stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div x-show="mobileMenuOpen" x-cloak x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-1"
        class="md:hidden bg-white dark:bg-zinc-950 border-t border-slate-100 dark:border-white/[0.06] shadow-lg dark:shadow-none">
        <div class="px-4 pt-2 pb-6 space-y-2 max-h-[calc(100vh-4rem)] overflow-y-auto">
            <a href="{{ $featuresLink }}" class="block px-3 py-2 text-base font-medium text-slate-700 dark:text-zinc-300 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-slate-50 dark:hover:bg-white/5 rounded-md">Fonctionnalités</a>
            <a href="{{ $pricingLink }}" class="block px-3 py-2 text-base font-medium text-slate-700 dark:text-zinc-300 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-slate-50 dark:hover:bg-white/5 rounded-md">Tarifs</a>
            <a href="{{ $faqLink }}" class="block px-3 py-2 text-base font-medium text-slate-700 dark:text-zinc-300 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-slate-50 dark:hover:bg-white/5 rounded-md">FAQ</a>

            @auth
                <div class="px-3 py-3 border-b border-slate-50 dark:border-white/5 mb-2">
                    <span class="block text-xs text-slate-500 dark:text-zinc-500 uppercase tracking-wider font-semibold">Compte</span>
                    <span class="block text-base font-medium text-slate-900 dark:text-white truncate">{{ Auth::user()->name }}</span>
                    <span class="block text-sm text-slate-500 dark:text-zinc-400 truncate">{{ Auth::user()->email }}</span>
                </div>
                <a href="{{ Auth::user()->isAdmin() ? '/admin' : route('commercial.dashboard') }}"
                    class="block px-3 py-2 text-base font-medium text-slate-700 dark:text-zinc-300 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-slate-50 dark:hover:bg-white/5 rounded-md">
                    Accéder au Dashboard
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full text-left block px-3 py-2 text-base font-medium text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-md">
                        Déconnexion
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}"
                    class="block px-3 py-2 text-base font-medium text-slate-700 dark:text-zinc-300 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-slate-50 dark:hover:bg-white/5 rounded-md">
                    Connexion
                </a>
                <a href="{{ route('tenant.register') }}"
                    class="block px-3 py-2 text-base font-medium text-white bg-amber-600 hover:bg-amber-500 rounded-md text-center">
                    Commencer gratuitement
                </a>
            @endauth
        </div>
    </div>
</nav>
