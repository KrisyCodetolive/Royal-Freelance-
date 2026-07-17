<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\SaasAnalyticsOverview;
use App\Filament\Widgets\TenantGrowthChart;
use App\Filament\Widgets\TenantsByPlanChart;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

/**
 * Module 7 — Super Admin : analytics SaaS globale, au-dessus du niveau
 * tenant (nb tenants/statut, répartition par plan, MRR mocké, croissance).
 * Réservé au super_admin, cf. TenantResource/SubscriptionResource pour la
 * même convention de restriction.
 */
class SaasAnalytics extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedPresentationChartLine;

    protected static string|\UnitEnum|null $navigationGroup = 'Super Admin';

    protected static ?string $navigationLabel = 'Analytics SaaS';

    protected static ?string $title = 'Analytics SaaS';

    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(12)->schema(
                $this->getWidgetsSchemaComponents([
                    SaasAnalyticsOverview::class,
                    TenantsByPlanChart::class,
                    TenantGrowthChart::class,
                ])
            ),
        ]);
    }
}
