<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use App\Enums\LeadStatus;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ConversionByCountryWidget extends ChartWidget
{
    protected ?string $heading = 'Top Pays par Conversion';

    protected ?string $pollingInterval = '60s';

    protected static ?int $sort = 7;

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'lg' => 6,
    ];

    protected ?string $maxHeight = '200px';

    protected function getData(): array
    {
        // Top 10 pays avec le plus de leads convertis (CONVERTED)
        $topCountries = Lead::withoutGlobalScope('tenant')
            ->select('country', DB::raw('count(*) as total'))
            ->whereNotNull('converted_at')
            ->whereNotNull('country')
            ->groupBy('country')
            ->orderBy('total', 'desc')
            ->take(10)
            ->get();

        $labels = [];
        $data = [];
        $colors = [
            'rgba(239, 68, 68, 0.8)',
            'rgba(59, 130, 246, 0.8)',
            'rgba(16, 185, 129, 0.8)',
            'rgba(245, 158, 11, 0.8)',
            'rgba(139, 92, 246, 0.8)',
            'rgba(236, 72, 153, 0.8)',
            'rgba(20, 184, 166, 0.8)',
            'rgba(251, 146, 60, 0.8)',
            'rgba(168, 85, 247, 0.8)',
            'rgba(52, 211, 153, 0.8)',
        ];

        foreach ($topCountries as $country) {
            $countryName = \Locale::getDisplayRegion("-{$country->country}", 'fr') ?: $country->country;
            $labels[] = $countryName;
            $data[] = $country->total;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Leads Convertis',
                    'data' => $data,
                    'backgroundColor' => array_slice($colors, 0, count($data)),
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
                    'beginAtZero' => true,
                ],
            ],
            'indexAxis' => 'y',
        ];
    }
}
