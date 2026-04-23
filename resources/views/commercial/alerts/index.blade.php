@extends('commercial.layouts.app')

@section('title', 'Mes Alertes')
@section('header', 'Mes Alertes')

@section('content')
    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-rose-50 rounded-xl p-5 border border-rose-100">
            <p class="text-sm font-medium text-rose-600">Leads Chauds</p>
            <p class="text-2xl font-bold text-rose-900 mt-1">{{ $summary['hot_leads'] }}</p>
        </div>
        <div class="bg-green-50 rounded-xl p-5 border border-green-100">
            <p class="text-sm font-medium text-green-600">WhatsApp</p>
            <p class="text-2xl font-bold text-green-900 mt-1">{{ $summary['whatsapp_clicks'] }}</p>
        </div>
        <div class="bg-amber-50 rounded-xl p-5 border border-amber-100">
            <p class="text-sm font-medium text-amber-600">Inactifs</p>
            <p class="text-2xl font-bold text-amber-900 mt-1">{{ $summary['inactive'] }}</p>
        </div>
        <div class="bg-blue-50 rounded-xl p-5 border border-blue-100">
            <p class="text-sm font-medium text-blue-600">Inscriptions</p>
            <p class="text-2xl font-bold text-blue-900 mt-1">{{ $summary['new_registrations'] }}</p>
        </div>
    </div>

    {{-- Filters & Actions --}}
    <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-100 mb-6">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Statut</label>
                <select name="status" class="w-full rounded-lg border-gray-200 bg-slate-50 text-sm">
                    <option value="">Toutes</option>
                    <option value="unread" {{ ($filters['status'] ?? '') === 'unread' ? 'selected' : '' }}>Non lues</option>
                    <option value="read" {{ ($filters['status'] ?? '') === 'read' ? 'selected' : '' }}>Lues</option>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-slate-900 text-white rounded-lg text-sm font-semibold hover:bg-slate-800">
                Filtrer
            </button>
            @if($alerts->total() > 0)
                <button type="button" onclick="markAllAsRead()" class="px-4 py-2 border border-slate-200 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-50">
                    Tout marquer comme lu
                </button>
            @endif
        </form>
    </div>

    {{-- Alerts List --}}
    <div class="bg-white shadow-sm border border-slate-100 rounded-xl overflow-hidden">
        @if($alerts->count() > 0)
            <ul class="divide-y divide-slate-100">
                @foreach($alerts as $alert)
                    <li class="p-6 hover:bg-slate-50/50 transition-colors {{ $alert->is_read ? 'opacity-60' : '' }}" data-alert-id="{{ $alert->id }}">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0">
                                @if($alert->type->value === 'hot_lead')
                                    <div class="h-10 w-10 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center">🔥</div>
                                @elseif($alert->type->value === 'whatsapp_click')
                                    <div class="h-10 w-10 rounded-full bg-green-100 text-green-600 flex items-center justify-center">💬</div>
                                @elseif($alert->type->value === 'new_registration')
                                    <div class="h-10 w-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">👤</div>
                                @else
                                    <div class="h-10 w-10 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center">⏰</div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-slate-900">{{ $alert->title }}</p>
                                <p class="text-sm text-slate-600 mt-1">{{ $alert->message }}</p>
                                @if($alert->lead)
                                    <p class="text-xs text-slate-500 mt-2">
                                        Lead: <span class="font-medium">{{ $alert->lead->first_name }} {{ $alert->lead->last_name }}</span>
                                        · {{ $alert->lead->email }}
                                    </p>
                                @endif
                                <p class="text-xs text-slate-400 mt-1">{{ $alert->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="flex-shrink-0 flex gap-2">
                                @if(!$alert->is_read)
                                    <button onclick="markAsRead({{ $alert->id }})" class="p-2 text-slate-400 hover:text-amber-600" title="Marquer lu">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </button>
                                @endif
                                @if($alert->lead)
                                    <a href="{{ route('commercial.leads') }}" class="p-2 text-slate-400 hover:text-blue-600" title="Voir lead">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $alerts->withQueryString()->links() }}
            </div>
        @else
            <div class="text-center py-16">
                <div class="mx-auto h-16 w-16 rounded-full bg-slate-100 flex items-center justify-center mb-4">
                    <svg class="h-8 w-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-slate-900">Aucune alerte</h3>
                <p class="mt-2 text-sm text-slate-500">Vous êtes à jour !</p>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
function markAsRead(alertId) {
    fetch(`/commercial/alerts/${alertId}/read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const el = document.querySelector(`[data-alert-id="${alertId}"]`);
            if (el) el.style.opacity = '0.6';
            location.reload();
        }
    });
}

function markAllAsRead() {
    if (!confirm('Marquer toutes les alertes comme lues ?')) return;
    
    fetch('/commercial/alerts/mark-all-read', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) location.reload();
    });
}
</script>
@endpush
