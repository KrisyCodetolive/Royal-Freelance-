<?php

namespace App\Filament\Widgets;

use App\Models\Subscription;
use App\Models\Tenant;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * Module 7 — Super Admin : métriques plateforme au-dessus du niveau tenant.
 * Réservé au super_admin via SaasAnalytics::canAccess() (page hôte).
 */
class SaasAnalyticsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $totalTenants = Tenant::count();
        $suspendedTenants = Tenant::whereNotNull('suspended_at')->count();
        $newTenantsThisWeek = Tenant::where('created_at', '>=', now()->subDays(7))->count();

        return [
            Stat::make('Tenants', number_format($totalTenants))
                ->description("{$newTenantsThisWeek} nouveaux cette semaine")
                ->descriptionIcon('heroicon-o-building-office-2')
                ->color('primary'),

            Stat::make('Tenants actifs', number_format($totalTenants - $suspendedTenants))
                ->description($suspendedTenants > 0 ? "{$suspendedTenants} suspendu(s)" : 'Aucun suspendu')
                ->descriptionIcon($suspendedTenants > 0 ? 'heroicon-o-exclamation-triangle' : 'heroicon-o-check-circle')
                ->color($suspendedTenants > 0 ? 'warning' : 'success'),

            Stat::make('MRR (mocké)', number_format($this->monthlyRecurringRevenue()) . ' FCFA')
                ->description('Paiement mocké, pas de gateway (cf. ROADMAP_SAAS_PHASE3.md)')
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('success'),
        ];
    }

    /**
     * Somme des abonnements actifs, ramenés à un équivalent mensuel
     * (price_yearly / 12 pour les cycles annuels) — même logique de
     * "mocké" que le reste du billing (Module 2). Public pour être
     * testable directement, comme QuotaUsageWidget::getUsage().
     */
    public function monthlyRecurringRevenue(): float
    {
        return Subscription::query()
            ->active()
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>', now()))
            ->with('plan')
            ->get()
            ->sum(function (Subscription $subscription) {
                $plan = $subscription->plan;

                if (!$plan) {
                    return 0;
                }

                return $subscription->cycle === 'yearly'
                    ? $plan->price_yearly / 12
                    : $plan->price_monthly;
            });
    }
}
