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

    protected static ?int $sort = 7;

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'lg' => 6,
    ];

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::role('commercial')
                    ->withCount([
                        'broughtLeads as leads',
                        'broughtLeads as hot_leads_count' => fn($q) => $q->where('score', '>=', 60),
                        'broughtLeads as converted_leads_count' => fn($q) => $q->whereNotNull('converted_at'),
                    ])
                    ->orderByDesc('converted_leads_count')
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Commercial')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('leads_count')
                    ->label('Total Leads')
                    ->numeric()
                    ->alignCenter()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('hot_leads_count')
                    ->label('Leads HOT')
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

                Tables\Columns\TextColumn::make('conversion_rate')
                    ->label('Taux Conv.')
                    ->formatStateUsing(function ($record) {
                        $total = $record->leads_count ?? 0;
                        $converted = $record->converted_leads_count ?? 0;
                        return $total > 0 ? round(($converted / $total) * 100, 1) . '%' : '-';
                    })
                    ->alignCenter()
                    ->badge()
                    ->color(fn($state) => match (true) {
                        str_contains($state, '100') => 'success',
                        str_contains($state, '-') => 'gray',
                        (float) $state >= 30 => 'success',
                        (float) $state >= 15 => 'warning',
                        default => 'danger',
                    }),

                Tables\Columns\TextColumn::make('avg_score')
                    ->label('Score Moyen')
                    ->formatStateUsing(function ($record) {
                        $avg = $record->broughtLeads()->avg('score') ?? 0;
                        return round($avg, 1);
                    })
                    ->alignCenter()
                    ->numeric(),
            ])
            ->paginated(false)
            ->striped();
    }
}
