<!DOCTYPE html>
<html lang="fr" class="h-full bg-slate-50">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Espace Partenaire</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:300,400,500,600,700|playfair-display:400,500,600,700"
        rel="stylesheet" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        .font-serif {
            font-family: 'Playfair Display', serif;
        }

        .font-sans {
            font-family: 'Outfit', sans-serif;
        }

        @media (min-width: 1024px) {
            .sidebar-desktop {
                transition: width 0.3s ease;
            }
            .sidebar-desktop.is-collapsed { width: 5rem; }
            .sidebar-desktop.is-expanded  { width: 18rem; }

            .main-content {
                transition: padding-left 0.3s ease;
            }
            .main-content.sidebar-is-collapsed { padding-left: 5rem; }
            .main-content.sidebar-is-expanded  { padding-left: 18rem; }
        }
    </style>
    @livewireStyles
    @stack('styles')
</head>

<body class="h-full font-sans antialiased text-slate-900 bg-slate-50 overflow-x-hidden"
    x-data="{
        sidebarOpen: false,
        sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
        toggleCollapse() {
            this.sidebarCollapsed = !this.sidebarCollapsed;
            localStorage.setItem('sidebarCollapsed', this.sidebarCollapsed);
        }
    }">
    @php
        $unreadCount = \App\Models\Alert::where('user_id', Auth::id())->where('is_read', false)->count();
    @endphp
    <div class="min-h-full">
        <!-- Sidebar Desktop -->
        <div class="sidebar-desktop hidden lg:fixed lg:inset-y-0 lg:flex lg:flex-col z-50"
            :class="sidebarCollapsed ? 'is-collapsed' : 'is-expanded'">
            <div class="flex min-h-0 flex-1 flex-col bg-slate-900 border-r border-slate-800 shadow-2xl">

                <!-- Logo + toggle -->
                <div class="flex h-20 flex-shrink-0 items-center bg-slate-950 border-b border-slate-800 px-4"
                    :class="sidebarCollapsed ? 'justify-center' : 'justify-between px-6'">
                    <a href="/" x-show="!sidebarCollapsed">
                        <img src="{{ asset('assets/logowhite.png') }}" class="h-8 sm:h-10 w-auto" alt="Logo">
                    </a>
                    <button @click="toggleCollapse()"
                        class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-colors"
                        :title="sidebarCollapsed ? 'Agrandir' : 'Réduire'">
                        <svg class="h-5 w-5 transition-transform duration-300" :class="sidebarCollapsed ? 'rotate-180' : ''"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7M18 19l-7-7 7-7" />
                        </svg>
                    </button>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 space-y-1 py-6" :class="sidebarCollapsed ? 'px-2' : 'px-4'">
                    <p x-show="!sidebarCollapsed" class="px-2 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Principal</p>

                    <a href="{{ route('commercial.dashboard') }}" title="Tableau de bord"
                        class="group flex items-center py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('commercial.dashboard') ? 'bg-amber-500 text-slate-900 shadow-lg shadow-amber-500/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}"
                        :class="sidebarCollapsed ? 'justify-center px-3' : 'px-3'">
                        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('commercial.dashboard') ? 'text-slate-900' : 'text-slate-500 group-hover:text-amber-400' }}"
                            :class="sidebarCollapsed ? '' : 'mr-3'"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        <span x-show="!sidebarCollapsed" x-transition.opacity>Tableau de bord</span>
                    </a>

                    <p x-show="!sidebarCollapsed" class="px-2 text-xs font-semibold text-slate-500 uppercase tracking-wider mt-8 mb-2">Activités</p>

                    <a href="{{ route('commercial.funnels') }}" title="Mes Tunnels"
                        class="group flex items-center py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('commercial.funnels*') ? 'bg-amber-500 text-slate-900 shadow-lg shadow-amber-500/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}"
                        :class="sidebarCollapsed ? 'justify-center px-3' : 'px-3'">
                        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('commercial.funnels*') ? 'text-slate-900' : 'text-slate-500 group-hover:text-amber-400' }}"
                            :class="sidebarCollapsed ? '' : 'mr-3'"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                        </svg>
                        <span x-show="!sidebarCollapsed" x-transition.opacity>Mes Tunnels</span>
                    </a>

                    <a href="{{ route('commercial.leads') }}" title="Mes Leads"
                        class="group flex items-center py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('commercial.leads*') ? 'bg-amber-500 text-slate-900 shadow-lg shadow-amber-500/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}"
                        :class="sidebarCollapsed ? 'justify-center px-3' : 'px-3'">
                        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('commercial.leads*') ? 'text-slate-900' : 'text-slate-500 group-hover:text-amber-400' }}"
                            :class="sidebarCollapsed ? '' : 'mr-3'"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span x-show="!sidebarCollapsed" x-transition.opacity>Mes Leads</span>
                    </a>

                    <a href="{{ route('commercial.alerts') }}" title="Alertes"
                        class="group flex items-center py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('commercial.alerts*') ? 'bg-amber-500 text-slate-900 shadow-lg shadow-amber-500/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}"
                        :class="sidebarCollapsed ? 'justify-center px-3 relative' : 'justify-between px-3'">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('commercial.alerts*') ? 'text-slate-900' : 'text-slate-500 group-hover:text-amber-400' }}"
                                :class="sidebarCollapsed ? '' : 'mr-3'"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <span x-show="!sidebarCollapsed" x-transition.opacity>Alertes</span>
                        </div>
                        @if($unreadCount > 0)
                            <span id="alerts-badge-desktop"
                                class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-rose-500 rounded-full"
                                :class="sidebarCollapsed ? 'absolute -top-1 -right-1 px-1.5 py-0.5' : ''">
                                {{ $unreadCount }}
                            </span>
                        @else
                            <span id="alerts-badge-desktop" class="hidden"></span>
                        @endif
                    </a>

                    <p x-show="!sidebarCollapsed" class="px-2 text-xs font-semibold text-slate-500 uppercase tracking-wider mt-8 mb-2">Compte</p>

                    <a href="{{ route('commercial.profile') }}" title="Mon Profil"
                        class="group flex items-center py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('commercial.profile') ? 'bg-amber-500 text-slate-900 shadow-lg shadow-amber-500/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}"
                        :class="sidebarCollapsed ? 'justify-center px-3' : 'px-3'">
                        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('commercial.profile') ? 'text-slate-900' : 'text-slate-500 group-hover:text-amber-400' }}"
                            :class="sidebarCollapsed ? '' : 'mr-3'"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span x-show="!sidebarCollapsed" x-transition.opacity>Mon Profil</span>
                    </a>
                </nav>

                <!-- User info / Logout -->
                <div class="border-t border-slate-800 p-4 bg-slate-950">
                    <div class="flex items-center" :class="sidebarCollapsed ? 'justify-center' : 'w-full'">
                        <div class="shrink-0">
                            @if(auth()->user()->avatar)
                                <img class="h-10 w-10 rounded-full border-2 border-slate-700"
                                    src="{{ Storage::url(auth()->user()->avatar) }}" alt="">
                            @else
                                <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-slate-800 border-2 border-slate-700">
                                    <span class="text-sm font-medium leading-none text-amber-500">{{ substr(auth()->user()->name, 0, 2) }}</span>
                                </span>
                            @endif
                        </div>
                        <div class="ml-3 min-w-0 flex-1" x-show="!sidebarCollapsed" x-transition.opacity>
                            <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs font-medium text-slate-400 truncate">{{ auth()->user()->shop_name ?? 'Partenaire' }}</p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" x-show="!sidebarCollapsed" x-transition.opacity>
                            @csrf
                            <button type="submit"
                                class="ml-2 p-2 text-slate-400 hover:text-white rounded-full hover:bg-slate-800 transition-colors"
                                title="Déconnexion">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>

        <!-- Sidebar Mobile -->
        <div x-show="sidebarOpen" x-cloak class="relative z-50 lg:hidden" role="dialog" aria-modal="true">

            <!-- Backdrop -->
            <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm"></div>

            <div class="fixed inset-0 flex">
                <div x-show="sidebarOpen" x-transition:enter="transition ease-in-out duration-300 transform"
                    x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
                    x-transition:leave="transition ease-in-out duration-300 transform"
                    x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
                    class="relative mr-16 flex w-full max-w-xs flex-1" @click.away="sidebarOpen = false">

                    <!-- Close button -->
                    <div x-show="sidebarOpen" x-transition:enter="ease-in-out duration-300"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                        x-transition:leave="ease-in-out duration-300" x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="absolute left-full top-0 flex w-16 justify-center pt-5">
                        <button type="button" class="-m-2.5 p-2.5 text-white" @click="sidebarOpen = false">
                            <span class="sr-only">Close sidebar</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="flex grow flex-col bg-slate-900 border-r border-slate-800 shadow-2xl overflow-y-auto">
                        <!-- Logo -->
                        <div class="flex h-20 flex-shrink-0 items-center px-6 bg-slate-950 border-b border-slate-800">
                            <a href="/" class="flex items-center gap-3 group">
                                <img src="{{ asset('assets/logowhite.png') }}" class="h-8 w-auto" alt="Logo">
                            </a>
                        </div>

                        <!-- Navigation -->
                        <nav class="flex-1 space-y-1 px-4 py-6">
                            <p class="px-2 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Principal
                            </p>

                            <a href="{{ route('commercial.dashboard') }}"
                                class="group flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('commercial.dashboard') ? 'bg-amber-500 text-slate-900 shadow-lg shadow-amber-500/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                                <svg class="mr-3 h-5 w-5 {{ request()->routeIs('commercial.dashboard') ? 'text-slate-900' : 'text-slate-500 group-hover:text-amber-400' }}"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                </svg>
                                Tableau de bord
                            </a>

                            <p class="px-2 text-xs font-semibold text-slate-500 uppercase tracking-wider mt-8 mb-2">
                                Activités</p>

                            <a href="{{ route('commercial.funnels') }}"
                                class="group flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('commercial.funnels*') ? 'bg-amber-500 text-slate-900 shadow-lg shadow-amber-500/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                                <svg class="mr-3 h-5 w-5 {{ request()->routeIs('commercial.funnels*') ? 'text-slate-900' : 'text-slate-500 group-hover:text-amber-400' }}"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                </svg>
                                Mes Tunnels
                            </a>

                            <a href="{{ route('commercial.leads') }}"
                                class="group flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('commercial.leads*') ? 'bg-amber-500 text-slate-900 shadow-lg shadow-amber-500/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                                <svg class="mr-3 h-5 w-5 {{ request()->routeIs('commercial.leads*') ? 'text-slate-900' : 'text-slate-500 group-hover:text-amber-400' }}"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                Mes Leads
                            </a>

                            <a href="{{ route('commercial.alerts') }}"
                                class="group flex items-center justify-between px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('commercial.alerts*') ? 'bg-amber-500 text-slate-900 shadow-lg shadow-amber-500/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                                <div class="flex items-center">
                                    <svg class="mr-3 h-5 w-5 {{ request()->routeIs('commercial.alerts*') ? 'text-slate-900' : 'text-slate-500 group-hover:text-amber-400' }}"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                    Alertes
                                </div>
                                @if($unreadCount > 0)
                                    <span id="alerts-badge-mobile"
                                        class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-rose-500 rounded-full">
                                        {{ $unreadCount }}
                                    </span>
                                @else
                                    <span id="alerts-badge-mobile" class="hidden"></span>
                                @endif
                            </a>

                            <p class="px-2 text-xs font-semibold text-slate-500 uppercase tracking-wider mt-8 mb-2">
                                Compte</p>

                            <a href="{{ route('commercial.profile') }}"
                                class="group flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('commercial.profile') ? 'bg-amber-500 text-slate-900 shadow-lg shadow-amber-500/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                                <svg class="mr-3 h-5 w-5 {{ request()->routeIs('commercial.profile') ? 'text-slate-900' : 'text-slate-500 group-hover:text-amber-400' }}"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Mon Profil
                            </a>
                        </nav>

                        <!-- User info / Logout -->
                        <div class="border-t border-slate-800 p-4 bg-slate-950">
                            <div class="flex items-center w-full">
                                <div class="flex-shrink-0">
                                    @if(auth()->user()->avatar)
                                        <img class="h-10 w-10 rounded-full border-2 border-slate-700"
                                            src="{{ Storage::url(auth()->user()->avatar) }}" alt="">
                                    @else
                                        <span
                                            class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-slate-800 border-2 border-slate-700">
                                            <span
                                                class="text-sm font-medium leading-none text-amber-500">{{ substr(auth()->user()->name, 0, 2) }}</span>
                                        </span>
                                    @endif
                                </div>
                                <div class="ml-3 min-w-0 flex-1">
                                    <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                                    <p class="text-xs font-medium text-slate-400 truncate">
                                        {{ auth()->user()->shop_name ?? 'Partenaire' }}
                                    </p>
                                </div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="ml-2 p-2 text-slate-400 hover:text-white rounded-full hover:bg-slate-800 transition-colors">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Main content -->
        <div class="main-content flex flex-col flex-1 min-h-screen"
            :class="sidebarCollapsed ? 'sidebar-is-collapsed' : 'sidebar-is-expanded'">
            <!-- Top bar -->
            <div
                class="sticky top-0 z-40 flex min-h-16 lg:min-h-20 py-2 flex-shrink-0 items-center gap-x-4 border-b border-slate-200 bg-white/80 backdrop-blur-md px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8">
                <button type="button"
                    class="-m-2.5 p-2.5 text-slate-700 lg:hidden hover:text-amber-600 transition-colors"
                    @click="sidebarOpen = true">
                    <span class="sr-only">Open sidebar</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                        aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>

                <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
                    <div class="flex flex-1 items-center">
                        <h1 class="text-xl sm:text-2xl font-serif font-bold text-slate-900 break-words leading-tight">
                            @yield('header', 'Dashboard')</h1>
                    </div>
                    <div class="flex items-center gap-x-4 lg:gap-x-6">
                        <!-- Notifications -->
                        <button type="button" class="-m-2.5 p-2.5 text-slate-400 hover:text-slate-500">
                            <span class="sr-only">View notifications</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                            </svg>
                        </button>

                        <!-- Separator -->
                        <div class="hidden lg:block lg:h-6 lg:w-px lg:bg-slate-900/10" aria-hidden="true"></div>

                        <!-- Profile Dropdown (Simplified) -->
                        <div class="flex items-center gap-2">
                            <span class="hidden lg:flex lg:items-center">
                                <span class="text-sm font-semibold leading-6 text-slate-900"
                                    aria-hidden="true">{{ auth()->user()->first_name }}</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page content -->
            <main class="py-10">
                <div class="px-4 sm:px-6 lg:px-8">
                    <!-- Flash messages -->
                    @if(session('success'))
                        <div
                            class="mb-6 rounded-lg bg-emerald-50 p-4 border border-emerald-100 flex gap-3 items-start animate-fade-in-up shadow-sm">
                            <svg class="h-5 w-5 text-emerald-500 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                            <p class="text-sm font-medium text-emerald-800">{{ session('success') }}</p>
                        </div>
                    @endif

                    @if(session('error'))
                        <div
                            class="mb-6 rounded-lg bg-red-50 p-4 border border-red-100 flex gap-3 items-start animate-fade-in-up shadow-sm">
                            <svg class="h-5 w-5 text-red-500 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>
    @livewireScripts
    @stack('scripts')
</body>

</html>