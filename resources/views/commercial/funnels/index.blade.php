@extends('commercial.layouts.app')

@section('title', 'Mes Tunnels')
@section('header', 'Mes Tunnels')

@section('content')
    <div class="mb-8">
        <p
            class="text-sm font-medium text-slate-500 max-w-2xl bg-white p-4 rounded-lg border border-slate-100 shadow-sm leading-relaxed">
            <span class="text-amber-600 font-bold mr-1">💡 Astuce :</span>
            Voici les tunnels mis à votre disposition. Configurez-les pour personnaliser vos liens de partage et vos
            messages WhatsApp.
            Attribuez-vous des leads automatiquement en partageant <strong>VOTRE</strong> lien unique.
        </p>
    </div>

    <div x-data="{ 
        search: '', 
        filter: 'all',
        matches(name, active) {
            const matchesSearch = name.toLowerCase().includes(this.search.toLowerCase());
            const matchesFilter = this.filter === 'all' || 
                                 (this.filter === 'active' && active) || 
                                 (this.filter === 'inactive' && !active);
            return matchesSearch && matchesFilter;
        },
        get visibleCount() {
            return Array.from(document.querySelectorAll('.funnel-card')).filter(el => el.style.display !== 'none').length;
        }
    }">
        <!-- Recherche et Filtres -->
        <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <!-- Barre de recherche -->
            <div class="relative flex-1 max-w-md w-full">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" x-model="search" placeholder="Rechercher un tunnel..."
                    class="block w-full pl-10 pr-3 py-2.5 border border-slate-200 rounded-xl leading-5 bg-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent sm:text-sm transition-all duration-200 shadow-sm">
            </div>

            <!-- Filtres (Onglets) -->
            <div class="flex items-center bg-slate-200/50 p-1 rounded-xl w-fit">
                <button @click="filter = 'all'"
                    :class="filter === 'all' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                    class="px-4 py-1.5 text-sm font-medium rounded-lg transition-all duration-200">
                    Tous
                </button>
                <button @click="filter = 'active'"
                    :class="filter === 'active' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                    class="px-4 py-1.5 text-sm font-medium rounded-lg transition-all duration-200">
                    Activés
                </button>
                <button @click="filter = 'inactive'"
                    :class="filter === 'inactive' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                    class="px-4 py-1.5 text-sm font-medium rounded-lg transition-all duration-200">
                    Inactifs
                </button>
            </div>
        </div>

        @if($funnels->count() > 0)
            <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($funnels as $funnel)
                    @php
                        $isActive = in_array($funnel->id, $activatedFunnelIds);
                    @endphp
                    <div x-show="matches('{{ addslashes($funnel->name) }}', {{ $isActive ? 'true' : 'false' }})"
                        x-transition.opacity
                        class="funnel-card group relative flex flex-col overflow-hidden rounded-xl bg-white shadow-sm border border-slate-100 hover:shadow-lg hover:border-amber-200 transition-all duration-300">
                        <!-- Thumbnail -->
                        <div class="aspect-video w-full bg-slate-100 relative overflow-hidden">
                            @if($funnel->thumbnail)
                                <img src="{{ Storage::url($funnel->thumbnail) }}" alt="{{ $funnel->name }}"
                                    class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                            @else
                                <div
                                    class="absolute inset-0 flex items-center justify-center bg-gradient-to-br from-indigo-50 to-slate-100">
                                    <svg class="h-16 w-16 text-slate-300 group-hover:text-amber-500/50 transition-colors duration-300"
                                        fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 0 1-.659 1.591l-5.432 5.432a2.25 2.25 0 0 0-.659 1.591v2.927a2.25 2.25 0 0 1-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 0 0-.659-1.591L3.659 7.409A2.25 2.25 0 0 1 3 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0 1 12 3Z" />
                                    </svg>
                                </div>
                            @endif

                            <!-- Status badge -->
                            @if($isActive)
                                <div class="absolute top-3 right-3">
                                    <span
                                        class="inline-flex items-center rounded-full bg-emerald-500/90 backdrop-blur-sm px-2.5 py-1 text-xs font-bold text-white shadow-sm border border-emerald-400/50">
                                        <svg class="mr-1 h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="3"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75 6 6 9-13.5" />
                                        </svg>
                                        Activé
                                    </span>
                                </div>
                            @else
                                <div class="absolute top-3 right-3">
                                    <span
                                        class="inline-flex items-center rounded-full bg-slate-500/80 backdrop-blur-sm px-2.5 py-1 text-xs font-medium text-white shadow-sm">
                                        Inactif
                                    </span>
                                </div>
                            @endif

                            <!-- Overlay on hover -->
                            <div
                                class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent p-4 opacity-0 transition-opacity duration-300 group-hover:opacity-100 flex items-end justify-between">
                                <span class="text-white text-xs font-medium">Voir la page &rarr;</span>
                            </div>
                        </div>

                        <div class="flex flex-1 flex-col justify-between p-5">
                            <div>
                                <h3
                                    class="text-lg font-sans font-bold text-slate-900 line-clamp-1 group-hover:text-amber-600 transition-colors">
                                    {{ $funnel->name }}
                                </h3>

                                <div class="mt-4 grid grid-cols-3 gap-2">
                                    <!-- Pages -->
                                    <div
                                        class="flex flex-col items-center justify-center p-2 rounded-lg bg-slate-50 border border-slate-100">
                                        <span class="text-xs text-slate-400 font-medium uppercase">Pages</span>
                                        <span class="text-lg font-bold text-slate-700">{{ $funnel->pages_count ?? 0 }}</span>
                                    </div>
                                    <!-- Vues (Clics) -->
                                    <div
                                        class="flex flex-col items-center justify-center p-2 rounded-lg bg-indigo-50 border border-indigo-100">
                                        <span class="text-xs text-indigo-400 font-medium uppercase">Vues</span>
                                        <span
                                            class="text-lg font-bold text-indigo-700">{{ $funnel->my_views_count ?? 0 }}</span>
                                    </div>
                                    <!-- Leads -->
                                    <div
                                        class="flex flex-col items-center justify-center p-2 rounded-lg bg-amber-50 border border-amber-100">
                                        <span class="text-xs text-amber-500 font-medium uppercase">Leads</span>
                                        <span
                                            class="text-lg font-bold text-amber-700">{{ $funnel->my_leads_count ?? 0 }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 flex flex-col gap-2">
                                <div class="flex gap-2">
                                    <a href="{{ route('commercial.funnel.configure', $funnel) }}"
                                        class="flex-1 inline-flex justify-center items-center rounded-lg bg-slate-900 px-3 py-2 text-sm font-semibold text-white shadow-md hover:bg-slate-800 transition-all duration-200">
                                        <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 0 1 1.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.559.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.894.149c-.424.07-.764.383-.929.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 0 1-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.398.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 0 1-.12-1.45l.527-.737c.25-.35.272-.806.108-1.204-.165-.397-.506-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.108-1.204l-.526-.738a1.125 1.125 0 0 1 .12-1.45l.773-.773a1.125 1.125 0 0 1 1.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                        Configurer
                                    </a>

                                    <a href="{{ $funnel->getPublicUrl() }}" target="_blank"
                                        class="inline-flex justify-center items-center rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 hover:text-slate-900 transition-colors"
                                        title="Voir le tunnel en ligne">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                        </svg>
                                    </a>
                                </div>

                                @if(($funnel->my_leads_count ?? 0) > 0)
                                    <a href="{{ route('commercial.leads', ['funnel_id' => $funnel->id]) }}"
                                        class="w-full inline-flex justify-center items-center rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-sm font-medium text-amber-700 hover:bg-amber-100 transition-colors">
                                        <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                        </svg>
                                        Voir les Leads
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Message Aucun résultat (Filtre/Recherche) -->
            <div x-show="Object.values($el.parentElement.querySelectorAll('.funnel-card')).every(el => el.style.display === 'none')"
                class="text-center py-20 bg-white rounded-xl shadow-sm border border-slate-100 mt-8">
                <div class="mx-auto h-24 w-24 rounded-full bg-slate-50 flex items-center justify-center mb-6">
                    <svg class="h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-medium text-slate-900">Aucun tunnel ne correspond à votre recherche</h3>
                <p class="mt-2 text-slate-500">Essayez de modifier vos filtres ou vos termes de recherche.</p>
            </div>
        @else
            <div class="text-center py-20 bg-white rounded-xl shadow-sm border border-slate-100">
                <div class="mx-auto h-24 w-24 rounded-full bg-slate-50 flex items-center justify-center mb-6">
                    <svg class="h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                </div>
                <h3 class="text-xl font-medium text-slate-900">Aucun tunnel disponible</h3>
                <p class="mt-2 text-slate-500">Contactez votre administrateur pour obtenir l'accès à vos premiers tunnels.
                </p>
            </div>
        @endif
    </div>
@endsection