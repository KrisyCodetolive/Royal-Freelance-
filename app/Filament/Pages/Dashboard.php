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
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationLabel = 'Tableau de bord';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?int $navigationSort = -1;

    protected static ?string $title = 'Tableau de bord';

    public function getColumns(): int | array
    {
        return 12;
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make()
                ->persistTabInQueryString('tab')
                ->tabs([
                    Tab::make('Vue d\'ensemble')
                        ->icon('heroicon-o-home')
                        ->schema([
                            Grid::make(12)->schema(
                                $this->getWidgetsSchemaComponents([
                                    StatsOverview::class,
                                    LeadsPipelineChart::class,
                                    LatestLeads::class,
                                ])
                            ),
                        ]),

                    Tab::make('Analyse')
                        ->icon('heroicon-o-chart-bar')
                        ->schema([
                            Grid::make(12)->schema(
                                $this->getWidgetsSchemaComponents([
                                    EngagementStatsWidget::class,
                                    LeadsByDeviceWidget::class,
                                    ConversionByCountryWidget::class,
                                    RevenuePerformanceWidget::class,
                                ])
                            ),
                        ]),

                    Tab::make('Équipe')
                        ->icon('heroicon-o-user-group')
                        ->schema([
                            Grid::make()->schema(
                                $this->getWidgetsSchemaComponents([
                                    TeamPerformanceWidget::class,
                                ])
                            ),
                        ]),
                ]),
        ]);
    }
}
