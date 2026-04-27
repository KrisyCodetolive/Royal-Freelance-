<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use Filament\Actions\Action;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestLeads extends BaseWidget
{
    protected static ?string $heading = 'Leads Prioritaires';
    protected static ?int $sort = 5;
    protected int|string|array $columnSpan = [
        'default' => 12,
        'md' => 7,
    ];

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Lead::withoutGlobalScope('tenant')
                    ->with(['funnel:id,name'])
                    ->orderByRaw("FIELD(status, 'ultra_hot', 'hot', 'warm', 'cold', 'client', 'member')")
                    ->orderByDesc('score')
                    ->limit(3)
            )
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Prospect')
                    ->state(fn($record) => $record->getFullName() ?: $record->email)
                    ->weight('bold')
                    ->description(fn($record) => $record->email)
                    ->searchable(['first_name', 'last_name', 'email']),

                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state->label())
                    ->color(fn($state) => match ($state->value) {
                        'ultra_hot' => 'danger',
                        'hot'       => 'warning',
                        'warm'      => 'info',
                        'client'    => 'success',
                        default     => 'gray',
                    }),

                Tables\Columns\TextColumn::make('score')
                    ->label('Score')
                    ->numeric()
                    ->alignCenter()
                    ->badge()
                    ->color(fn($state) => match (true) {
                        $state >= 61 => 'danger',
                        $state >= 31 => 'warning',
                        $state >= 11 => 'info',
                        default      => 'gray',
                    }),

                Tables\Columns\TextColumn::make('funnel.name')
                    ->label('Tunnel')
                    ->badge()
                    ->color('primary')
                    ->limit(15),

                Tables\Columns\TextColumn::make('last_activity_at')
                    ->label('Activité')
                    ->since()
                    ->placeholder('—'),
            ])
            ->recordActions([
                Action::make('view')
                    ->icon('heroicon-o-eye')
                    ->url(fn($record) => route('filament.admin.resources.leads.view', $record))
                    ->color('gray')
                    ->iconButton(),
            ])
            ->paginated(false)
            ->striped();
    }
}
