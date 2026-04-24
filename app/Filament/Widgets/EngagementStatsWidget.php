<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use App\Models\Event;
use App\Enums\EventType;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class EngagementStatsWidget extends ChartWidget
{
    protected ?string $heading = 'Engagement Leads (7 derniers jours)';

    protected ?string $pollingInterval = '30s';

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $labels = [];
        $videoViews = [];
        $formSubmits = [];
        $whatsappClicks = [];

        // 7 derniers jours
        for ($i = 6; $i >= 0; $i--) {
            $date = today()->subDays($i);
            $labels[] = $date->format('d/m');

            // Vidéos vues
            $videoViews[] = Event::where('type', EventType::VIDEO_100)
                ->whereDate('created_at', $date)
                ->count();

            // Formulaires soumis
            $formSubmits[] = Event::where('type', EventType::FORM_SUBMIT)
                ->whereDate('created_at', $date)
                ->count();

            // Clics WhatsApp
            $whatsappClicks[] = Event::where('type', EventType::WHATSAPP_CLICK)
                ->whereDate('created_at', $date)
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Vidéos 100%',
                    'data' => $videoViews,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.5)',
                    'borderColor' => 'rgb(59, 130, 246)',
                ],
                [
                    'label' => 'Formulaires',
                    'data' => $formSubmits,
                    'backgroundColor' => 'rgba(16, 185, 129, 0.5)',
                    'borderColor' => 'rgb(16, 185, 129)',
                ],
                [
                    'label' => 'WhatsApp',
                    'data' => $whatsappClicks,
                    'backgroundColor' => 'rgba(245, 158, 11, 0.5)',
                    'borderColor' => 'rgb(245, 158, 11)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}
