<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use App\Models\Funnel;
use App\Models\Tag;
use App\Enums\LeadStatus;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class LeadOverviewWidget extends BaseWidget
{
    protected static bool $isDiscovered = false;

    protected ?string $pollingInterval = '30s';

    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        // === STATS LEADS ===
        $totalLeads = Lead::count();
        $leadsToday = Lead::whereDate('created_at', today())->count();
        $leadsThisWeek = Lead::whereBetween('created_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ])->count();

        // Taux de conversion
        $convertedCount = Lead::whereNotNull('converted_at')->count();
        $conversionRate = $totalLeads > 0 ? round(($convertedCount / $totalLeads) * 100, 1) : 0;

        // Leads HOT
        $hotLeads = Lead::where('score', '>=', 31)->count();

        // Tunnels actifs
        $activeFunnels = Funnel::where('status', 'active')->count();

        // === STATS TAGS ===
        $topTag = Tag::withCount('leads')
            ->orderBy('leads_count', 'desc')
            ->first();

        return [
            // Stats principales
            Stat::make('Total Leads', number_format($totalLeads))
                ->description($leadsToday . ' aujourd\'hui, ' . $leadsThisWeek . ' cette semaine')
                ->descriptionIcon('heroicon-o-users')
                ->color('primary')
                ->chart($this->getLeadsChart()),

            Stat::make('Leads HOT 🔥', $hotLeads)
                ->description('Score ≥ 31 points')
                ->descriptionIcon('heroicon-o-fire')
                ->color('warning'),

            Stat::make('Taux Conversion', $conversionRate . '%')
                ->description($convertedCount . ' leads convertis')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color($conversionRate >= 10 ? 'success' : ($conversionRate >= 5 ? 'warning' : 'danger')),

            Stat::make('Tunnels Actifs', $activeFunnels)
                ->description('En production')
                ->descriptionIcon('heroicon-o-funnel')
                ->color('success'),

            // Top tag
            Stat::make('Top Tag', $topTag ? $topTag->name : 'Aucun')
                ->description($topTag ? $topTag->leads_count . ' leads tagués' : 'Aucun tag')
                ->descriptionIcon('heroicon-o-tag')
                ->color('info'),
        ];
    }

    protected function getLeadsChart(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = today()->subDays($i);
            $count = Lead::whereDate('created_at', $date)->count();
            $data[] = $count;
        }
        return $data;
    }
}

