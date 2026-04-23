<?php

namespace App\Services;

use App\Enums\LeadStatus;
use App\Models\Funnel;
use App\Models\Lead;
use App\Models\Tag;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LeadService
{
    protected ScoringService $scoringService;
    protected AlertService $alertService;

    public function __construct(ScoringService $scoringService, AlertService $alertService)
    {
        $this->scoringService = $scoringService;
        $this->alertService = $alertService;
    }

    /**
     * Get leads for a funnel with filters
     */
    public function getLeads(Funnel $funnel, array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Lead::where('funnel_id', $funnel->id)
            ->with(['tags', 'assignedTo', 'broughtBy']);

        // Filter by status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Filter by tags
        if (!empty($filters['tags'])) {
            $query->whereHas('tags', fn($q) => $q->whereIn('tags.id', $filters['tags']));
        }

        // Filter by score range
        if (!empty($filters['min_score'])) {
            $query->where('score', '>=', $filters['min_score']);
        }
        if (!empty($filters['max_score'])) {
            $query->where('score', '<=', $filters['max_score']);
        }

        // Filter by date range
        if (!empty($filters['from_date'])) {
            $query->where('created_at', '>=', $filters['from_date']);
        }
        if (!empty($filters['to_date'])) {
            $query->where('created_at', '<=', $filters['to_date']);
        }

        // Filter by source
        if (!empty($filters['source'])) {
            $query->where('source', $filters['source']);
        }

        // Filter by assigned user
        if (!empty($filters['assigned_to'])) {
            $query->where('assigned_to', $filters['assigned_to']);
        }

        // Filter by brought_by (commercial)
        if (!empty($filters['brought_by'])) {
            $query->where('brought_by', $filters['brought_by']);
        }

        // Search
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDir = $filters['sort_dir'] ?? 'desc';
        $query->orderBy($sortBy, $sortDir);

        return $query->paginate($filters['per_page'] ?? 25);
    }

    /**
     * Update lead status manually
     */
    public function updateStatus(Lead $lead, LeadStatus $status): Lead
    {
        $oldStatus = $lead->status;
        $lead->update(['status' => $status]);

        if ($status === LeadStatus::CLIENT && !$lead->converted_at) {
            $lead->update(['converted_at' => now()]);
        }

        // Trigger status change event
        if ($oldStatus !== $status) {
            event(new \App\Events\LeadStatusChanged($lead, $oldStatus, $status));
        }

        return $lead->fresh();
    }

    /**
     * Assign lead to a user
     */
    public function assignTo(Lead $lead, User $user): Lead
    {
        $lead->update(['assigned_to' => $user->id]);
        return $lead->fresh();
    }

    /**
     * Add tag to lead
     */
    public function addTag(Lead $lead, Tag|int $tag): Lead
    {
        $lead->addTag($tag);
        return $lead->fresh()->load('tags');
    }

    /**
     * Remove tag from lead
     */
    public function removeTag(Lead $lead, Tag|int $tag): Lead
    {
        $lead->removeTag($tag);
        return $lead->fresh()->load('tags');
    }

    /**
     * Add note to lead
     */
    public function addNote(Lead $lead, string $content, ?User $user = null, bool $isPrivate = false): Lead
    {
        $lead->addNote($content, $user?->id, $isPrivate);
        return $lead->fresh()->load('notes');
    }

    /**
     * Import leads from CSV
     */
    public function importFromCsv(Funnel $funnel, array $rows, array $mapping = []): array
    {
        $imported = 0;
        $errors = [];

        $defaultMapping = [
            'email' => 'email',
            'phone' => 'phone',
            'first_name' => 'first_name',
            'last_name' => 'last_name',
            'source' => 'source',
        ];

        $mapping = array_merge($defaultMapping, $mapping);

        foreach ($rows as $index => $row) {
            try {
                $data = [];
                foreach ($mapping as $field => $column) {
                    if (isset($row[$column])) {
                        $data[$field] = $row[$column];
                    }
                }

                // Skip if no email and no phone
                if (empty($data['email']) && empty($data['phone'])) {
                    $errors[] = "Ligne {$index}: Email ou téléphone requis";
                    continue;
                }

                // Check for duplicate
                $exists = Lead::where('funnel_id', $funnel->id)
                    ->where(function ($q) use ($data) {
                        if (!empty($data['email'])) {
                            $q->where('email', $data['email']);
                        }
                        if (!empty($data['phone'])) {
                            $q->orWhere('phone', $data['phone']);
                        }
                    })
                    ->exists();

                if ($exists) {
                    $errors[] = "Ligne {$index}: Doublon détecté";
                    continue;
                }

                Lead::create([
                    'tenant_id' => $funnel->tenant_id,
                    'funnel_id' => $funnel->id,
                    'email' => $data['email'] ?? null,
                    'phone' => $data['phone'] ?? null,
                    'first_name' => $data['first_name'] ?? null,
                    'last_name' => $data['last_name'] ?? null,
                    'source' => $data['source'] ?? 'import',
                    'status' => LeadStatus::COLD,
                ]);

                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Ligne {$index}: " . $e->getMessage();
            }
        }

        return [
            'imported' => $imported,
            'errors' => $errors,
        ];
    }

    /**
     * Export leads to array (for CSV)
     */
    public function exportToArray(Funnel $funnel, array $filters = []): array
    {
        $leads = Lead::where('funnel_id', $funnel->id)
            ->with(['tags', 'assignedTo', 'broughtBy'])
            ->get();

        return $leads->map(fn($lead) => [
            'id' => $lead->id,
            'first_name' => $lead->first_name,
            'last_name' => $lead->last_name,
            'email' => $lead->email,
            'phone' => $lead->phone,
            'source' => $lead->source,
            'status' => $lead->status->label(),
            'score' => $lead->score,
            'tags' => $lead->tags->pluck('name')->implode(', '),
            'assigned_to' => $lead->assignedTo?->name,
            'brought_by' => $lead->broughtBy?->name,
            'created_at' => $lead->created_at->format('Y-m-d H:i:s'),
            'converted_at' => $lead->converted_at?->format('Y-m-d H:i:s'),
        ])->toArray();
    }

    /**
     * Get leads to relaunch (inactive)
     */
    public function getLeadsToRelaunch(Tenant $tenant, int $inactiveDays = 7): Collection
    {
        return Lead::where('tenant_id', $tenant->id)
            ->whereNotIn('status', [LeadStatus::CLIENT, LeadStatus::MEMBER])
            ->inactive($inactiveDays)
            ->with(['funnel', 'assignedTo'])
            ->orderByDesc('score')
            ->get();
    }

    /**
     * Get pipeline summary for dashboard
     */
    public function getPipelineSummary(Tenant $tenant): array
    {
        $leads = Lead::where('tenant_id', $tenant->id);

        return [
            LeadStatus::COLD->value => (clone $leads)->where('status', LeadStatus::COLD)->count(),
            LeadStatus::WARM->value => (clone $leads)->where('status', LeadStatus::WARM)->count(),
            LeadStatus::HOT->value => (clone $leads)->where('status', LeadStatus::HOT)->count(),
            LeadStatus::ULTRA_HOT->value => (clone $leads)->where('status', LeadStatus::ULTRA_HOT)->count(),
            LeadStatus::CLIENT->value => (clone $leads)->where('status', LeadStatus::CLIENT)->count(),
            LeadStatus::MEMBER->value => (clone $leads)->where('status', LeadStatus::MEMBER)->count(),
        ];
    }

    /**
     * Get top sources for a funnel/tenant
     */
    public function getTopSources(Funnel|Tenant $entity, int $limit = 5): array
    {
        $query = Lead::query();

        if ($entity instanceof Funnel) {
            $query->where('funnel_id', $entity->id);
        } else {
            $query->where('tenant_id', $entity->id);
        }

        return $query->select('source', DB::raw('COUNT(*) as count'))
            ->whereNotNull('source')
            ->groupBy('source')
            ->orderByDesc('count')
            ->limit($limit)
            ->pluck('count', 'source')
            ->toArray();
    }

    /*
    |--------------------------------------------------------------------------
    | Segments Dynamiques
    |--------------------------------------------------------------------------
    */

    /**
     * Get available dynamic segments with counts
     */
    public function getSegments(Tenant $tenant): array
    {
        $baseQuery = Lead::where('tenant_id', $tenant->id);

        return [
            [
                'key' => 'hot_no_contact',
                'name' => '🔥 Leads chauds sans contact',
                'description' => 'Leads chauds ou ultra chauds jamais contactés via WhatsApp',
                'color' => '#EF4444',
                'icon' => 'fire',
                'count' => (clone $baseQuery)
                    ->whereIn('status', [LeadStatus::HOT, LeadStatus::ULTRA_HOT])
                    ->whereDoesntHave('events', function ($q) {
                        $q->where('type', \App\Enums\EventType::WHATSAPP_CLICK);
                    })
                    ->count(),
            ],
            [
                'key' => 'inactive_7d',
                'name' => '💤 Inactifs 7j+',
                'description' => 'Leads sans activité depuis plus de 7 jours',
                'color' => '#94A3B8',
                'icon' => 'clock',
                'count' => (clone $baseQuery)
                    ->whereNotIn('status', [LeadStatus::CLIENT, LeadStatus::MEMBER])
                    ->where(function ($q) {
                        $q->where('last_activity_at', '<', now()->subDays(7))
                            ->orWhere(function ($q2) {
                                $q2->whereNull('last_activity_at')
                                    ->where('created_at', '<', now()->subDays(7));
                            });
                    })
                    ->count(),
            ],
            [
                'key' => 'video_engaged',
                'name' => '🎬 Engagés vidéo',
                'description' => 'Leads ayant regardé au moins 75% d\'une vidéo',
                'color' => '#8B5CF6',
                'icon' => 'play',
                'count' => (clone $baseQuery)
                    ->whereHas('events', function ($q) {
                        $q->whereIn('type', [
                            \App\Enums\EventType::VIDEO_75,
                            \App\Enums\EventType::VIDEO_100,
                        ]);
                    })
                    ->count(),
            ],
            [
                'key' => 'new_24h',
                'name' => '🆕 Nouveaux 24h',
                'description' => 'Leads capturés dans les dernières 24 heures',
                'color' => '#10B981',
                'icon' => 'sparkles',
                'count' => (clone $baseQuery)
                    ->where('created_at', '>=', now()->subHours(24))
                    ->count(),
            ],
            [
                'key' => 'ready_to_convert',
                'name' => '🟣 Prêts à convertir',
                'description' => 'Leads Ultra Chauds pas encore clients',
                'color' => '#8B5CF6',
                'icon' => 'rocket',
                'count' => (clone $baseQuery)
                    ->where('status', LeadStatus::ULTRA_HOT)
                    ->whereNull('converted_at')
                    ->count(),
            ],
            [
                'key' => 'no_email',
                'name' => '📧 Sans email',
                'description' => 'Leads identifiés mais sans adresse email',
                'color' => '#F59E0B',
                'icon' => 'envelope',
                'count' => (clone $baseQuery)
                    ->whereNull('email')
                    ->whereNotNull('phone')
                    ->count(),
            ],
        ];
    }

    /**
     * Get leads for a specific dynamic segment
     */
    public function getLeadsBySegment(Tenant $tenant, string $segmentKey, int $perPage = 25): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Lead::where('tenant_id', $tenant->id)
            ->with(['tags', 'funnel', 'assignedTo', 'broughtBy']);

        match ($segmentKey) {
            'hot_no_contact' => $query
                ->whereIn('status', [LeadStatus::HOT, LeadStatus::ULTRA_HOT])
                ->whereDoesntHave('events', function ($q) {
                    $q->where('type', \App\Enums\EventType::WHATSAPP_CLICK);
                }),

            'inactive_7d' => $query
                ->whereNotIn('status', [LeadStatus::CLIENT, LeadStatus::MEMBER])
                ->where(function ($q) {
                    $q->where('last_activity_at', '<', now()->subDays(7))
                        ->orWhere(function ($q2) {
                            $q2->whereNull('last_activity_at')
                                ->where('created_at', '<', now()->subDays(7));
                        });
                }),

            'video_engaged' => $query
                ->whereHas('events', function ($q) {
                    $q->whereIn('type', [
                        \App\Enums\EventType::VIDEO_75,
                        \App\Enums\EventType::VIDEO_100,
                    ]);
                }),

            'new_24h' => $query
                ->where('created_at', '>=', now()->subHours(24)),

            'ready_to_convert' => $query
                ->where('status', LeadStatus::ULTRA_HOT)
                ->whereNull('converted_at'),

            'no_email' => $query
                ->whereNull('email')
                ->whereNotNull('phone'),

            default => $query,
        };

        return $query->orderByDesc('last_activity_at')->paginate($perPage);
    }

    /*
    |--------------------------------------------------------------------------
    | Fusion de Doublons
    |--------------------------------------------------------------------------
    */

    /**
     * Find potential duplicate leads based on email or phone
     */
    public function findDuplicates(Tenant $tenant): Collection
    {
        $duplicates = collect();

        // 1. Doublons par email
        $emailDuplicates = Lead::where('tenant_id', $tenant->id)
            ->whereNotNull('email')
            ->select('email', DB::raw('COUNT(*) as count'), DB::raw('GROUP_CONCAT(id) as ids'))
            ->groupBy('email')
            ->having('count', '>', 1)
            ->get();

        foreach ($emailDuplicates as $dup) {
            $leads = Lead::whereIn('id', explode(',', $dup->ids))
                ->with(['tags', 'funnel', 'events'])
                ->orderByDesc('score')
                ->get();

            $duplicates->push([
                'type' => 'email',
                'value' => $dup->email,
                'count' => $dup->count,
                'leads' => $leads,
                'primary' => $leads->first(), // Celui avec le plus haut score
            ]);
        }

        // 2. Doublons par téléphone (qui ne sont pas déjà trouvés par email)
        $phoneDuplicates = Lead::where('tenant_id', $tenant->id)
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->select('phone', DB::raw('COUNT(*) as count'), DB::raw('GROUP_CONCAT(id) as ids'))
            ->groupBy('phone')
            ->having('count', '>', 1)
            ->get();

        foreach ($phoneDuplicates as $dup) {
            $leads = Lead::whereIn('id', explode(',', $dup->ids))
                ->with(['tags', 'funnel', 'events'])
                ->orderByDesc('score')
                ->get();

            // Éviter les doublons déjà détectés par email
            $alreadyFound = $duplicates->contains(function ($existing) use ($leads) {
                $existingIds = $existing['leads']->pluck('id')->sort()->values();
                $currentIds = $leads->pluck('id')->sort()->values();
                return $existingIds->toArray() === $currentIds->toArray();
            });

            if (!$alreadyFound) {
                $duplicates->push([
                    'type' => 'phone',
                    'value' => $dup->phone,
                    'count' => $dup->count,
                    'leads' => $leads,
                    'primary' => $leads->first(),
                ]);
            }
        }

        return $duplicates;
    }

    /**
     * Merge duplicate leads into the primary lead
     * Keeps the primary lead, transfers data from secondaries, and soft-deletes them
     *
     * @param Lead $primaryLead The lead to keep
     * @param Collection|array $secondaryLeadIds IDs of leads to merge into primary
     */
    public function mergeLeads(Lead $primaryLead, array $secondaryLeadIds): Lead
    {
        return DB::transaction(function () use ($primaryLead, $secondaryLeadIds) {
            $secondaryLeads = Lead::whereIn('id', $secondaryLeadIds)
                ->where('id', '!=', $primaryLead->id)
                ->with(['tags', 'events', 'notes'])
                ->get();

            foreach ($secondaryLeads as $secondary) {
                // 1. Fusionner les données manquantes
                if (!$primaryLead->email && $secondary->email) {
                    $primaryLead->email = $secondary->email;
                }
                if (!$primaryLead->phone && $secondary->phone) {
                    $primaryLead->phone = $secondary->phone;
                }
                if (!$primaryLead->first_name && $secondary->first_name) {
                    $primaryLead->first_name = $secondary->first_name;
                }
                if (!$primaryLead->last_name && $secondary->last_name) {
                    $primaryLead->last_name = $secondary->last_name;
                }
                if (!$primaryLead->country && $secondary->country) {
                    $primaryLead->country = $secondary->country;
                }
                if (!$primaryLead->city && $secondary->city) {
                    $primaryLead->city = $secondary->city;
                }

                // 2. Combiner les custom fields
                $primaryCustom = $primaryLead->custom_fields ?? [];
                $secondaryCustom = $secondary->custom_fields ?? [];
                $primaryLead->custom_fields = array_merge($secondaryCustom, $primaryCustom);

                // 3. Garder le meilleur score
                if ($secondary->score > $primaryLead->score) {
                    $primaryLead->score = $secondary->score;
                }

                // 4. Garder le statut le plus avancé
                if ($secondary->status->priority() > $primaryLead->status->priority()) {
                    $primaryLead->status = $secondary->status;
                }

                // 5. Garder la date de conversion la plus ancienne
                if ($secondary->converted_at && (!$primaryLead->converted_at || $secondary->converted_at < $primaryLead->converted_at)) {
                    $primaryLead->converted_at = $secondary->converted_at;
                }

                // 6. Transférer les tags (sans doublon)
                $existingTagIds = $primaryLead->tags()->pluck('tags.id')->toArray();
                $newTags = $secondary->tags()
                    ->whereNotIn('tags.id', $existingTagIds)
                    ->pluck('tags.id');
                if ($newTags->isNotEmpty()) {
                    $primaryLead->tags()->attach($newTags);
                }

                // 7. Transférer les événements
                $secondary->events()->update(['lead_id' => $primaryLead->id]);

                // 8. Transférer les notes
                $secondary->notes()->update(['lead_id' => $primaryLead->id]);

                // 9. Transférer les alertes
                $secondary->alerts()->update(['lead_id' => $primaryLead->id]);

                // 10. Ajouter une note de fusion
                $primaryLead->addNote(
                    "🔀 Fusionné avec Lead #{$secondary->id} ({$secondary->getDisplayName()}). " .
                    "Email: {$secondary->email}, Tél: {$secondary->phone}, Score: {$secondary->score}",
                    null,
                    false
                );

                // 11. Soft-delete le lead secondaire
                $secondary->delete();
            }

            // Garder le last_activity_at le plus récent
            $latestActivity = Lead::withTrashed()
                ->whereIn('id', array_merge([$primaryLead->id], $secondaryLeadIds))
                ->max('last_activity_at');

            if ($latestActivity) {
                $primaryLead->last_activity_at = $latestActivity;
            }

            $primaryLead->save();

            \Log::info('🔀 [LEAD MERGE] Fusion de leads effectuée', [
                'primary_id' => $primaryLead->id,
                'merged_ids' => $secondaryLeadIds,
                'final_score' => $primaryLead->score,
                'final_status' => $primaryLead->status->value,
            ]);

            return $primaryLead->fresh()->load(['tags', 'events', 'notes']);
        });
    }
}
