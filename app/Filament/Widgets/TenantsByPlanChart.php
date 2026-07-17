<?php

namespace App\Filament\Widgets;

use App\Models\Plan;
use Filament\Widgets\ChartWidget;

class TenantsByPlanChart extends ChartWidget
{
    protected ?string $heading = 'Répartition des tenants par plan';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'lg' => 6,
    ];

    protected ?string $maxHeight = '250px';

    protected function getData(): array
    {
        $plans = Plan::orderBy('price_monthly')->get();

        $counts = $plans->map(
            fn (Plan $plan) => $plan->subscriptions()->active()->count()
        );

        return [
            'datasets' => [
                [
                    'label' => 'Tenants',
                    'data' => $counts->all(),
                    'backgroundColor' => ['#94a3b8', '#6366f1', '#f59e0b'],
                ],
            ],
            'labels' => $plans->pluck('name')->all(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
