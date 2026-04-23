<?php

namespace App\Filament\Widgets;

use App\Enums\LeadStatus;
use App\Models\Funnel;
use App\Models\Lead;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $tenant = Auth::user()->tenant;

        if (!$tenant) {
            return [];
        }

        $totalLeads = Lead::count();
        $hotLeads = Lead::hot()->count();
        $conversions = Lead::converted()->count();
        $conversionRate = $totalLeads > 0 ? round(($conversions / $totalLeads) * 100, 1) : 0;
        $activeFunnels = Funnel::active()->count();
        $commercials = User::role('commercial')->count();

        return [
            Stat::make('Total Leads', number_format($totalLeads))
                ->description('Prospects capturés')
                ->descriptionIcon('heroicon-o-users')
                ->color('primary')
                ->chart([7, 12, 18, 24, 21, 28, $totalLeads]),
            Stat::make('Leads HOT 🔥', number_format($hotLeads))
                ->description('Prêts à convertir')
                ->descriptionIcon('heroicon-o-fire')
                ->color('warning')
                ->chart([2, 4, 3, 8, 6, 10, $hotLeads]),
            Stat::make('Conversions', number_format($conversions))
                ->description("{$conversionRate}% taux de conversion")
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success')
                ->chart([1, 2, 4, 3, 6, 5, $conversions]),
            Stat::make('Tunnels Actifs', $activeFunnels)
                ->description('En production')
                ->descriptionIcon('heroicon-o-funnel')
                ->color('info'),
            Stat::make('Emails Envoyés', number_format(\App\Models\EmailSequenceEmailSend::count()))
                ->description("Séquences automatiques")
                ->descriptionIcon('heroicon-o-envelope')
                ->color('primary'),
        ];
    }
}
