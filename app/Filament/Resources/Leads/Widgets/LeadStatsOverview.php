<?php

namespace App\Filament\Resources\Leads\Widgets;

use App\Models\Lead;
use App\Enums\LeadStatus;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class LeadStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();

        // Base query selon le rôle
        $query = Lead::query();

        if ($user->hasRole('commercial')) {
            // Commercial voit uniquement ses leads
            $query->where('brought_by', $user->id);
        }

        // Stats globales
        $totalLeads = (clone $query)->count();
        $newLeadsThisWeek = (clone $query)->where('created_at', '>=', now()->startOfWeek())->count();
        $newLeadsLastWeek = (clone $query)
            ->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])
            ->count();

        // Calcul du pourcentage de changement
        $weekChange = $newLeadsLastWeek > 0
            ? round((($newLeadsThisWeek - $newLeadsLastWeek) / $newLeadsLastWeek) * 100, 1)
            : ($newLeadsThisWeek > 0 ? 100 : 0);

        // Leads chauds (hot + ultra_hot)
        $hotLeads = (clone $query)->whereIn('status', [LeadStatus::HOT, LeadStatus::ULTRA_HOT])->count();

        // Taux de conversion (leads qualifiés / total)
        $qualifiedLeads = (clone $query)->whereIn('status', [LeadStatus::HOT, LeadStatus::WARM, LeadStatus::ULTRA_HOT, LeadStatus::CLIENT])->count();
        $conversionRate = $totalLeads > 0 ? round(($qualifiedLeads / $totalLeads) * 100, 1) : 0;

        return [
            Stat::make('Total Leads', $totalLeads)
                ->description($newLeadsThisWeek . ' nouveaux cette semaine')
                ->descriptionIcon($weekChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($weekChange >= 0 ? 'success' : 'danger')
                ->chart($this->getWeeklyChart($query)),

            Stat::make('Leads Chauds', $hotLeads)
                ->description('Prêts à convertir')
                ->descriptionIcon('heroicon-m-fire')
                ->color('warning'),

            Stat::make('Taux de Qualification', $conversionRate . '%')
                ->description('Leads qualifiés / Total')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($conversionRate >= 30 ? 'success' : ($conversionRate >= 15 ? 'warning' : 'danger')),
        ];
    }

    protected function getWeeklyChart($query): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->startOfDay();
            $data[] = (clone $query)
                ->whereDate('created_at', $date)
                ->count();
        }
        return $data;
    }
}
