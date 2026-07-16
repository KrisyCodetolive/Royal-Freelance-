<?php

namespace App\Filament\Widgets;

use App\Services\QuotaService;
use Filament\Widgets\Widget;

/**
 * Module 6 — Dashboard SaaS : usage courant du tenant face aux limites de
 * son plan (tunnels, listes mailing, leads, tunnels partagés), avec CTA
 * d'upgrade contextuel. Visible de tous les rôles ayant accès au panel
 * (Owner/Admin/Editor/Viewer) : la donnée est au niveau du tenant, pas de
 * l'utilisateur, mais seul l'Owner peut agir dessus (cf. MySubscription).
 */
class QuotaUsageWidget extends Widget
{
    protected string $view = 'filament.widgets.quota-usage';

    protected static ?int $sort = -1;

    protected int|string|array $columnSpan = 'full';

    private const LABELS = [
        'tunnels' => 'Tunnels',
        'mailing_lists' => 'Listes mailing',
        'leads' => 'Leads',
        'shared_tunnels' => 'Tunnels partagés',
    ];

    private const ICONS = [
        'tunnels' => 'heroicon-o-funnel',
        'mailing_lists' => 'heroicon-o-envelope',
        'leads' => 'heroicon-o-user-group',
        'shared_tunnels' => 'heroicon-o-share',
    ];

    public static function canView(): bool
    {
        return (bool) auth()->user()?->tenant_id;
    }

    public function getPlanName(): ?string
    {
        return $this->tenant()?->currentPlan()?->name;
    }

    public function getUsage(): array
    {
        $tenant = $this->tenant();

        if (!$tenant) {
            return [];
        }

        $usage = app(QuotaService::class)->usageWithLimits($tenant);

        return collect($usage)
            ->map(function (array $data, string $key) {
                $ratio = $data['limit'] ? min(1, $data['used'] / max(1, $data['limit'])) : 0;

                return [
                    ...$data,
                    'label' => self::LABELS[$key] ?? $key,
                    'icon' => self::ICONS[$key] ?? 'heroicon-o-chart-bar',
                    'ratio' => $ratio,
                    'is_near_limit' => !is_null($data['limit']) && $ratio >= 0.8,
                    'severity' => match (true) {
                        is_null($data['limit']) => 'unlimited',
                        $ratio >= 1 => 'danger',
                        $ratio >= 0.8 => 'warning',
                        default => 'success',
                    },
                ];
            })
            ->all();
    }

    public function hasAnyQuotaNearLimit(): bool
    {
        return collect($this->getUsage())->contains(fn (array $data) => $data['is_near_limit']);
    }

    public function canManageBilling(): bool
    {
        return (bool) auth()->user()?->isOwner();
    }

    private function tenant(): ?\App\Models\Tenant
    {
        return auth()->user()?->tenant;
    }
}
