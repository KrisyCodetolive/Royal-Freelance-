@extends('commercial.layouts.app')

@section('title', 'Tableau de Bord')
@section('header', 'Vue d\'ensemble')

@section('content')
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        <!-- Total Leads -->
        <div class="bg-white overflow-hidden rounded-xl shadow-sm border border-slate-100 transition-all duration-200 hover:shadow-md">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-blue-50 text-blue-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-slate-500 truncate">Total Leads</dt>
                            <dd>
                                <div class="text-2xl font-bold text-slate-900">{{ $stats['total_leads'] ?? 0 }}</div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-slate-50 px-5 py-3 border-t border-slate-100">
                <div class="text-xs font-medium text-blue-600 truncate hover:text-blue-500">
                    <a href="{{ route('commercial.leads') }}">Voir tous les leads &rarr;</a>
                </div>
            </div>
        </div>

        <!-- Hot Leads -->
        <div class="bg-white overflow-hidden rounded-xl shadow-sm border border-slate-100 transition-all duration-200 hover:shadow-md">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                         <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-rose-50 text-rose-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-slate-500 truncate">Leads Chauds</dt>
                            <dd>
                                <div class="text-2xl font-bold text-slate-900">{{ $stats['hot_leads'] ?? 0 }}</div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
             <div class="bg-slate-50 px-5 py-3 border-t border-slate-100">
                <div class="text-xs font-medium text-rose-600 truncate hover:text-rose-500">
                    Potentiel élevé
                </div>
            </div>
        </div>

        <!-- Conversions -->
        <div class="bg-white overflow-hidden rounded-xl shadow-sm border border-slate-100 transition-all duration-200 hover:shadow-md">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                         <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-emerald-50 text-emerald-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-slate-500 truncate">Clients Convertis</dt>
                            <dd>
                                <div class="text-2xl font-bold text-slate-900">{{ $stats['conversions'] ?? 0 }}</div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
             <div class="bg-slate-50 px-5 py-3 border-t border-slate-100">
                <div class="text-xs font-medium text-emerald-600 truncate">
                    Bravo !
                </div>
            </div>
        </div>

        <!-- Active Funnels -->
        <div class="bg-white overflow-hidden rounded-xl shadow-sm border border-slate-100 transition-all duration-200 hover:shadow-md">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-amber-50 text-amber-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-slate-500 truncate">Tunnels Actifs</dt>
                            <dd>
                                <div class="text-2xl font-bold text-slate-900">{{ $stats['active_funnels'] ?? 0 }}</div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
             <div class="bg-slate-50 px-5 py-3 border-t border-slate-100">
                <div class="text-xs font-medium text-amber-600 truncate hover:text-amber-500">
                     <a href="{{ route('commercial.funnels') }}">Gérer mes liens &rarr;</a>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Chart Section -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-slate-100 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-serif font-semibold text-slate-900">Performance (30 jours)</h3>
            </div>
             <div class="h-80 relative w-full">
                <canvas id="leadsChart"></canvas>
            </div>
        </div>

        <!-- Recent Leads & Actions -->
        <div class="lg:col-span-1 space-y-8">
            <!-- Alerts Widget -->
            @include('commercial.widgets.alerts', ['alerts' => $alerts, 'unreadCount' => $unreadCount])

            <!-- Recent Leads -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-6 border-b border-slate-50 flex justify-between items-center">
                    <h3 class="text-lg font-serif font-semibold text-slate-900">Derniers Prospects</h3>
                    <a href="{{ route('commercial.leads') }}" class="text-xs font-medium text-amber-600 hover:text-amber-700">Tout voir</a>
                </div>
                
                @if($recentLeads->count() > 0)
                    <ul class="divide-y divide-slate-50">
                        @foreach($recentLeads as $lead)
                            <li class="px-6 py-4 hover:bg-slate-50/50 transition-colors">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-slate-100 to-slate-200 border border-white shadow-sm">
                                            <span class="text-xs font-bold text-slate-600">{{ substr($lead->name ?? $lead->email, 0, 2) }}</span>
                                        </span>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-medium text-slate-900">{{ $lead->name ?? 'Prospect Sans Nom' }}</p>
                                        <p class="truncate text-xs text-slate-500">{{ $lead->email }}</p>
                                    </div>
                                    <div>
                                        @if($lead->score >= 60)
                                            <span class="inline-flex items-center rounded-full bg-rose-50 px-2 py-1 text-xs font-medium text-rose-700 ring-1 ring-inset ring-rose-600/10">🔥 Chaud</span>
                                        @elseif($lead->score >= 30)
                                            <span class="inline-flex items-center rounded-full bg-amber-50 px-2 py-1 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-600/10">☀️ Tiède</span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-1 text-xs font-medium text-slate-600 ring-1 ring-inset ring-slate-500/10">❄️ Froid</span>
                                        @endif
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="p-8 text-center bg-slate-50/50">
                        <div class="mx-auto h-12 w-12 rounded-full bg-slate-100 flex items-center justify-center mb-3">
                             <svg class="h-6 w-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                        </div>
                        <h3 class="text-sm font-medium text-slate-900">Aucun lead</h3>
                        <p class="mt-1 text-xs text-slate-500">Commencez à partager vos tunnels !</p>
                    </div>
                @endif
            </div>

            <!-- Quick Actions -->
            <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-xl shadow-lg text-white p-6 relative overflow-hidden">
                <!-- Background Pattern -->
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 rounded-full bg-white/5 blur-xl"></div>
                
                <h3 class="text-lg font-serif font-semibold mb-4 relative z-10">Actions Rapides</h3>
                <div class="grid grid-cols-1 gap-3 relative z-10">
                    <a href="{{ route('commercial.funnels') }}" class="group flex items-center p-3 rounded-lg bg-white/10 hover:bg-white/20 transition-all border border-white/5 hover:border-amber-500/30">
                        <div class="p-2 bg-amber-500/20 rounded-lg mr-3 group-hover:bg-amber-500 group-hover:text-white transition-colors text-amber-400">
                             <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-sm">Obtenir mon lien</p>
                            <p class="text-xs text-slate-400 group-hover:text-slate-200">Partager un tunnel</p>
                        </div>
                    </a>

                    <a href="{{ route('commercial.profile') }}" class="group flex items-center p-3 rounded-lg bg-white/10 hover:bg-white/20 transition-all border border-white/5 hover:border-amber-500/30">
                        <div class="p-2 bg-slate-700 rounded-lg mr-3 group-hover:bg-amber-500 group-hover:text-white transition-colors text-slate-300">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-sm">Configurer mon profil</p>
                            <p class="text-xs text-slate-400 group-hover:text-slate-200">Infos & Réseaux</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('leadsChart').getContext('2d');
        
        // Préparer les données
        const rawData = @json($leadsByDay);
        const labels = [];
        const data = [];
        
        // Générer les 30 derniers jours
        for (let i = 29; i >= 0; i--) {
            const date = new Date();
            date.setDate(date.getDate() - i);
            const dateStr = date.toISOString().split('T')[0];
            labels.push(date.toLocaleDateString('fr-FR', { day: '2-digit', month: 'short' }));
            data.push(rawData[dateStr] || 0);
        }
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Nouveaux Leads',
                    data: data,
                    // Gradient Amber/Orange
                    borderColor: '#f59e0b', // Amber 500
                    backgroundColor: (context) => {
                        const bg = context.chart.ctx.createLinearGradient(0, 0, 0, 400);
                        bg.addColorStop(0, 'rgba(245, 158, 11, 0.2)');
                        bg.addColorStop(1, 'rgba(245, 158, 11, 0)');
                        return bg;
                    },
                    borderWidth: 3,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#f59e0b',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false,
                    },
                    tooltip: {
                         backgroundColor: '#0f172a',
                         titleFont: { family: 'Outfit', size: 13 },
                         bodyFont: { family: 'Outfit', size: 13 },
                         padding: 10,
                         cornerRadius: 8,
                         displayColors: false,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f1f5f9',
                            borderDash: [5, 5]
                        },
                        ticks: {
                            stepSize: 1,
                            font: { family: 'Outfit', size: 11 },
                            color: '#64748b'
                        },
                        border: { display: false }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { family: 'Outfit', size: 11 },
                            color: '#64748b',
                            maxTicksLimit: 10
                        },
                        border: { display: false }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
            }
        });
    });
</script>
@endpush
