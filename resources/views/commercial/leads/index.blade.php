@extends('commercial.layouts.app')

@section('title', 'Mes Leads')
@section('header', 'Mes Leads')

@section('content')
    {{-- Header avec Toggle View --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center gap-3 sm:justify-between">
        <div>
            <h2 class="text-xl sm:text-2xl font-serif font-bold text-slate-900">Mes Leads</h2>
            <p class="text-sm text-slate-500 mt-0.5">{{ $leads->total() }} prospects au total</p>
        </div>
        <div class="inline-flex rounded-lg border border-slate-200 p-1 bg-white shadow-sm self-start sm:self-auto">
            <button class="flex items-center px-3 py-1.5 text-sm font-medium text-white bg-amber-500 rounded-md shadow-sm">
                <svg class="h-4 w-4 sm:mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <span class="hidden sm:inline">Liste</span>
            </button>
            <a href="{{ route('commercial.leads.kanban') }}"
               class="flex items-center px-3 py-1.5 text-sm font-medium text-slate-600 hover:text-slate-900 rounded-md transition-colors">
                <svg class="h-4 w-4 sm:mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 0v10" />
                </svg>
                <span class="hidden sm:inline">Kanban</span>
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="mb-6 bg-white p-4 sm:p-5 rounded-xl shadow-sm border border-slate-100">
        <form method="GET" class="flex flex-col sm:flex-row sm:flex-wrap lg:flex-nowrap items-stretch sm:items-end gap-3 sm:gap-4">
            <div class="w-full lg:flex-1">
                <label for="search" class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Recherche</label>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-4 w-4 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                           <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                        placeholder="Nom, email ou téléphone..."
                        class="block w-full rounded-lg border-gray-200 pl-9 bg-slate-50 text-sm focus:border-amber-500 focus:ring-amber-500 py-2.5">
                </div>
            </div>

            <div class="flex gap-3 sm:gap-4">
                <div class="flex-1 sm:w-40 lg:w-48">
                    <label for="status" class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Statut</label>
                    <select name="status" id="status" class="block w-full rounded-lg border-gray-200 bg-slate-50 text-sm focus:border-amber-500 focus:ring-amber-500 py-2.5">
                        <option value="">Tous</option>
                        <option value="hot" {{ ($filters['status'] ?? '') === 'hot' ? 'selected' : '' }}>🔥 Chauds</option>
                        <option value="warm" {{ ($filters['status'] ?? '') === 'warm' ? 'selected' : '' }}>☀️ Tièdes</option>
                        <option value="cold" {{ ($filters['status'] ?? '') === 'cold' ? 'selected' : '' }}>❄️ Froids</option>
                        <option value="converted" {{ ($filters['status'] ?? '') === 'converted' ? 'selected' : '' }}>✓ Convertis</option>
                    </select>
                </div>

                <div class="flex-1 sm:w-48 lg:w-56">
                    <label for="funnel_id" class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Tunnel source</label>
                    <select name="funnel_id" id="funnel_id" class="block w-full rounded-lg border-gray-200 bg-slate-50 text-sm focus:border-amber-500 focus:ring-amber-500 py-2.5">
                        <option value="">Tous les tunnels</option>
                        @foreach($funnels as $funnel)
                            <option value="{{ $funnel->id }}" {{ ($filters['funnel_id'] ?? '') == $funnel->id ? 'selected' : '' }}>
                                {{ $funnel->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-2 sm:flex-shrink-0">
                <button type="submit" class="flex-1 sm:flex-none inline-flex items-center justify-center rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-800 transition-colors">
                    Filtrer
                </button>
                @if(request()->hasAny(['status', 'funnel_id', 'search']))
                    <a href="{{ route('commercial.leads') }}" class="flex-1 sm:flex-none inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                        Réinit.
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Leads -->
    <div class="bg-white shadow-sm border border-slate-100 rounded-xl overflow-hidden">
        @if($leads->count() > 0)

            {{-- Table — desktop --}}
            <div class="hidden sm:block overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Lead</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Source</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Score</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Date</th>
                            <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Contact</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-100">
                        @foreach($leads as $lead)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-slate-100 to-slate-200 border border-white shadow-sm flex-shrink-0">
                                            <span class="text-xs font-bold text-slate-600">{{ strtoupper(substr($lead->name ?? $lead->email, 0, 2)) }}</span>
                                        </span>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-slate-900">{{ $lead->name ?? 'Sans nom' }}</div>
                                            <div class="text-xs text-slate-500">{{ $lead->email }}</div>
                                            @if($lead->phone)
                                                <div class="text-xs text-slate-400 mt-0.5">{{ $lead->phone }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-2 w-2 rounded-full bg-amber-400 mr-2"></div>
                                        <div class="text-sm text-slate-700">{{ $lead->funnel?->name ?? 'Inconnu' }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($lead->converted_at)
                                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">✓ Converti</span>
                                    @elseif($lead->score >= 60)
                                        <span class="inline-flex items-center rounded-full bg-rose-50 px-2.5 py-1 text-xs font-medium text-rose-700 ring-1 ring-inset ring-rose-600/10">🔥 {{ $lead->score }} pts</span>
                                    @elseif($lead->score >= 30)
                                        <span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-600/10">☀️ {{ $lead->score }} pts</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600 ring-1 ring-inset ring-slate-500/10">❄️ {{ $lead->score }} pts</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                    {{ $lead->created_at->format('d/m/Y') }}
                                    <span class="text-xs text-slate-400 block">{{ $lead->created_at->format('H:i') }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-3">
                                        @if($lead->phone)
                                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $lead->phone) }}"
                                            target="_blank"
                                            class="flex items-center justify-center h-8 w-8 rounded-full bg-green-50 text-green-600 hover:bg-green-100 transition-all"
                                            title="WhatsApp">
                                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                                </svg>
                                            </a>
                                        @endif
                                        @if($lead->email)
                                            <a href="mailto:{{ $lead->email }}"
                                            class="flex items-center justify-center h-8 w-8 rounded-full bg-slate-100 text-slate-600 hover:bg-amber-100 hover:text-amber-700 transition-all"
                                            title="Email">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                                                </svg>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Cards — mobile --}}
            <ul class="sm:hidden divide-y divide-slate-100">
                @foreach($leads as $lead)
                    <li class="px-4 py-4">
                        <div class="flex items-start gap-3">
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-slate-100 to-slate-200 border border-white shadow-sm flex-shrink-0 mt-0.5">
                                <span class="text-xs font-bold text-slate-600">{{ strtoupper(substr($lead->name ?? $lead->email, 0, 2)) }}</span>
                            </span>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-slate-900 truncate">{{ $lead->name ?? 'Sans nom' }}</p>
                                        <p class="text-xs text-slate-500 truncate">{{ $lead->email }}</p>
                                        @if($lead->phone)
                                            <p class="text-xs text-slate-400 mt-0.5">{{ $lead->phone }}</p>
                                        @endif
                                    </div>
                                    <div class="flex-shrink-0">
                                        @if($lead->converted_at)
                                            <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700">✓ Converti</span>
                                        @elseif($lead->score >= 60)
                                            <span class="inline-flex items-center rounded-full bg-rose-50 px-2 py-0.5 text-xs font-medium text-rose-700">🔥 {{ $lead->score }}</span>
                                        @elseif($lead->score >= 30)
                                            <span class="inline-flex items-center rounded-full bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700">☀️ {{ $lead->score }}</span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600">❄️ {{ $lead->score }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="mt-2 flex items-center justify-between">
                                    <div class="flex items-center gap-1 text-xs text-slate-500 min-w-0">
                                        <div class="h-1.5 w-1.5 rounded-full bg-amber-400 flex-shrink-0"></div>
                                        <span class="truncate">{{ $lead->funnel?->name ?? 'Inconnu' }}</span>
                                        <span class="text-slate-300 flex-shrink-0">·</span>
                                        <span class="flex-shrink-0">{{ $lead->created_at->format('d/m') }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 flex-shrink-0">
                                        @if($lead->phone)
                                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $lead->phone) }}"
                                               target="_blank"
                                               class="flex items-center justify-center h-7 w-7 rounded-full bg-green-50 text-green-600 hover:bg-green-100 transition-all">
                                                <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                                </svg>
                                            </a>
                                        @endif
                                        @if($lead->email)
                                            <a href="mailto:{{ $lead->email }}"
                                               class="flex items-center justify-center h-7 w-7 rounded-full bg-slate-100 text-slate-500 hover:bg-amber-100 hover:text-amber-700 transition-all">
                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                                                </svg>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>

            <div class="px-4 sm:px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $leads->withQueryString()->links() }}
            </div>
        @else
            <div class="text-center py-16 px-4">
                <div class="mx-auto h-24 w-24 rounded-full bg-slate-50 flex items-center justify-center mb-4">
                    <svg class="h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <h3 class="text-lg font-serif font-medium text-slate-900">Aucun lead trouvé</h3>
                <p class="mt-2 text-sm text-slate-500 max-w-sm mx-auto">
                    @if(!empty($filters))
                        Aucun résultat avec les filtres actuels. Essayez de <a href="{{ route('commercial.leads') }}" class="text-amber-600 font-medium hover:underline">réinitialiser</a>.
                    @else
                        Vos tunnels n'ont pas encore généré de leads. Partagez vos liens pour commencer !
                    @endif
                </p>
            </div>
        @endif
    </div>
@endsection
