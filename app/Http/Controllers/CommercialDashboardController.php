<?php

namespace App\Http\Controllers;

use App\Models\Funnel;
use App\Models\Lead;
use App\Services\CommercialService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommercialDashboardController extends Controller
{
    public function __construct(
        protected CommercialService $commercialService
    ) {
    }

    /**
     * Dashboard principal du commercial
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $stats = $this->commercialService->getStats($user);

        // ── Filtres graphique ──────────────────────────────────────────────
        $period    = $request->input('period', '30d');
        $funnelId  = $request->input('funnel_id');
        $dateFrom  = $request->input('date_from');
        $dateTo    = $request->input('date_to');

        [$startDate, $endDate] = $this->resolveDateRange($period, $dateFrom, $dateTo);

        // Durée en jours pour construire les labels du graphique
        $days = (int) $startDate->diffInDays($endDate) + 1;

        // ── Tendances (toujours sur 30j, indépendantes du filtre) ──────────
        $currentLeads = Lead::withoutGlobalScope('tenant')
            ->where('brought_by', $user->id)
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        $prevLeads = Lead::withoutGlobalScope('tenant')
            ->where('brought_by', $user->id)
            ->whereBetween('created_at', [now()->subDays(60), now()->subDays(30)])
            ->count();

        $currentConversions = Lead::withoutGlobalScope('tenant')
            ->where('brought_by', $user->id)
            ->whereNotNull('converted_at')
            ->where('converted_at', '>=', now()->subDays(30))
            ->count();

        $prevConversions = Lead::withoutGlobalScope('tenant')
            ->where('brought_by', $user->id)
            ->whereNotNull('converted_at')
            ->whereBetween('converted_at', [now()->subDays(60), now()->subDays(30)])
            ->count();

        $leadsTrend = $prevLeads > 0 ? round(($currentLeads - $prevLeads) / $prevLeads * 100) : null;
        $conversionsTrend = $prevConversions > 0 ? round(($currentConversions - $prevConversions) / $prevConversions * 100) : null;

        // ── Stats globales ─────────────────────────────────────────────────
        $totalLeads = $stats['total_leads'];
        $conversionRate = $totalLeads > 0 ? round($stats['conversions'] / $totalLeads * 100, 1) : 0;

        // ── Pipeline par statut ────────────────────────────────────────────
        $leadsByStatus = Lead::withoutGlobalScope('tenant')
            ->where('brought_by', $user->id)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // ── Leads à relancer ───────────────────────────────────────────────
        $leadsToFollowUp = Lead::withoutGlobalScope('tenant')
            ->where('brought_by', $user->id)
            ->whereIn('status', [
                \App\Enums\LeadStatus::WARM,
                \App\Enums\LeadStatus::HOT,
                \App\Enums\LeadStatus::ULTRA_HOT,
            ])
            ->where(function ($q) {
                $q->where('last_activity_at', '<=', now()->subDays(3))
                  ->orWhereNull('last_activity_at');
            })
            ->with(['funnel:id,name'])
            ->orderByDesc('score')
            ->limit(6)
            ->get();

        // ── Données graphique (avec filtres période + tunnel) ──────────────
        $leadsQuery = Lead::withoutGlobalScope('tenant')
            ->where('brought_by', $user->id)
            ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->copy()->endOfDay()]);

        $conversionsQuery = Lead::withoutGlobalScope('tenant')
            ->where('brought_by', $user->id)
            ->whereNotNull('converted_at')
            ->whereBetween('converted_at', [$startDate->startOfDay(), $endDate->copy()->endOfDay()]);

        if ($funnelId) {
            $leadsQuery->where('funnel_id', $funnelId);
            $conversionsQuery->where('funnel_id', $funnelId);
        }

        $leadsByDayRaw = $leadsQuery
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')->orderBy('date')
            ->pluck('count', 'date')->toArray();

        $conversionsByDayRaw = $conversionsQuery
            ->selectRaw('DATE(converted_at) as date, COUNT(*) as count')
            ->groupBy('date')->orderBy('date')
            ->pluck('count', 'date')->toArray();

        // Construire les tableaux complets (une entrée par jour, même à 0)
        $chartLabels = [];
        $leadsByDay  = [];
        $conversionsByDay = [];

        for ($i = 0; $i < $days; $i++) {
            $date = $startDate->copy()->addDays($i);
            $dateStr = $date->toDateString();
            $chartLabels[]    = $date->translatedFormat('d M');
            $leadsByDay[]     = $leadsByDayRaw[$dateStr] ?? 0;
            $conversionsByDay[] = $conversionsByDayRaw[$dateStr] ?? 0;
        }

        // ── Liste des tunnels du commercial (pour le filtre) ───────────────
        $funnels = $user->availableFunnels()
            ->where('is_template', false)
            ->select('funnels.id', 'funnels.name')
            ->get();

        // ── Alertes ────────────────────────────────────────────────────────
        $alerts = \App\Models\Alert::where('user_id', $user->id)
            ->where('is_read', false)
            ->with('lead:id,first_name,last_name,email,phone,score,funnel_id')
            ->orderByDesc('priority')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $unreadAlertsCount = \App\Models\Alert::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        return view('commercial.dashboard', [
            'user'             => $user,
            'stats'            => $stats,
            'conversionRate'   => $conversionRate,
            'leadsTrend'       => $leadsTrend,
            'conversionsTrend' => $conversionsTrend,
            'leadsByStatus'    => $leadsByStatus,
            'leadsToFollowUp'  => $leadsToFollowUp,
            'chartLabels'      => $chartLabels,
            'leadsByDay'       => $leadsByDay,
            'conversionsByDay' => $conversionsByDay,
            'funnels'          => $funnels,
            'currentPeriod'    => $period,
            'currentFunnelId'  => $funnelId,
            'dateFrom'         => $dateFrom ?? $startDate->toDateString(),
            'dateTo'           => $dateTo   ?? $endDate->toDateString(),
            'recentLeads'      => Lead::withoutGlobalScope('tenant')
                ->where('brought_by', $user->id)
                ->with(['funnel:id,name'])
                ->latest()
                ->take(5)
                ->get(),
            'alerts'      => $alerts,
            'unreadCount' => $unreadAlertsCount,
        ]);
    }

    private function resolveDateRange(string $period, ?string $dateFrom, ?string $dateTo): array
    {
        return match ($period) {
            'today'  => [now()->startOfDay(), now()],
            '7d'     => [now()->subDays(6)->startOfDay(), now()],
            '90d'    => [now()->subDays(89)->startOfDay(), now()],
            'custom' => [
                \Carbon\Carbon::parse($dateFrom ?? now()->subDays(29))->startOfDay(),
                \Carbon\Carbon::parse($dateTo   ?? now())->endOfDay(),
            ],
            default  => [now()->subDays(29)->startOfDay(), now()], // 30d
        };
    }

    /**
     * Liste des tunnels disponibles pour ce commercial
     */
    public function funnels()
    {
        $user = Auth::user();

        // Tunnels via les groupes commerciaux (exclure les templates)
        $availableFunnels = $user->availableFunnels()
            ->where('is_template', false)
            ->withCount(['pages'])
            // Compter uniquement les leads apportés par ce commercial pour ce tunnel
            ->withCount([
                'leads as my_leads_count' => function ($query) use ($user) {
                    $query->where('brought_by', $user->id);
                }
            ])
            ->get();

        // Charger les pivots pour avoir les vues
        $user->usableFunnels->each(function ($funnel) use ($availableFunnels) {
            $match = $availableFunnels->firstWhere('id', $funnel->id);
            if ($match) {
                $match->my_views_count = $funnel->pivot->leads_count ?? 0; // Note: leads_count dans pivot est souvent utilisé pour les vues ou clics selon l'implémentation
                $match->is_configured = true;
            }
        });

        $activatedFunnelIds = $user->usableFunnels()
            ->pluck('funnels.id')
            ->merge($user->assignedFunnels()->active()->pluck('id'))
            ->unique()
            ->toArray();

        return view('commercial.funnels.index', [
            'user' => $user,
            'funnels' => $availableFunnels,
            'activatedFunnelIds' => $activatedFunnelIds,
        ]);
    }

    /**
     * Formulaire de configuration CTA pour un tunnel
     */
    public function configureFunnel(Funnel $funnel)
    {
        $user = Auth::user();

        // Vérifier que le commercial a accès à ce tunnel
        if (!$this->commercialService->canAccessFunnel($user, $funnel)) {
            abort(403, 'Vous n\'avez pas accès à ce tunnel.');
        }

        // Récupérer la config existante
        $pivot = $user->usableFunnels()
            ->where('funnel_id', $funnel->id)
            ->first()?->pivot;

        return view('commercial.funnels.configure', [
            'user' => $user,
            'funnel' => $funnel,
            'config' => [
                'shop_url' => $pivot?->shop_url ?? '',
                'custom_slug' => $pivot?->custom_slug ?? '',
                'whatsapp_redirect' => $pivot?->whatsapp_redirect ?? $user->whatsapp_number ?? '',
                'whatsapp_message' => $pivot?->whatsapp_message ?? '',
                'is_active' => $pivot?->is_active ?? false,
            ],
        ]);
    }

    /**
     * Sauvegarder la configuration CTA
     */
    public function saveFunnelConfig(Request $request, Funnel $funnel)
    {
        $user = Auth::user();

        if (!$this->commercialService->canAccessFunnel($user, $funnel)) {
            abort(403);
        }

        $validated = $request->validate([
            'shop_url' => 'nullable|url|max:500',
            'custom_slug' => 'nullable|string|alpha_dash|max:255', // Validation basique
            'whatsapp_redirect' => 'nullable|string|max:20',
            'whatsapp_message' => 'nullable|string|max:1000',
        ]);

        // Créer ou mettre à jour le pivot
        $user->usableFunnels()->syncWithoutDetaching([
            $funnel->id => [
                'shop_url' => $validated['shop_url'],
                'custom_slug' => $validated['custom_slug'],
                'whatsapp_redirect' => $validated['whatsapp_redirect'],
                'whatsapp_message' => $validated['whatsapp_message'],
                'is_active' => true,
            ],
        ]);

        return redirect()
            ->route('commercial.funnels')
            ->with('success', 'Configuration sauvegardée ! Votre lien : ' . $this->getCommercialFunnelUrl($user, $funnel));
    }

    /**
     * Liste des leads apportés par ce commercial
     */
    public function leads(Request $request)
    {
        $user = Auth::user();

        $query = Lead::withoutGlobalScope('tenant')
            ->where('brought_by', $user->id)
            ->with(['funnel', 'tags']);

        // Filtres
        if ($request->filled('status')) {
            match ($request->status) {
                'hot' => $query->hot(),
                'warm' => $query->warm(),
                'cold' => $query->cold(),
                'converted' => $query->converted(),
                default => null,
            };
        }

        if ($request->filled('funnel_id')) {
            $query->where('funnel_id', $request->funnel_id);
        }

        $leads = $query->latest()->paginate(20);

        return view('commercial.leads.index', [
            'user' => $user,
            'leads' => $leads,
            'funnels' => $user->availableFunnels()->active()->get(),
            'filters' => $request->only(['status', 'funnel_id']),
        ]);
    }

    /**
     * Profil du commercial
     */
    public function profile()
    {
        $user = Auth::user();

        return view('commercial.profile', [
            'user' => $user,
        ]);
    }

    /**
     * Mettre à jour le profil du commercial
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'shop_name'       => 'nullable|string|max:255',
            'bio'             => 'nullable|string|max:1000',
            'whatsapp_number' => 'nullable|string|max:20',
            'avatar'          => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $this->commercialService->updateProfile($user, $validated);

        return redirect()
            ->route('commercial.profile')
            ->with('success', 'Profil mis à jour avec succès.');
    }

    /**
     * Vue Kanban des leads
     */
    public function kanban(Request $request)
    {
        $user = Auth::user();

        // Grouper les leads par statut réel (LeadStatus enum)
        $leadsByStatus = [
            'cold' => Lead::where('brought_by', $user->id)
                ->where('status', \App\Enums\LeadStatus::COLD)
                ->with(['funnel:id,name', 'tags:id,name,color'])
                ->orderByDesc('last_activity_at')
                ->get(),

            'warm' => Lead::where('brought_by', $user->id)
                ->where('status', \App\Enums\LeadStatus::WARM)
                ->with(['funnel:id,name', 'tags:id,name,color'])
                ->orderByDesc('last_activity_at')
                ->get(),

            'hot' => Lead::where('brought_by', $user->id)
                ->where('status', \App\Enums\LeadStatus::HOT)
                ->with(['funnel:id,name', 'tags:id,name,color'])
                ->orderByDesc('last_activity_at')
                ->get(),

            'ultra_hot' => Lead::where('brought_by', $user->id)
                ->where('status', \App\Enums\LeadStatus::ULTRA_HOT)
                ->with(['funnel:id,name', 'tags:id,name,color'])
                ->orderByDesc('last_activity_at')
                ->get(),

            'client' => Lead::where('brought_by', $user->id)
                ->where('status', \App\Enums\LeadStatus::CLIENT)
                ->with(['funnel:id,name', 'tags:id,name,color'])
                ->orderByDesc('converted_at')
                ->get(),
        ];

        return view('commercial.leads.kanban', [
            'user' => $user,
            'leadsByStatus' => $leadsByStatus,
        ]);
    }

    /**
     * Mettre à jour le statut d'un lead (AJAX - Kanban drag & drop)
     */
    public function updateLeadStatus(Request $request)
    {
        $validated = $request->validate([
            'lead_id' => 'required|integer|exists:leads,id',
            'status' => 'required|string|in:cold,warm,hot,ultra_hot,client',
        ]);

        $user = Auth::user();
        $lead = Lead::findOrFail($validated['lead_id']);

        // Vérifier que le lead appartient au commercial
        if ($lead->brought_by !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Accès refusé',
            ], 403);
        }

        $newStatus = \App\Enums\LeadStatus::from($validated['status']);
        $oldStatus = $lead->status;

        // Mettre à jour le statut
        $lead->status = $newStatus;

        // Gérer la conversion
        if ($newStatus === \App\Enums\LeadStatus::CLIENT && !$lead->converted_at) {
            $lead->converted_at = now();
        } elseif ($newStatus !== \App\Enums\LeadStatus::CLIENT) {
            $lead->converted_at = null;
        }

        $lead->save();

        // Mettre à jour les tags de progression correspondants
        $this->syncProgressionTagForStatus($lead, $newStatus);

        // Événement de changement de statut
        if ($oldStatus !== $newStatus) {
            event(new \App\Events\LeadStatusChanged($lead, $oldStatus, $newStatus));
        }

        return response()->json([
            'success' => true,
            'message' => 'Statut mis à jour',
            'lead' => [
                'id' => $lead->id,
                'status' => $lead->status->value,
                'converted_at' => $lead->converted_at,
            ],
        ]);
    }

    /**
     * Synchroniser le tag de progression avec le nouveau statut (drag & drop)
     */
    protected function syncProgressionTagForStatus(Lead $lead, \App\Enums\LeadStatus $status): void
    {
        $progressionMap = [
            'cold' => ['slug' => 'froid', 'name' => 'Froid', 'color' => '#3B82F6'],
            'warm' => ['slug' => 'tiede', 'name' => 'Tiède', 'color' => '#F59E0B'],
            'hot' => ['slug' => 'chaud', 'name' => 'Chaud', 'color' => '#EF4444'],
            'ultra_hot' => ['slug' => 'ultra-chaud', 'name' => 'Ultra Chaud', 'color' => '#8B5CF6'],
            'client' => ['slug' => 'client', 'name' => 'Client', 'color' => '#10B981'],
        ];

        // Retirer tous les tags de progression
        $allSlugs = array_column($progressionMap, 'slug');
        $existingTagIds = \App\Models\Tag::where('tenant_id', $lead->tenant_id)
            ->whereIn('slug', $allSlugs)
            ->where('is_auto', true)
            ->pluck('id');

        if ($existingTagIds->isNotEmpty()) {
            $lead->tags()->detach($existingTagIds);
        }

        // Ajouter le nouveau tag
        if (isset($progressionMap[$status->value])) {
            $tagData = $progressionMap[$status->value];
            $tag = \App\Models\Tag::firstOrCreate(
                ['tenant_id' => $lead->tenant_id, 'slug' => $tagData['slug']],
                ['name' => $tagData['name'], 'color' => $tagData['color'], 'is_auto' => true]
            );
            $lead->addTag($tag);
            
            // Check sequences that trigger on TAG_ASSIGNED
            app(\App\Services\EmailService::class)->checkAndSubscribeLeadToSequences($lead, \App\Enums\EmailSequenceTrigger::TAG_ASSIGNED->value);
        }
    }

    /**
     * Générer l'URL unique du commercial pour un tunnel
     */
    protected function getCommercialFunnelUrl($user, Funnel $funnel): string
    {
        $pivot = $user->usableFunnels()->where('funnel_id', $funnel->id)->first()?->pivot;
        $slug = $pivot?->custom_slug ?: $funnel->slug;

        if ($user->subdomain) {
            $parsed     = parse_url(config('app.url'));
            $scheme     = $parsed['scheme'] ?? 'https';
            $port       = isset($parsed['port']) ? ':' . $parsed['port'] : '';
            $baseDomain = config('app.subdomain_base');

            return $scheme . '://' . $user->subdomain . '.' . $baseDomain . $port . '/f/' . $slug;
        }

        return url('/f/' . $slug . '?ref=' . $user->id);
    }
}
