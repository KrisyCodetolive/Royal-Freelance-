<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\LeadsPipelineChart;
use App\Filament\Widgets\EngagementStatsWidget;
use App\Filament\Widgets\LatestLeads;
use App\Filament\Widgets\LeadsByDeviceWidget;
use App\Filament\Widgets\ConversionByCountryWidget;
use App\Filament\Widgets\RevenuePerformanceWidget;
use App\Filament\Widgets\TeamPerformanceWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationLabel = 'Tableau de bord';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?int $navigationSort = -1;

    protected static ?string $title = 'Tableau de bord';

    public function getWidgets(): array
    {
        return [
            StatsOverview::class,
            LeadsPipelineChart::class,
            EngagementStatsWidget::class,
            LatestLeads::class,
            LeadsByDeviceWidget::class,
            ConversionByCountryWidget::class,
            RevenuePerformanceWidget::class,
            TeamPerformanceWidget::class,
        ];
    }

    public function getColumns(): int | array
    {
        return 12;
    }
}
