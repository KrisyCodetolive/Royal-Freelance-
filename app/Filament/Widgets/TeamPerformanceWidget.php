<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Lead;
use App\Enums\LeadStatus;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\DB;

class TeamPerformanceWidget extends BaseWidget
{
    protected static ?string $heading = 'Performance Équipe Commerciale';

    protected static ?int $sort = 9;

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'lg' => 6,
    ];

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::role('commercial')
                    ->where('tenant_id', auth()->user()?->tenant_id)
                    ->withCount([
                        'broughtLeads as leads_count',
                        'broughtLeads as hot_leads_count'       => fn($q) => $q->where('score', '>=', 31),
                        'broughtLeads as converted_leads_count' => fn($q) => $q->whereNotNull('converted_at'),
                    ])
                    ->withAvg('broughtLeads as avg_score', 'score')
                    ->orderByDesc('leads_count')
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Commercial')
                    ->searchable()
                    ->weight('bold')
                    ->description(fn($record) => $record->shop_name),

                Tables\Columns\TextColumn::make('leads_count')
                    ->label('Total Leads')
                    ->numeric()
                    ->alignCenter()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('hot_leads_count')
                    ->label('Leads Chauds')
                    ->numeric()
                    ->alignCenter()
                    ->badge()
                    ->color('warning'),

                Tables\Columns\TextColumn::make('converted_leads_count')
                    ->label('Conversions')
                    ->numeric()
                    ->alignCenter()
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('taux_conversion')
                    ->label('Taux Conv.')
                    ->state(function ($record) {
                        $total = $record->leads_count ?? 0;
                        $converted = $record->converted_leads_count ?? 0;
                        return $total > 0 ? round($converted / $total * 100, 1) . '%' : '—';
                    })
                    ->alignCenter()
                    ->badge()
                    ->color(fn($state) => match (true) {
                        $state === '—'                    => 'gray',
                        (float) $state >= 30             => 'success',
                        (float) $state >= 10             => 'warning',
                        default                          => 'danger',
                    }),

                Tables\Columns\TextColumn::make('avg_score')
                    ->label('Score Moyen')
                    ->state(fn($record) => round($record->avg_score ?? 0, 1))
                    ->alignCenter()
                    ->badge()
                    ->color(fn($state) => match (true) {
                        $state >= 61 => 'danger',
                        $state >= 31 => 'warning',
                        default      => 'gray',
                    }),
            ])
            ->paginated(false)
            ->striped();
    }
}
