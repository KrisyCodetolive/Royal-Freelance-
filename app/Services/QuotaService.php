<?php

namespace App\Services;

use App\Models\EmailSequence;
use App\Models\Funnel;
use App\Models\Lead;
use App\Models\Tenant;

/**
 * Calcule l'usage courant d'un tenant face aux limites de son plan actif
 * (Module 3 — Quotas par plan). Enforcement câblé sur les créations côté
 * panel admin (tunnels, listes mailing), sur le partage de tunnels
 * (Module 4, cf. canShareFunnel()), et sur la création de leads
 * (TrackingService::trackVisitor()/createLeadFromForm(), cf.
 * ROADMAP_SAAS_PHASE3.md pour la décision d'aller jusqu'au blocage du
 * tracking de visite).
 */
class QuotaService
{
    private const QUOTA_TO_PLAN_FIELD = [
        'tunnels' => 'max_tunnels',
        'mailing_lists' => 'max_mailing_lists',
        'leads' => 'max_leads',
        'shared_tunnels' => 'max_shared_tunnels',
    ];

    public function usage(Tenant $tenant): array
    {
        return [
            'tunnels' => Funnel::where('tenant_id', $tenant->id)->where('is_template', false)->count(),
            'mailing_lists' => EmailSequence::where('tenant_id', $tenant->id)->count(),
            'leads' => Lead::where('tenant_id', $tenant->id)->count(),
            'shared_tunnels' => Funnel::where('tenant_id', $tenant->id)
                ->where('is_template', false)
                ->where(fn ($q) => $q->whereHas('users')->orWhereHas('commercialGroups'))
                ->count(),
        ];
    }

    /**
     * Un tunnel déjà partagé (individuellement ou via un groupe) ne consomme pas
     * de quota supplémentaire quand on y ajoute un partage de plus — seul le
     * premier partage d'un tunnel jusque-là privé compte comme une nouvelle unité.
     */
    public function isFunnelAlreadyShared(Funnel $funnel): bool
    {
        return $funnel->users()->exists() || $funnel->commercialGroups()->exists();
    }

    /**
     * À appeler avant d'attacher un nouveau partage (utilisateur ou groupe) sur un tunnel.
     */
    public function canShareFunnel(Tenant $tenant, Funnel $funnel): bool
    {
        if ($this->isFunnelAlreadyShared($funnel)) {
            return true;
        }

        return !$this->hasReachedLimit($tenant, 'shared_tunnels');
    }

    /**
     * Limite pour une quota donnée. null = illimité.
     * Si le tenant n'a pas d'abonnement actif, la limite est 0 (aucune création autorisée).
     */
    public function limit(Tenant $tenant, string $quotaKey): ?int
    {
        $plan = $tenant->currentPlan();

        if (!$plan) {
            return 0;
        }

        $field = self::QUOTA_TO_PLAN_FIELD[$quotaKey] ?? null;

        return $field ? $plan->{$field} : 0;
    }

    /**
     * Quantité restante avant d'atteindre la limite. null = illimité.
     */
    public function remaining(Tenant $tenant, string $quotaKey): ?int
    {
        $limit = $this->limit($tenant, $quotaKey);

        if (is_null($limit)) {
            return null;
        }

        $used = $this->usage($tenant)[$quotaKey] ?? 0;

        return max(0, $limit - $used);
    }

    public function hasReachedLimit(Tenant $tenant, string $quotaKey): bool
    {
        $remaining = $this->remaining($tenant, $quotaKey);

        return !is_null($remaining) && $remaining <= 0;
    }

    /**
     * Usage + limite pour chaque quota, prêt à afficher (Module 6 — Dashboard
     * SaaS et page "Mon abonnement"). `limit` = null signifie illimité.
     */
    public function usageWithLimits(Tenant $tenant): array
    {
        $usage = $this->usage($tenant);

        return collect(array_keys(self::QUOTA_TO_PLAN_FIELD))
            ->mapWithKeys(fn (string $key) => [$key => [
                'used' => $usage[$key] ?? 0,
                'limit' => $this->limit($tenant, $key),
            ]])
            ->all();
    }
}
