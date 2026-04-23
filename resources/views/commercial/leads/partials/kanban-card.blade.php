{{-- Kanban Card Component --}}
<div class="kanban-card bg-white rounded-lg shadow-sm border border-slate-200 p-4 cursor-move hover:shadow-md transition-shadow"
     data-lead-id="{{ $lead->id }}">
    {{-- Header with Avatar and Score --}}
    <div class="flex items-start justify-between mb-3">
        <div class="flex items-center gap-3">
            <div class="shrink-0">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-slate-100 to-slate-200 border border-white shadow-sm">
                    <span class="text-xs font-bold text-slate-600">{{ strtoupper(substr($lead->first_name ?? $lead->email, 0, 2)) }}</span>
                </span>
            </div>
            <div class="min-w-0">
                <p class="text-sm font-semibold text-slate-900 truncate">
                    {{ $lead->first_name }} {{ $lead->last_name }}
                </p>
                <p class="text-xs text-slate-500 truncate">{{ $lead->email }}</p>
            </div>
        </div>
        <div class="shrink-0">
            @if($lead->score >= 60)
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-rose-100 text-rose-700">
                    🔥 {{ $lead->score }}
                </span>
            @elseif($lead->score >= 30)
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                    ☀️ {{ $lead->score }}
                </span>
            @else
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">
                    ❄️ {{ $lead->score }}
                </span>
            @endif
        </div>
    </div>

    {{-- Info --}}
    @if($lead->phone)
        <div class="flex items-center text-xs text-slate-500 mb-2">
            <svg class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
            </svg>
            {{ $lead->phone }}
        </div>
    @endif

    @if($lead->funnel)
        <div class="flex items-center text-xs text-slate-500 mb-3">
            <svg class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
            </svg>
            {{ Str::limit($lead->funnel->name, 20) }}
        </div>
    @endif

    {{-- Tags --}}
    @if($lead->tags->count() > 0)
        <div class="flex flex-wrap gap-1 mb-3">
            @foreach($lead->tags->take(2) as $tag)
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-{{ $tag->color }}-100 text-{{ $tag->color }}-700">
                    {{ $tag->name }}
                </span>
            @endforeach
            @if($lead->tags->count() > 2)
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-600">
                    +{{ $lead->tags->count() - 2 }}
                </span>
            @endif
        </div>
    @endif

    {{-- Footer Actions --}}
    <div class="flex items-center justify-between pt-3 border-t border-slate-100">
        <span class="text-xs text-slate-400">
            {{ $lead->created_at->diffForHumans() }}
        </span>
        <div class="flex items-center gap-2">
            @if($lead->phone)
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $lead->phone) }}" 
                   target="_blank"
                   class="p-1.5 text-green-600 hover:bg-green-50 rounded transition-colors"
                   title="WhatsApp"
                   onclick="event.stopPropagation();">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                </a>
            @endif
            <a href="mailto:{{ $lead->email }}" 
               class="p-1.5 text-slate-600 hover:bg-slate-50 rounded transition-colors"
               title="Email"
               onclick="event.stopPropagation();">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </a>
        </div>
    </div>
</div>
