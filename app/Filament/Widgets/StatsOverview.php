<?php

namespace App\Filament\Widgets;

use App\Models\Funnel;
use App\Models\Lead;
use App\Models\User;
use App\Models\EmailSequenceEmailSend;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int|string|array $columnSpan = 'full';
    protected ?string $pollingInterval = '60s';

    protected function getStats(): array
    {
        // Période courante vs précédente (7j)
        $totalLeads     = Lead::query()->count();
        $prevLeads      = Lead::query()->where('created_at', '<', now()->subDays(7))->count();
        $leadsThisWeek  = Lead::query()->where('created_at', '>=', now()->subDays(7))->count();
        $leadsPrevWeek  = Lead::query()->whereBetween('created_at', [now()->subDays(14), now()->subDays(7)])->count();
        $leadsTrend     = $leadsPrevWeek > 0 ? round(($leadsThisWeek - $leadsPrevWeek) / $leadsPrevWeek * 100) : 0;

        $hotLeads       = Lead::query()->where('score', '>=', 31)->count();
        $hotThisWeek    = Lead::query()->where('score', '>=', 31)->where('created_at', '>=', now()->subDays(7))->count();
        $hotPrevWeek    = Lead::query()->where('score', '>=', 31)->whereBetween('created_at', [now()->subDays(14), now()->subDays(7)])->count();
        $hotTrend       = $hotPrevWeek > 0 ? round(($hotThisWeek - $hotPrevWeek) / $hotPrevWeek * 100) : 0;

        $conversions    = Lead::query()->whereNotNull('converted_at')->count();
        $convRate       = $totalLeads > 0 ? round($conversions / $totalLeads * 100, 1) : 0;
        $convThisWeek   = Lead::query()->whereNotNull('converted_at')->where('converted_at', '>=', now()->subDays(7))->count();
        $convPrevWeek   = Lead::query()->whereNotNull('converted_at')->whereBetween('converted_at', [now()->subDays(14), now()->subDays(7)])->count();
        $convTrend      = $convPrevWeek > 0 ? round(($convThisWeek - $convPrevWeek) / $convPrevWeek * 100) : 0;

        $activeFunnels  = Funnel::where('status', 'active')->count();
        $commercials    = User::role('commercial')->where('is_active', true)->count();
        $emailsSent     = EmailSequenceEmailSend::count();

        return [
            Stat::make('Total Leads', number_format($totalLeads))
                ->description($this->trendLabel($leadsTrend, 'cette semaine'))
                ->descriptionIcon($leadsTrend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($leadsTrend >= 0 ? 'success' : 'danger')
                ->chart($this->getDailyChart('leads', 7)),

            Stat::make('Leads Chauds 🔥', number_format($hotLeads))
                ->description($this->trendLabel($hotTrend, 'cette semaine'))
                ->descriptionIcon($hotTrend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($hotTrend >= 0 ? 'warning' : 'danger')
                ->chart($this->getDailyChart('hot', 7)),

            Stat::make('Conversions', number_format($conversions))
                ->description($convRate . '% taux — ' . $this->trendLabel($convTrend, 'cette semaine'))
                ->descriptionIcon($convTrend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($convTrend >= 0 ? 'success' : 'danger')
                ->chart($this->getDailyChart('conversions', 7)),

            Stat::make('Tunnels Actifs', $activeFunnels)
                ->description("{$commercials} commerciaux actifs")
                ->descriptionIcon('heroicon-o-funnel')
                ->color('info'),

            Stat::make('Emails Envoyés', number_format($emailsSent))
                ->description('Séquences automatiques')
                ->descriptionIcon('heroicon-o-envelope')
                ->color('primary'),
        ];
    }

    private function trendLabel(int $trend, string $period): string
    {
        if ($trend === 0) return "Stable {$period}";
        $arrow = $trend > 0 ? '↑' : '↓';
        return "{$arrow} " . abs($trend) . "% {$period}";
    }

    private function getDailyChart(string $type, int $days): array
    {
        $data = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = today()->subDays($i);
            $data[] = match ($type) {
                'leads'       => Lead::whereDate('created_at', $date)->count(),
                'hot'         => Lead::query()->where('score', '>=', 31)->whereDate('created_at', $date)->count(),
                'conversions' => Lead::query()->whereNotNull('converted_at')->whereDate('converted_at', $date)->count(),
                default       => 0,
            };
        }
        return $data;
    }
}
