@extends('commercial.layouts.app')

@section('title', 'Mes Leads - Kanban')
@section('header', 'Pipeline Leads')

@section('content')
    {{-- Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center gap-3 sm:justify-between">
        <div>
            <h2 class="text-xl sm:text-2xl font-serif font-bold text-slate-900">Pipeline des Leads</h2>
            <p class="text-sm text-slate-500 mt-0.5">Glissez-déposez pour changer le statut</p>
        </div>
        <div class="flex items-center gap-2 flex-shrink-0">
            <div class="inline-flex rounded-lg border border-slate-200 p-1 bg-white shadow-sm">
                <a href="{{ route('commercial.leads') }}"
                   class="flex items-center px-3 py-1.5 text-sm font-medium text-slate-600 hover:text-slate-900 rounded-md transition-colors">
                    <svg class="h-4 w-4 sm:mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <span class="hidden sm:inline">Liste</span>
                </a>
                <button class="flex items-center px-3 py-1.5 text-sm font-medium text-white bg-amber-500 rounded-md shadow-sm">
                    <svg class="h-4 w-4 sm:mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 0v10" />
                    </svg>
                    <span class="hidden sm:inline">Kanban</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Indicateur de scroll mobile --}}
    <p class="text-xs text-slate-400 mb-3 flex items-center gap-1 sm:hidden">
        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h8M8 12h8M8 17h4" />
        </svg>
        Faites glisser horizontalement pour voir toutes les colonnes
    </p>

    {{-- Kanban Board — scroll horizontal sur mobile, grille sur desktop --}}
    <div class="overflow-x-auto pb-4 -mx-4 px-4 sm:mx-0 sm:px-0">
        <div class="flex gap-4 sm:grid sm:grid-cols-3 lg:grid-cols-5"
             style="min-width: max-content;"
             id="kanban-board">

            {{-- Colonne FROID --}}
            <div class="flex flex-col w-64 sm:w-auto flex-shrink-0">
                <div class="bg-blue-50 rounded-t-xl px-4 py-3 border-b-4 border-blue-400">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-blue-700 flex items-center text-sm">
                            <span class="text-lg mr-1.5">❄️</span> Froid
                        </h3>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-blue-200 text-blue-700">
                            {{ $leadsByStatus['cold']->count() }}
                        </span>
                    </div>
                </div>
                <div class="flex-1 bg-blue-50/30 rounded-b-xl p-2.5 min-h-[400px] space-y-2.5"
                     data-status="cold" data-sortable-group="leads">
                    @foreach($leadsByStatus['cold'] as $lead)
                        @include('commercial.leads.partials.kanban-card', ['lead' => $lead])
                    @endforeach
                </div>
            </div>

            {{-- Colonne TIÈDE --}}
            <div class="flex flex-col w-64 sm:w-auto flex-shrink-0">
                <div class="bg-amber-50 rounded-t-xl px-4 py-3 border-b-4 border-amber-400">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-amber-700 flex items-center text-sm">
                            <span class="text-lg mr-1.5">☀️</span> Tiède
                        </h3>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-amber-200 text-amber-700">
                            {{ $leadsByStatus['warm']->count() }}
                        </span>
                    </div>
                </div>
                <div class="flex-1 bg-amber-50/30 rounded-b-xl p-2.5 min-h-[400px] space-y-2.5"
                     data-status="warm" data-sortable-group="leads">
                    @foreach($leadsByStatus['warm'] as $lead)
                        @include('commercial.leads.partials.kanban-card', ['lead' => $lead])
                    @endforeach
                </div>
            </div>

            {{-- Colonne CHAUD --}}
            <div class="flex flex-col w-64 sm:w-auto flex-shrink-0">
                <div class="bg-rose-50 rounded-t-xl px-4 py-3 border-b-4 border-rose-400">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-rose-700 flex items-center text-sm">
                            <span class="text-lg mr-1.5">🔥</span> Chaud
                        </h3>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-rose-200 text-rose-700">
                            {{ $leadsByStatus['hot']->count() }}
                        </span>
                    </div>
                </div>
                <div class="flex-1 bg-rose-50/30 rounded-b-xl p-2.5 min-h-[400px] space-y-2.5"
                     data-status="hot" data-sortable-group="leads">
                    @foreach($leadsByStatus['hot'] as $lead)
                        @include('commercial.leads.partials.kanban-card', ['lead' => $lead])
                    @endforeach
                </div>
            </div>

            {{-- Colonne ULTRA CHAUD --}}
            <div class="flex flex-col w-64 sm:w-auto flex-shrink-0">
                <div class="bg-purple-50 rounded-t-xl px-4 py-3 border-b-4 border-purple-500">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-purple-700 flex items-center text-sm">
                            <span class="text-lg mr-1.5">🟣</span> Ultra Chaud
                        </h3>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-purple-200 text-purple-700">
                            {{ $leadsByStatus['ultra_hot']->count() }}
                        </span>
                    </div>
                </div>
                <div class="flex-1 bg-purple-50/30 rounded-b-xl p-2.5 min-h-[400px] space-y-2.5"
                     data-status="ultra_hot" data-sortable-group="leads">
                    @foreach($leadsByStatus['ultra_hot'] as $lead)
                        @include('commercial.leads.partials.kanban-card', ['lead' => $lead])
                    @endforeach
                </div>
            </div>

            {{-- Colonne CLIENT --}}
            <div class="flex flex-col w-64 sm:w-auto flex-shrink-0">
                <div class="bg-emerald-50 rounded-t-xl px-4 py-3 border-b-4 border-emerald-400">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-emerald-700 flex items-center text-sm">
                            <span class="text-lg mr-1.5">✅</span> Client
                        </h3>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-200 text-emerald-700">
                            {{ $leadsByStatus['client']->count() }}
                        </span>
                    </div>
                </div>
                <div class="flex-1 bg-emerald-50/30 rounded-b-xl p-2.5 min-h-[400px] space-y-2.5"
                     data-status="client" data-sortable-group="leads">
                    @foreach($leadsByStatus['client'] as $lead)
                        @include('commercial.leads.partials.kanban-card', ['lead' => $lead])
                    @endforeach
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-sortable-group="leads"]').forEach(column => {
        new Sortable(column, {
            group: 'leads',
            animation: 150,
            ghostClass: 'opacity-30',
            dragClass: 'shadow-2xl',
            handle: '.kanban-card',
            onEnd(evt) {
                const leadId  = evt.item.dataset.leadId;
                const newStatus = evt.to.dataset.status;
                evt.item.classList.add('ring-2', 'ring-amber-400');
                setTimeout(() => evt.item.classList.remove('ring-2', 'ring-amber-400'), 1500);
                updateColumnCounts();
                updateLeadStatus(leadId, newStatus);
            }
        });
    });
});

function updateColumnCounts() {
    document.querySelectorAll('[data-sortable-group="leads"]').forEach(col => {
        const count  = col.querySelectorAll('.kanban-card').length;
        const badge  = col.previousElementSibling?.querySelector('span:last-child');
        if (badge) badge.textContent = count;
    });
}

function updateLeadStatus(leadId, newStatus) {
    fetch('/commercial/leads/update-status', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ lead_id: leadId, status: newStatus })
    })
    .then(r => r.json())
    .then(data => {
        showToast(data.success ? '✓ ' + getStatusLabel(newStatus) : 'Erreur', data.success ? 'success' : 'error');
        if (!data.success) location.reload();
    })
    .catch(() => { showToast('Erreur réseau', 'error'); location.reload(); });
}

const STATUS_LABELS = { cold:'❄️ Froid', warm:'☀️ Tiède', hot:'🔥 Chaud', ultra_hot:'🟣 Ultra Chaud', client:'✅ Client' };
function getStatusLabel(s) { return STATUS_LABELS[s] || s; }

function showToast(msg, type) {
    const t = document.createElement('div');
    t.className = `fixed bottom-4 right-4 px-5 py-2.5 rounded-lg shadow-lg text-white text-sm font-medium z-50 transition-all ${type === 'success' ? 'bg-emerald-500' : 'bg-red-500'}`;
    t.textContent = msg;
    document.body.appendChild(t);
    setTimeout(() => { t.style.opacity = '0'; setTimeout(() => t.remove(), 300); }, 3000);
}
</script>
@endpush
