<?php

namespace App\Filament\Resources\Leads\Pages;

use App\Enums\LeadStatus;
use App\Filament\Resources\Leads\LeadResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Leads\Widgets\LeadStatsOverview;
use App\Filament\Resources\Leads\Widgets\LeadsChart;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListLeads extends ListRecords
{
    protected static string $resource = LeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            LeadStatsOverview::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Tous')
                ->icon('heroicon-o-users')
                ->badge(fn () => \App\Models\Lead::count()),

            'hot_no_contact' => Tab::make('🔥 Chauds sans contact')
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->whereIn('status', [LeadStatus::HOT, LeadStatus::ULTRA_HOT])
                    ->whereDoesntHave('events', fn ($q) => $q->where('type', \App\Enums\EventType::WHATSAPP_CLICK))
                )
                ->badge(fn () => \App\Models\Lead::whereIn('status', [LeadStatus::HOT, LeadStatus::ULTRA_HOT])
                    ->whereDoesntHave('events', fn ($q) => $q->where('type', \App\Enums\EventType::WHATSAPP_CLICK))
                    ->count()
                )
                ->badgeColor('danger'),

            'inactive_7d' => Tab::make('💤 Inactifs 7j+')
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->whereNotIn('status', [LeadStatus::CLIENT, LeadStatus::MEMBER])
                    ->where(function ($q) {
                        $q->where('last_activity_at', '<', now()->subDays(7))
                            ->orWhere(fn ($q2) => $q2->whereNull('last_activity_at')->where('created_at', '<', now()->subDays(7)));
                    })
                )
                ->badge(fn () => \App\Models\Lead::whereNotIn('status', [LeadStatus::CLIENT, LeadStatus::MEMBER])
                    ->where(function ($q) {
                        $q->where('last_activity_at', '<', now()->subDays(7))
                            ->orWhere(fn ($q2) => $q2->whereNull('last_activity_at')->where('created_at', '<', now()->subDays(7)));
                    })->count()
                )
                ->badgeColor('gray'),

            'video_engaged' => Tab::make('🎬 Engagés vidéo')
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->whereHas('events', fn ($q) => $q->whereIn('type', [
                        \App\Enums\EventType::VIDEO_75, \App\Enums\EventType::VIDEO_100,
                    ]))
                )
                ->badge(fn () => \App\Models\Lead::whereHas('events', fn ($q) => $q->whereIn('type', [
                    \App\Enums\EventType::VIDEO_75, \App\Enums\EventType::VIDEO_100,
                ]))->count())
                ->badgeColor('info'),

            'new_24h' => Tab::make('🆕 Nouveaux 24h')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('created_at', '>=', now()->subHours(24)))
                ->badge(fn () => \App\Models\Lead::where('created_at', '>=', now()->subHours(24))->count())
                ->badgeColor('success'),

            'ready_to_convert' => Tab::make('🟣 Prêts à convertir')
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->where('status', LeadStatus::ULTRA_HOT)
                    ->whereNull('converted_at')
                )
                ->badge(fn () => \App\Models\Lead::where('status', LeadStatus::ULTRA_HOT)->whereNull('converted_at')->count())
                ->badgeColor('warning'),

            'no_email' => Tab::make('📧 Sans email')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNull('email')->whereNotNull('phone'))
                ->badge(fn () => \App\Models\Lead::whereNull('email')->whereNotNull('phone')->count())
                ->badgeColor('warning'),
        ];
    }
}
