<?php

namespace App\Filament\Resources\EmailSequences\Tables;

use App\Enums\EmailSequenceStatus;
use App\Enums\EmailSequenceTrigger;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EmailSequencesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn($record) => $record->description ? \Illuminate\Support\Str::limit($record->description, 50) : null),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->sortable(),

                TextColumn::make('trigger')
                    ->label('Déclencheur')
                    ->badge()
                    ->icon(fn($state) => $state?->icon())
                    ->sortable(),

                TextColumn::make('emails_count')
                    ->label('Emails')
                    ->counts('emails')
                    ->badge()
                    ->color('info')
                    ->suffix(' emails')
                    ->sortable(),

                TextColumn::make('subscriptions_count')
                    ->label('Abonnés')
                    ->counts('subscriptions')
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                TextColumn::make('stats')
                    ->label('Envoyés')
                    ->formatStateUsing(fn($state) => $state['sent'] ?? 0)
                    ->badge()
                    ->color('success')
                    ->toggleable(),

                TextColumn::make('stats')
                    ->label('Taux ouverture')
                    ->formatStateUsing(function($record) {
                        $sent = $record->stats['sent'] ?? 0;
                        $opened = $record->stats['opened'] ?? 0;
                        return $sent > 0 ? round(($opened / $sent) * 100, 1) . '%' : '-';
                    })
                    ->color(fn($record) => $record->getOpenRate() >= 20 ? 'success' : ($record->getOpenRate() >= 10 ? 'warning' : 'danger'))
                    ->toggleable(),

                TextColumn::make('stats')
                    ->label('Taux clic')
                    ->formatStateUsing(function($record) {
                        $sent = $record->stats['sent'] ?? 0;
                        $clicked = $record->stats['clicked'] ?? 0;
                        return $sent > 0 ? round(($clicked / $sent) * 100, 1) . '%' : '-';
                    })
                    ->color(fn($record) => $record->getClickRate() >= 5 ? 'success' : ($record->getClickRate() >= 2 ? 'warning' : 'danger'))
                    ->toggleable(),

                TextColumn::make('funnel.name')
                    ->label('Tunnel')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Créée le')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options(EmailSequenceStatus::class)
                    ->multiple(),

                SelectFilter::make('trigger')
                    ->label('Déclencheur')
                    ->options(EmailSequenceTrigger::class)
                    ->multiple(),

                SelectFilter::make('funnel_id')
                    ->label('Tunnel')
                    ->relationship('funnel', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                Action::make('toggle_status')
                    ->label(fn($record) => $record->status === EmailSequenceStatus::ACTIVE ? 'Mettre en pause' : 'Activer')
                    ->icon(fn($record) => $record->status === EmailSequenceStatus::ACTIVE ? 'heroicon-o-pause' : 'heroicon-o-play')
                    ->color(fn($record) => $record->status === EmailSequenceStatus::ACTIVE ? 'warning' : 'success')
                    ->action(function($record) {
                        if ($record->status === EmailSequenceStatus::ACTIVE) {
                            $record->update(['status' => EmailSequenceStatus::PAUSED]);
                        } else {
                            $record->update(['status' => EmailSequenceStatus::ACTIVE]);
                        }
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Changer le statut')
                    ->modalDescription(fn($record) => $record->status === EmailSequenceStatus::ACTIVE 
                        ? 'Cette séquence sera mise en pause et n\'enverra plus d\'emails automatiquement.' 
                        : 'Cette séquence sera activée et reprendra l\'envoi d\'emails selon les conditions.'),

                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped();
    }
}
