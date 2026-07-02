<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use App\Enums\LeadStatus;
use Filament\Widgets\ChartWidget;

class RevenuePerformanceWidget extends ChartWidget
{
    protected ?string $heading = 'Performance Revenus (30 derniers jours)';

    protected ?string $pollingInterval = '60s';

    protected static ?int $sort = 8;

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'lg' => 6,
    ];

    protected ?string $maxHeight = '200px';

    protected function getData(): array
    {
        $labels = [];
        $conversions = [];
        $revenue = [];

        // 30 derniers jours
        for ($i = 29; $i >= 0; $i--) {
            $date = today()->subDays($i);
            $labels[] = $date->format('d/m');

            // Conversions du jour
            $dayConversions = Lead::query()
                ->whereNotNull('converted_at')
                ->whereDate('converted_at', $date)
                ->count();
            $conversions[] = $dayConversions;

            // Revenue estimé (basé sur score moyen des conversions)
            $dayRevenue = Lead::query()
                ->whereNotNull('converted_at')
                ->whereDate('converted_at', $date)
                ->avg('score') ?? 0;
            $revenue[] = round($dayRevenue);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Conversions',
                    'data' => $conversions,
                    'backgroundColor' => 'rgba(16, 185, 129, 0.5)',
                    'borderColor' => 'rgb(16, 185, 129)',
                    'borderWidth' => 2,
                    'yAxisID' => 'y',
                ],
                [
                    'label' => 'Score Moyen',
                    'data' => $revenue,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.5)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 2,
                    'yAxisID' => 'y1',
                    'type' => 'line',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'left',
                    'title' => [
                        'display' => true,
                        'text' => 'Conversions',
                    ],
                    'beginAtZero' => true,
                ],
                'y1' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'right',
                    'title' => [
                        'display' => true,
                        'text' => 'Score Moyen',
                    ],
                    'beginAtZero' => true,
                    'grid' => [
                        'drawOnChartArea' => false,
                    ],
                ],
            ],
        ];
    }
}
