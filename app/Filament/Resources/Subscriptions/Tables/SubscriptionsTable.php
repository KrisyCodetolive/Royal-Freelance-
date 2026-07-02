<?php

namespace App\Filament\Resources\Subscriptions\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SubscriptionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label('Tenant')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('plan.name')
                    ->label('Plan')
                    ->badge()
                    ->color(fn($record) => match ($record->plan?->slug) {
                        'prestige' => 'warning',
                        'starter' => 'info',
                        default => 'gray',
                    }),

                TextColumn::make('cycle')
                    ->label('Cycle')
                    ->formatStateUsing(fn($state) => $state === 'yearly' ? 'Annuel' : 'Mensuel'),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'active' => 'success',
                        'trialing' => 'info',
                        'suspended' => 'warning',
                        'expired' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('starts_at')
                    ->label('Débute le')
                    ->dateTime('d/m/Y')
                    ->sortable(),

                TextColumn::make('ends_at')
                    ->label('Expire le')
                    ->dateTime('d/m/Y')
                    ->placeholder('Jamais')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'active' => 'Actif',
                        'trialing' => 'Essai',
                        'suspended' => 'Suspendu',
                        'expired' => 'Expiré',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
