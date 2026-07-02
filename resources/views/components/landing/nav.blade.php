<!-- Alpine.js for mobile menu toggle -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<nav class="fixed w-full z-50 transition-all duration-300 bg-white/90 backdrop-blur-md border-b border-slate-100"
    x-data="{ scrolled: false, mobileMenuOpen: false }" @scroll.window="scrolled = (window.pageYOffset > 20)">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 sm:h-20">
            <!-- Logo -->
            <div class="flex items-center gap-1.5 sm:gap-2 flex-shrink-0">
                <a href="/" class="flex items-center gap-1.5 sm:gap-2">
                    <img src="{{ asset('assets/logoblack.png') }}" class="h-8 sm:h-10 w-auto" alt="Logo">

                </a>
            </div>

            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center space-x-8">
                @auth
                    <div class="flex items-center gap-4">
                        <span class="text-sm text-slate-600">Bonjour, <span
                                class="font-semibold text-slate-900">{{ Auth::user()->name }}</span></span>

                        <a href="{{ Auth::user()->isAdmin() ? '/admin' : route('commercial.dashboard') }}"
                            class="text-sm font-medium text-slate-900 hover:text-amber-600 transition-colors">
                            Mon Espace
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="px-5 py-2.5 text-sm font-bold text-white bg-slate-900 rounded-lg shadow-lg shadow-slate-900/20 hover:bg-slate-800 hover:shadow-xl hover:shadow-slate-900/30 transition-all duration-300 transform hover:-translate-y-0.5">
                                Déconnexion
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}"
                        class="text-sm font-medium text-slate-900 hover:text-amber-600 transition-colors">Connexion</a>
                    <a href="{{ route('register') }}"
                        class="px-5 py-2.5 text-sm font-bold text-white bg-slate-900 rounded-lg shadow-lg shadow-slate-900/20 hover:bg-slate-800 hover:shadow-xl hover:shadow-slate-900/30 transition-all duration-300 transform hover:-translate-y-0.5 whitespace-nowrap">
                        DEVENIR PARTENAIRE
                    </a>
                @endauth
            </div>

            <!-- Mobile Menu Button -->
            <div class="md:hidden flex-shrink-0">
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-slate-900 hover:text-amber-600 p-2 -mr-2">
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
        class="md:hidden bg-white border-t border-slate-100 shadow-lg">
        <div class="px-4 pt-2 pb-6 space-y-2 max-h-[calc(100vh-4rem)] overflow-y-auto">
            @auth
                <div class="px-3 py-3 border-b border-slate-50 mb-2">
                    <span class="block text-xs text-slate-500 uppercase tracking-wider font-semibold">Compte</span>
                    <span class="block text-base font-medium text-slate-900 truncate">{{ Auth::user()->name }}</span>
                    <span class="block text-sm text-slate-500 truncate">{{ Auth::user()->email }}</span>
                </div>
                <a href="{{ Auth::user()->isSuperAdmin() ? '/admin' : route('commercial.dashboard') }}"
                    class="block px-3 py-2 text-base font-medium text-slate-700 hover:text-amber-600 hover:bg-slate-50 rounded-md">
                    Accéder au Dashboard
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full text-left block px-3 py-2 text-base font-medium text-red-600 hover:text-red-700 hover:bg-red-50 rounded-md">
                        Déconnexion
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}"
                    class="block px-3 py-2 text-base font-medium text-slate-700 hover:text-amber-600 hover:bg-slate-50 rounded-md">
                    Connexion
                </a>
                <a href="{{ route('register') }}"
                    class="block px-3 py-2 text-base font-medium text-white bg-amber-600 hover:bg-amber-700 rounded-md text-center">
                    Créer un compte Partenaire
                </a>
            @endauth
        </div>
    </div>
</nav>