<?php

namespace App\Filament\Resources\Users\RelationManagers;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;


class FunnelsRelationManager extends RelationManager
{
    protected static string $relationship = 'usableFunnels';

    protected static ?string $title = 'Tunnels utilisables';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Tunnel')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge(),

                TextColumn::make('pivot.leads_count')
                    ->label('Leads')
                    ->numeric()
                    ->badge()
                    ->color('success'),

                TextColumn::make('pivot.views_count')
                    ->label('Vues')
                    ->numeric(),

                TextColumn::make('pivot.is_active')
                    ->label('Actif')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state ? 'Oui' : 'Non')
                    ->color(fn($state) => $state ? 'success' : 'danger'),

                TextColumn::make('pivot.shop_url')
                    ->label('URL Boutique')
                    ->url(fn($state) => $state)
                    ->openUrlInNewTab()
                    ->limit(30)
                    ->placeholder('Non configuré'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->actions([
                Actions\Action::make('toggle_active')
                    ->label(fn($record) => $record->pivot->is_active ? 'Désactiver' : 'Activer')
                    ->icon(fn($record) => $record->pivot->is_active ? 'heroicon-o-pause' : 'heroicon-o-play')
                    ->color(fn($record) => $record->pivot->is_active ? 'warning' : 'success')
                    ->action(function ($record, $livewire) {
                        $livewire->getOwnerRecord()->usableFunnels()->updateExistingPivot($record->id, [
                            'is_active' => !$record->pivot->is_active,
                        ]);

                        Notification::make()
                            ->title($record->pivot->is_active ? 'Tunnel désactivé' : 'Tunnel activé')
                            ->success()
                            ->send();
                    }),

                Actions\Action::make('view_funnel')
                    ->label('Voir')
                    ->icon('heroicon-o-eye')
                    ->url(fn($record) => route('filament.admin.resources.funnels.view', $record))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Actions\BulkAction::make('activate')
                    ->label('Activer')
                    ->icon('heroicon-o-play')
                    ->action(function (Collection $records, $livewire) {
                        foreach ($records as $record) {
                            $livewire->getOwnerRecord()->usableFunnels()->updateExistingPivot($record->id, [
                                'is_active' => true,
                            ]);
                        }
                    }),
            ]);
    }
}
