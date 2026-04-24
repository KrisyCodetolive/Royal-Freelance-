<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use Filament\Widgets\ChartWidget;

class LeadsByDeviceWidget extends ChartWidget
{
    protected ?string $heading = 'Répartition par Device';

    protected ?string $pollingInterval = '30s';

    protected static ?int $sort = 6;

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'lg' => 6,
    ];

    protected function getData(): array
    {
        $mobile = Lead::withoutGlobalScope('tenant')->where('device_type', 'mobile')->count();
        $desktop = Lead::withoutGlobalScope('tenant')->where('device_type', 'desktop')->count();
        $tablet = Lead::withoutGlobalScope('tenant')->where('device_type', 'tablet')->count();
        $unknown = Lead::withoutGlobalScope('tenant')->whereNull('device_type')->count();

        return [
            'datasets' => [
                [
                    'label' => 'Leads par Device',
                    'data' => [$mobile, $desktop, $tablet, $unknown],
                    'backgroundColor' => [
                        'rgba(59, 130, 246, 0.8)',   // Bleu - Mobile
                        'rgba(16, 185, 129, 0.8)',   // Vert - Desktop
                        'rgba(245, 158, 11, 0.8)',   // Orange - Tablet
                        'rgba(107, 114, 128, 0.8)',  // Gris - Unknown
                    ],
                ],
            ],
            'labels' => ['📱 Mobile', '🖥️ Desktop', '📱 Tablet', '❓ Inconnu'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
