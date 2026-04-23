<?php

namespace App\Filament\Resources\Leads\Widgets;

use App\Models\Lead;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class LeadsChart extends ChartWidget
{
    protected ?string $heading = 'Évolution des Leads';

    protected static ?int $sort = 2;

    public ?string $filter = '30days';

    protected function getData(): array
    {
        $user = Auth::user();

        // Base query selon le rôle
        $query = Lead::query();

        if ($user->hasRole('commercial')) {
            $query->where('brought_by', $user->id);
        }

        // Déterminer la période selon le filtre
        $start = match ($this->filter) {
            '7days' => now()->subDays(7),
            '30days' => now()->subDays(30),
            '90days' => now()->subDays(90),
            'year' => now()->subYear(),
            default => now()->subDays(30),
        };

        $data = Trend::query($query)
            ->between(
                start: $start,
                end: now(),
            )
            ->perDay()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Nouveaux Leads',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getFilters(): ?array
    {
        return [
            '7days' => '7 derniers jours',
            '30days' => '30 derniers jours',
            '90days' => '90 derniers jours',
            'year' => 'Cette année',
        ];
    }
}
