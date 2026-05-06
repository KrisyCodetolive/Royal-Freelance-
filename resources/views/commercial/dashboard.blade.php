@extends('commercial.layouts.app')

@section('title', 'Tableau de Bord')
@section('header', 'Vue d\'ensemble')

@php
    $statusConfig = [
        'cold'      => ['label' => 'Froid',      'color' => 'bg-blue-100 text-blue-700',   'bar' => 'bg-blue-400',   'icon' => '❄️'],
        'warm'      => ['label' => 'Tiède',      'color' => 'bg-amber-100 text-amber-700', 'bar' => 'bg-amber-400',  'icon' => '☀️'],
        'hot'       => ['label' => 'Chaud',      'color' => 'bg-rose-100 text-rose-700',   'bar' => 'bg-rose-400',   'icon' => '🔥'],
        'ultra_hot' => ['label' => 'Ultra Chaud','color' => 'bg-purple-100 text-purple-700','bar' => 'bg-purple-500', 'icon' => '⚡'],
        'client'    => ['label' => 'Client',     'color' => 'bg-emerald-100 text-emerald-700','bar' => 'bg-emerald-500','icon' => '✅'],
        'member'    => ['label' => 'Membre',     'color' => 'bg-indigo-100 text-indigo-700','bar' => 'bg-indigo-400', 'icon' => '👤'],
    ];
    $totalStatusLeads = array_sum($leadsByStatus);
@endphp

@section('content')

{{-- ===== KPI CARDS ===== --}}
<div class="grid grid-cols-2 gap-3 sm:gap-5 sm:grid-cols-2 lg:grid-cols-5 mb-6 sm:mb-8">

    {{-- Total Leads --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 sm:p-5 hover:shadow-md transition-all">
        <div class="flex items-center justify-between mb-2 sm:mb-3">
            <div class="flex items-center justify-center h-8 w-8 sm:h-10 sm:w-10 rounded-lg bg-blue-50 text-blue-600">
                <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
            @if($leadsTrend !== null)
                <span class="text-xs font-semibold {{ $leadsTrend >= 0 ? 'text-emerald-600 bg-emerald-50' : 'text-rose-600 bg-rose-50' }} px-1.5 py-0.5 rounded-full">
                    {{ $leadsTrend >= 0 ? '↑' : '↓' }} {{ abs($leadsTrend) }}%
                </span>
            @endif
        </div>
        <div class="text-xl sm:text-2xl font-bold text-slate-900">{{ $stats['total_leads'] }}</div>
        <div class="text-xs text-slate-500 mt-0.5">Total Leads</div>
        <a href="{{ route('commercial.leads') }}" class="text-xs text-blue-600 hover:underline mt-1.5 block">Voir tous →</a>
    </div>

    {{-- Leads Chauds --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 sm:p-5 hover:shadow-md transition-all">
        <div class="flex items-center justify-between mb-2 sm:mb-3">
            <div class="flex items-center justify-center h-8 w-8 sm:h-10 sm:w-10 rounded-lg bg-rose-50 text-rose-600">
                <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z"/></svg>
            </div>
            @if($leadsToFollowUp->count() > 0)
                <span class="text-xs font-semibold text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded-full hidden sm:inline">
                    {{ $leadsToFollowUp->count() }} relancer
                </span>
            @endif
        </div>
        <div class="text-xl sm:text-2xl font-bold text-slate-900">{{ $stats['hot_leads'] }}</div>
        <div class="text-xs text-slate-500 mt-0.5">Leads Chauds</div>
        <a href="{{ route('commercial.leads.kanban') }}" class="text-xs text-rose-600 hover:underline mt-1.5 block">Pipeline →</a>
    </div>

    {{-- Clients Convertis --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 sm:p-5 hover:shadow-md transition-all">
        <div class="flex items-center justify-between mb-2 sm:mb-3">
            <div class="flex items-center justify-center h-8 w-8 sm:h-10 sm:w-10 rounded-lg bg-emerald-50 text-emerald-600">
                <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            @if($conversionsTrend !== null)
                <span class="text-xs font-semibold {{ $conversionsTrend >= 0 ? 'text-emerald-600 bg-emerald-50' : 'text-rose-600 bg-rose-50' }} px-1.5 py-0.5 rounded-full">
                    {{ $conversionsTrend >= 0 ? '↑' : '↓' }} {{ abs($conversionsTrend) }}%
                </span>
            @endif
        </div>
        <div class="text-xl sm:text-2xl font-bold text-slate-900">{{ $stats['conversions'] }}</div>
        <div class="text-xs text-slate-500 mt-0.5">Convertis</div>
        <a href="{{ route('commercial.leads') }}?status=converted" class="text-xs text-emerald-600 hover:underline mt-1.5 block">Voir →</a>
    </div>

    {{-- Taux de Conversion --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 sm:p-5 hover:shadow-md transition-all">
        <div class="flex items-center justify-between mb-2 sm:mb-3">
            <div class="flex items-center justify-center h-8 w-8 sm:h-10 sm:w-10 rounded-lg bg-indigo-50 text-indigo-600">
                <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
        </div>
        <div class="text-xl sm:text-2xl font-bold text-slate-900">{{ $conversionRate }}%</div>
        <div class="text-xs text-slate-500 mt-0.5">Taux Conv.</div>
        <div class="mt-2 w-full bg-slate-100 rounded-full h-1.5">
            <div class="bg-indigo-500 h-1.5 rounded-full transition-all" style="width: {{ min($conversionRate, 100) }}%"></div>
        </div>
    </div>

    {{-- Tunnels Actifs --}}
    <div class="col-span-2 sm:col-span-1 bg-white rounded-xl shadow-sm border border-slate-100 p-4 sm:p-5 hover:shadow-md transition-all">
        <div class="flex items-center justify-between mb-2 sm:mb-3">
            <div class="flex items-center justify-center h-8 w-8 sm:h-10 sm:w-10 rounded-lg bg-amber-50 text-amber-600">
                <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
            </div>
        </div>
        <div class="text-xl sm:text-2xl font-bold text-slate-900">{{ $stats['active_funnels'] }}</div>
        <div class="text-xs text-slate-500 mt-0.5">Tunnels Actifs</div>
        <a href="{{ route('commercial.funnels') }}" class="text-xs text-amber-600 hover:underline mt-1.5 block">Gérer →</a>
    </div>
</div>

{{-- ===== RÉPARTITION PAR STATUT ===== --}}
@if($totalStatusLeads > 0)
<div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 sm:p-6 mb-6 sm:mb-8">
    <h3 class="text-sm font-semibold text-slate-700 mb-4">Répartition du Pipeline</h3>
    <div class="flex rounded-full overflow-hidden h-3 mb-4">
        @foreach($statusConfig as $key => $cfg)
            @php $count = $leadsByStatus[$key] ?? 0; $pct = $totalStatusLeads > 0 ? $count / $totalStatusLeads * 100 : 0; @endphp
            @if($pct > 0)
                <div class="{{ $cfg['bar'] }} transition-all" style="width: {{ $pct }}%" title="{{ $cfg['label'] }}: {{ $count }}"></div>
            @endif
        @endforeach
    </div>
    <div class="flex flex-wrap gap-4">
        @foreach($statusConfig as $key => $cfg)
            @php $count = $leadsByStatus[$key] ?? 0; @endphp
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full {{ $cfg['bar'] }}"></span>
                <span class="text-xs text-slate-600">{{ $cfg['icon'] }} {{ $cfg['label'] }}</span>
                <span class="text-xs font-bold text-slate-900">{{ $count }}</span>
            </div>
        @endforeach
    </div>
</div>
@endif

{{-- ===== GRAPHIQUE + ALERTES ===== --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">

    {{-- Graphique --}}
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-slate-100 p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4 sm:mb-6">
            <h3 class="text-base font-semibold text-slate-900">Performance (30 jours)</h3>
            <div class="flex items-center gap-3 text-xs text-slate-500">
                <span class="flex items-center gap-1.5"><span class="w-3 h-0.5 bg-amber-400 inline-block rounded"></span> Nouveaux leads</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-0.5 bg-emerald-500 inline-block rounded"></span> Conversions</span>
            </div>
        </div>
        <div class="h-48 sm:h-72 relative w-full">
            <canvas id="leadsChart"></canvas>
        </div>
    </div>

    {{-- Alertes --}}
    @include('commercial.widgets.alerts', ['alerts' => $alerts, 'unreadCount' => $unreadCount])
</div>

{{-- ===== LEADS À RELANCER + DERNIERS LEADS ===== --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">

    {{-- Leads à relancer --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-5 border-b border-slate-50 flex items-center justify-between">
            <div>
                <h3 class="text-base font-semibold text-slate-900">Leads à Relancer</h3>
                <p class="text-xs text-slate-400 mt-0.5">Chauds / tièdes inactifs depuis +3 jours</p>
            </div>
            @if($leadsToFollowUp->count() > 0)
                <span class="bg-rose-100 text-rose-700 text-xs font-bold px-2.5 py-1 rounded-full">{{ $leadsToFollowUp->count() }}</span>
            @endif
        </div>

        @if($leadsToFollowUp->count() > 0)
            <ul class="divide-y divide-slate-50">
                @foreach($leadsToFollowUp as $lead)
                    <li class="px-5 py-3 hover:bg-slate-50/50 transition-colors">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-br
                                {{ $lead->status->value === 'ultra_hot' ? 'from-purple-100 to-purple-200' : ($lead->status->value === 'hot' ? 'from-rose-100 to-rose-200' : 'from-amber-100 to-amber-200') }}
                                text-xs font-bold text-slate-700 flex-shrink-0">
                                {{ strtoupper(substr($lead->name ?? $lead->email, 0, 2)) }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-slate-900 truncate">{{ $lead->name ?? $lead->email }}</p>
                                <p class="text-xs text-slate-400 truncate">
                                    {{ $lead->funnel?->name ?? 'Tunnel inconnu' }}
                                    &bull; Score: <span class="font-semibold text-slate-600">{{ $lead->score }}</span>
                                </p>
                            </div>
                            <div class="flex flex-col items-end gap-1 flex-shrink-0">
                                @php $cfg = $statusConfig[$lead->status->value] ?? null; @endphp
                                @if($cfg)
                                    <span class="text-xs px-2 py-0.5 rounded-full {{ $cfg['color'] }} font-medium">{{ $cfg['icon'] }} {{ $cfg['label'] }}</span>
                                @endif
                                @if($lead->last_activity_at)
                                    <span class="text-xs text-slate-400">{{ $lead->last_activity_at->diffForHumans() }}</span>
                                @else
                                    <span class="text-xs text-slate-400">Jamais actif</span>
                                @endif
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="p-10 text-center">
                <div class="text-3xl mb-2">🎉</div>
                <p class="text-sm font-medium text-slate-700">Aucun lead en attente</p>
                <p class="text-xs text-slate-400 mt-1">Tous vos leads chauds sont à jour !</p>
            </div>
        @endif
    </div>

    {{-- Derniers Prospects --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-5 border-b border-slate-50 flex items-center justify-between">
            <h3 class="text-base font-semibold text-slate-900">Derniers Prospects</h3>
            <a href="{{ route('commercial.leads') }}" class="text-xs font-medium text-amber-600 hover:text-amber-700">Tout voir →</a>
        </div>

        @if($recentLeads->count() > 0)
            <ul class="divide-y divide-slate-50">
                @foreach($recentLeads as $lead)
                    <li class="px-5 py-3 hover:bg-slate-50/50 transition-colors">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-br from-slate-100 to-slate-200 text-xs font-bold text-slate-600 flex-shrink-0">
                                {{ strtoupper(substr($lead->name ?? $lead->email, 0, 2)) }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-slate-900 truncate">{{ $lead->name ?? 'Prospect Sans Nom' }}</p>
                                <p class="text-xs text-slate-400 truncate">{{ $lead->email }}</p>
                            </div>
                            <div class="flex flex-col items-end gap-1 flex-shrink-0">
                                @php $cfg = $statusConfig[$lead->status->value] ?? null; @endphp
                                @if($cfg)
                                    <span class="text-xs px-2 py-0.5 rounded-full {{ $cfg['color'] }} font-medium">{{ $cfg['icon'] }} {{ $cfg['label'] }}</span>
                                @endif
                                <span class="text-xs text-slate-400">{{ $lead->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="p-10 text-center bg-slate-50/50">
                <div class="mx-auto h-12 w-12 rounded-full bg-slate-100 flex items-center justify-center mb-3">
                    <svg class="h-6 w-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                </div>
                <p class="text-sm font-medium text-slate-900">Aucun lead</p>
                <p class="text-xs text-slate-500 mt-1">Commencez à partager vos tunnels !</p>
            </div>
        @endif
    </div>
</div>

{{-- ===== ACTIONS RAPIDES ===== --}}
<div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-xl shadow-lg text-white p-6 relative overflow-hidden">
    <div class="absolute top-0 right-0 -mt-6 -mr-6 w-32 h-32 rounded-full bg-white/5 blur-xl"></div>
    <h3 class="text-base font-semibold mb-4 relative z-10">Actions Rapides</h3>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 relative z-10">
        <a href="{{ route('commercial.funnels') }}" class="group flex items-center p-3 rounded-lg bg-white/10 hover:bg-white/20 transition-all border border-white/5 hover:border-amber-500/30">
            <div class="p-2 bg-amber-500/20 rounded-lg mr-3 group-hover:bg-amber-500 transition-colors text-amber-400 group-hover:text-white">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
            </div>
            <div>
                <p class="font-medium text-sm">Mon lien</p>
                <p class="text-xs text-slate-400">Partager un tunnel</p>
            </div>
        </a>
        <a href="{{ route('commercial.leads.kanban') }}" class="group flex items-center p-3 rounded-lg bg-white/10 hover:bg-white/20 transition-all border border-white/5 hover:border-rose-500/30">
            <div class="p-2 bg-rose-500/20 rounded-lg mr-3 group-hover:bg-rose-500 transition-colors text-rose-400 group-hover:text-white">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/></svg>
            </div>
            <div>
                <p class="font-medium text-sm">Pipeline</p>
                <p class="text-xs text-slate-400">Vue Kanban</p>
            </div>
        </a>
        <a href="{{ route('commercial.alerts') }}" class="group flex items-center p-3 rounded-lg bg-white/10 hover:bg-white/20 transition-all border border-white/5 hover:border-blue-500/30">
            <div class="p-2 bg-blue-500/20 rounded-lg mr-3 group-hover:bg-blue-500 transition-colors text-blue-400 group-hover:text-white">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            </div>
            <div>
                <p class="font-medium text-sm">Alertes</p>
                <p class="text-xs text-slate-400">
                    @if($unreadCount > 0)
                        <span class="text-amber-400 font-semibold">{{ $unreadCount }} non lues</span>
                    @else
                        Tout lu
                    @endif
                </p>
            </div>
        </a>
        <a href="{{ route('commercial.profile') }}" class="group flex items-center p-3 rounded-lg bg-white/10 hover:bg-white/20 transition-all border border-white/5 hover:border-slate-500/30">
            <div class="p-2 bg-slate-700 rounded-lg mr-3 group-hover:bg-slate-500 transition-colors text-slate-300">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <p class="font-medium text-sm">Profil</p>
                <p class="text-xs text-slate-400">Infos & Réseaux</p>
            </div>
        </a>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const rawLeads = @json($leadsByDay);
    const rawConversions = @json($conversionsByDay);
    const labels = [];
    const leadsData = [];
    const conversionsData = [];

    for (let i = 29; i >= 0; i--) {
        const date = new Date();
        date.setDate(date.getDate() - i);
        const dateStr = date.toISOString().split('T')[0];
        labels.push(date.toLocaleDateString('fr-FR', { day: '2-digit', month: 'short' }));
        leadsData.push(rawLeads[dateStr] || 0);
        conversionsData.push(rawConversions[dateStr] || 0);
    }

    const ctx = document.getElementById('leadsChart').getContext('2d');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [
                {
                    label: 'Nouveaux Leads',
                    data: leadsData,
                    borderColor: '#f59e0b',
                    backgroundColor: (ctx) => {
                        const g = ctx.chart.ctx.createLinearGradient(0, 0, 0, 300);
                        g.addColorStop(0, 'rgba(245,158,11,0.18)');
                        g.addColorStop(1, 'rgba(245,158,11,0)');
                        return g;
                    },
                    borderWidth: 2.5,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#f59e0b',
                    pointBorderWidth: 2,
                    pointRadius: 3,
                    pointHoverRadius: 5,
                    fill: true,
                    tension: 0.4,
                },
                {
                    label: 'Conversions',
                    data: conversionsData,
                    borderColor: '#10b981',
                    backgroundColor: (ctx) => {
                        const g = ctx.chart.ctx.createLinearGradient(0, 0, 0, 300);
                        g.addColorStop(0, 'rgba(16,185,129,0.15)');
                        g.addColorStop(1, 'rgba(16,185,129,0)');
                        return g;
                    },
                    borderWidth: 2.5,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#10b981',
                    pointBorderWidth: 2,
                    pointRadius: 3,
                    pointHoverRadius: 5,
                    fill: true,
                    tension: 0.4,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0f172a',
                    titleFont: { size: 12 },
                    bodyFont: { size: 12 },
                    padding: 10,
                    cornerRadius: 8,
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f1f5f9', borderDash: [4, 4] },
                    ticks: { stepSize: 1, font: { size: 11 }, color: '#94a3b8' },
                    border: { display: false },
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 11 }, color: '#94a3b8', maxTicksLimit: 10 },
                    border: { display: false },
                },
            },
            interaction: { intersect: false, mode: 'index' },
        },
    });
});
</script>
@endpush
