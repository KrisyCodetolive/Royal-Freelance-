{{-- Widget Alertes pour Dashboard Commercial --}}
<div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="p-6 border-b border-slate-50 flex justify-between items-center">
        <div class="flex items-center">
            <div class="flex items-center justify-center h-8 w-8 rounded-lg bg-rose-50 text-rose-600 mr-3">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
            </div>
            <h3 class="text-lg font-serif font-semibold text-slate-900">
                Alertes
                @if($unreadCount > 0)
                    <span class="ml-2 inline-flex items-center rounded-full bg-rose-100 px-2.5 py-0.5 text-xs font-semibold text-rose-800">
                        {{ $unreadCount }}
                    </span>
                @endif
            </h3>
        </div>
        <a href="{{ route('commercial.alerts') }}" class="text-xs font-medium text-amber-600 hover:text-amber-700">Tout voir</a>
    </div>
    
    @if($alerts->count() > 0)
        <ul class="divide-y divide-slate-50" id="alerts-widget-list">
            @foreach($alerts as $alert)
                <li class="px-6 py-4 hover:bg-slate-50/50 transition-colors {{ $alert->is_read ? 'opacity-60' : '' }}" 
                    data-alert-id="{{ $alert->id }}">
                    <div class="flex items-start space-x-3">
                        {{-- Icône selon type --}}
                        <div class="flex-shrink-0 mt-0.5">
                            @if($alert->type->value === 'hot_lead')
                                <div class="flex items-center justify-center h-8 w-8 rounded-full bg-rose-50 text-rose-600">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z" />
                                    </svg>
                                </div>
                            @elseif($alert->type->value === 'whatsapp_click')
                                <div class="flex items-center justify-center h-8 w-8 rounded-full bg-green-50 text-green-600">
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                    </svg>
                                </div>
                            @elseif($alert->type->value === 'new_registration')
                                <div class="flex items-center justify-center h-8 w-8 rounded-full bg-blue-50 text-blue-600">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                    </svg>
                                </div>
                            @elseif(in_array($alert->type->value, ['inactive_7_days', 'inactive_14_days']))
                                <div class="flex items-center justify-center h-8 w-8 rounded-full bg-amber-50 text-amber-600">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            @else
                                <div class="flex items-center justify-center h-8 w-8 rounded-full bg-slate-100 text-slate-600">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            @endif
                        </div>

                        {{-- Contenu --}}
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-slate-900">
                                {{ $alert->title }}
                            </p>
                            <p class="text-xs text-slate-500 mt-0.5">
                                {{ $alert->data['lead_name'] ?? 'Lead' }} 
                                @if($alert->lead)
                                    <span class="text-slate-400">·</span>
                                    <span class="font-medium">{{ $alert->lead->email }}</span>
                                @endif
                            </p>
                            <p class="text-xs text-slate-400 mt-1">
                                {{ $alert->created_at->diffForHumans() }}
                            </p>
                        </div>

                        {{-- Actions --}}
                        <div class="flex-shrink-0 flex items-center space-x-2">
                            @if(!$alert->is_read)
                                <button 
                                    onclick="markAlertAsRead({{ $alert->id }})"
                                    class="text-slate-400 hover:text-amber-600 transition-colors"
                                    title="Marquer comme lu">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </button>
                            @endif
                            @if($alert->lead)
                                <a href="{{ route('commercial.leads') }}?lead_id={{ $alert->lead_id }}" 
                                   class="text-slate-400 hover:text-blue-600 transition-colors"
                                   title="Voir le lead">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                </a>
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
            </div>
            <h3 class="text-sm font-medium text-slate-900">Aucune alerte</h3>
            <p class="mt-1 text-xs text-slate-500">Vous êtes à jour !</p>
        </div>
    @endif
</div>

@push('scripts')
<script>
function markAlertAsRead(alertId) {
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
            // Fade out alert
            const alertElement = document.querySelector(`[data-alert-id="${alertId}"]`);
            if (alertElement) {
                alertElement.style.opacity = '0.6';
                alertElement.querySelector('button[onclick]')?.remove();
            }
            
            // Update badge count
            updateAlertBadge();
        }
    })
    .catch(error => console.error('Error:', error));
}

function updateAlertBadge() {
    fetch('/commercial/alerts/unread-count')
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById('alerts-badge');
            if (badge) {
                if (data.count > 0) {
                    badge.textContent = data.count;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            }
        });
}
</script>
@endpush
