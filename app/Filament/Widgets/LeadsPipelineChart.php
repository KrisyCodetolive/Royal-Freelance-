<?php

namespace App\Filament\Widgets;

use App\Enums\LeadStatus;
use App\Models\Lead;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class LeadsPipelineChart extends ChartWidget
{
    protected ?string $heading = 'Pipeline des Leads';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $tenant = Auth::user()->tenant;

        if (!$tenant) {
            return ['datasets' => [], 'labels' => []];
        }

        $pipeline = [
            LeadStatus::COLD->value => Lead::where('status', LeadStatus::COLD)->count(),
            LeadStatus::WARM->value => Lead::where('status', LeadStatus::WARM)->count(),
            LeadStatus::HOT->value => Lead::where('status', LeadStatus::HOT)->count(),
            LeadStatus::ULTRA_HOT->value => Lead::where('status', LeadStatus::ULTRA_HOT)->count(),
            LeadStatus::CLIENT->value => Lead::where('status', LeadStatus::CLIENT)->count(),
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Leads par statut',
                    'data' => array_values($pipeline),
                    'backgroundColor' => [
                        '#94a3b8', // Cold - gray
                        '#60a5fa', // Warm - blue
                        '#fb923c', // Hot - orange
                        '#ef4444', // Ultra Hot - red
                        '#22c55e', // Client - green
                    ],
                    'borderWidth' => 0,
                ],
            ],
            'labels' => ['Froid', 'Tiède', 'Chaud', 'Ultra Chaud', 'Client'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}
