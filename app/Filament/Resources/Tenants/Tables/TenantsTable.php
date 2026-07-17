<?php

namespace App\Filament\Resources\Tenants\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class TenantsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Entreprise')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-o-envelope')
                    ->placeholder('Non renseigné'),

                TextColumn::make('phone')
                    ->label('Téléphone')
                    ->copyable()
                    ->placeholder('Non renseigné')
                    ->toggleable(),

                TextColumn::make('owner')
                    ->label('Propriétaire (Owner)')
                    ->state(fn($record) => $record->ownerUser()?->name ?? '—')
                    ->description(fn($record) => $record->ownerUser()?->email),

                TextColumn::make('plan')
                    ->label('Plan')
                    ->badge()
                    ->state(fn($record) => $record->currentPlan()?->name ?? 'Aucun')
                    ->color(fn($record) => match ($record->currentPlan()?->slug) {
                        'prestige' => 'warning',
                        'starter' => 'info',
                        default => 'gray',
                    }),

                TextColumn::make('users_count')
                    ->label('Membres')
                    ->counts('users')
                    ->alignCenter(),

                IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean()
                    ->sortable(),

                IconColumn::make('suspended_at')
                    ->label('Suspendu')
                    ->boolean()
                    ->state(fn($record) => $record->isSuspended())
                    ->trueColor('danger')
                    ->falseColor('gray'),

                TextColumn::make('created_at')
                    ->label('Inscrit le')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Actif'),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
