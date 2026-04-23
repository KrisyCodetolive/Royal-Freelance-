@extends('commercial.layouts.app')

@section('title', 'Mes Leads - Kanban')
@section('header', 'Pipeline Leads')

@section('content')
    {{-- Header avec Toggle View --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-serif font-bold text-slate-900">Pipeline des Leads</h2>
            <p class="text-sm text-slate-500 mt-1">Glissez-déposez pour changer le statut</p>
        </div>
        <div class="flex items-center gap-3">
            {{-- Toggle View --}}
            <div class="inline-flex rounded-lg border border-slate-200 p-1 bg-white">
                <a href="{{ route('commercial.leads') }}" 
                   class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-900 rounded-md transition-colors">
                    <svg class="h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    Liste
                </a>
                <button class="px-4 py-2 text-sm font-medium text-white bg-amber-500 rounded-md shadow-sm">
                    <svg class="h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 0v10" />
                    </svg>
                    Kanban
                </button>
            </div>
        </div>
    </div>

    {{-- Kanban Board - 5 colonnes --}}
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4 overflow-x-auto">
        {{-- Colonne FROID --}}
        <div class="flex flex-col min-w-[240px]">
            <div class="bg-blue-50 rounded-t-xl p-4 border-b-4 border-blue-400">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-blue-700 flex items-center">
                        <span class="text-xl mr-2">❄️</span>
                        Froid
                    </h3>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-200 text-blue-700">
                        {{ $leadsByStatus['cold']->count() }}
                    </span>
                </div>
            </div>
            <div class="flex-1 bg-blue-50/30 rounded-b-xl p-3 min-h-[500px] space-y-3" 
                 data-status="cold"
                 data-sortable-group="leads">
                @foreach($leadsByStatus['cold'] as $lead)
                    @include('commercial.leads.partials.kanban-card', ['lead' => $lead])
                @endforeach
            </div>
        </div>

        {{-- Colonne TIÈDE --}}
        <div class="flex flex-col min-w-[240px]">
            <div class="bg-amber-50 rounded-t-xl p-4 border-b-4 border-amber-400">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-amber-700 flex items-center">
                        <span class="text-xl mr-2">☀️</span>
                        Tiède
                    </h3>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-200 text-amber-700">
                        {{ $leadsByStatus['warm']->count() }}
                    </span>
                </div>
            </div>
            <div class="flex-1 bg-amber-50/30 rounded-b-xl p-3 min-h-[500px] space-y-3" 
                 data-status="warm"
                 data-sortable-group="leads">
                @foreach($leadsByStatus['warm'] as $lead)
                    @include('commercial.leads.partials.kanban-card', ['lead' => $lead])
                @endforeach
            </div>
        </div>

        {{-- Colonne CHAUD --}}
        <div class="flex flex-col min-w-[240px]">
            <div class="bg-rose-50 rounded-t-xl p-4 border-b-4 border-rose-400">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-rose-700 flex items-center">
                        <span class="text-xl mr-2">🔥</span>
                        Chaud
                    </h3>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-200 text-rose-700">
                        {{ $leadsByStatus['hot']->count() }}
                    </span>
                </div>
            </div>
            <div class="flex-1 bg-rose-50/30 rounded-b-xl p-3 min-h-[500px] space-y-3" 
                 data-status="hot"
                 data-sortable-group="leads">
                @foreach($leadsByStatus['hot'] as $lead)
                    @include('commercial.leads.partials.kanban-card', ['lead' => $lead])
                @endforeach
            </div>
        </div>

        {{-- Colonne ULTRA CHAUD --}}
        <div class="flex flex-col min-w-[240px]">
            <div class="bg-purple-50 rounded-t-xl p-4 border-b-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-purple-700 flex items-center">
                        <span class="text-xl mr-2">🟣</span>
                        Ultra Chaud
                    </h3>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-200 text-purple-700">
                        {{ $leadsByStatus['ultra_hot']->count() }}
                    </span>
                </div>
            </div>
            <div class="flex-1 bg-purple-50/30 rounded-b-xl p-3 min-h-[500px] space-y-3" 
                 data-status="ultra_hot"
                 data-sortable-group="leads">
                @foreach($leadsByStatus['ultra_hot'] as $lead)
                    @include('commercial.leads.partials.kanban-card', ['lead' => $lead])
                @endforeach
            </div>
        </div>

        {{-- Colonne CLIENT --}}
        <div class="flex flex-col min-w-[240px]">
            <div class="bg-emerald-50 rounded-t-xl p-4 border-b-4 border-emerald-400">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-emerald-700 flex items-center">
                        <span class="text-xl mr-2">✅</span>
                        Client
                    </h3>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-200 text-emerald-700">
                        {{ $leadsByStatus['client']->count() }}
                    </span>
                </div>
            </div>
            <div class="flex-1 bg-emerald-50/30 rounded-b-xl p-3 min-h-[500px] space-y-3" 
                 data-status="client"
                 data-sortable-group="leads">
                @foreach($leadsByStatus['client'] as $lead)
                    @include('commercial.leads.partials.kanban-card', ['lead' => $lead])
                @endforeach
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const columns = document.querySelectorAll('[data-sortable-group="leads"]');
    
    columns.forEach(column => {
        new Sortable(column, {
            group: 'leads',
            animation: 150,
            ghostClass: 'opacity-30',
            dragClass: 'shadow-2xl',
            handle: '.kanban-card',
            onEnd: function(evt) {
                const leadId = evt.item.dataset.leadId;
                const newStatus = evt.to.dataset.status;
                
                // Animation de transition
                evt.item.classList.add('ring-2', 'ring-amber-400', 'ring-opacity-50');
                setTimeout(() => evt.item.classList.remove('ring-2', 'ring-amber-400', 'ring-opacity-50'), 1500);
                
                // Mettre à jour les compteurs visuels
                updateColumnCounts();
                
                // Update lead status via AJAX
                updateLeadStatus(leadId, newStatus);
            }
        });
    });
});

function updateColumnCounts() {
    document.querySelectorAll('[data-sortable-group="leads"]').forEach(col => {
        const count = col.querySelectorAll('.kanban-card').length;
        const header = col.previousElementSibling;
        if (header) {
            const badge = header.querySelector('span:last-child');
            if (badge) badge.textContent = count;
        }
    });
}

function updateLeadStatus(leadId, newStatus) {
    fetch('/commercial/leads/update-status', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            lead_id: leadId,
            status: newStatus
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Statut mis à jour → ' + getStatusLabel(newStatus), 'success');
        } else {
            showToast('Erreur lors de la mise à jour', 'error');
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Erreur réseau', 'error');
        location.reload();
    });
}

function getStatusLabel(status) {
    const labels = {
        'cold': '❄️ Froid',
        'warm': '☀️ Tiède',
        'hot': '🔥 Chaud',
        'ultra_hot': '🟣 Ultra Chaud',
        'client': '✅ Client'
    };
    return labels[status] || status;
}

function showToast(message, type) {
    const toast = document.createElement('div');
    toast.className = `fixed bottom-4 right-4 px-6 py-3 rounded-lg shadow-lg text-white z-50 transition-all transform translate-y-0 opacity-100 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(10px)';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}
</script>
@endpush
