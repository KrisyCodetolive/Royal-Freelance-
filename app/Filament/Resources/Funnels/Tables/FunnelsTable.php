<?php

namespace App\Filament\Resources\Funnels\Tables;

use App\Enums\FunnelStatus;
use App\Models\Funnel;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class FunnelsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nom du Tunnel')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn($record) => $record->slug),

                TextColumn::make('offer.name')
                    ->label('Offre')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge(),

                TextColumn::make('leads_count')
                    ->label('Leads')
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('conversions_count')
                    ->label('Ventes')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->color(fn($state) => $state >= 10 ? 'success' : ($state >= 5 ? 'warning' : 'danger')),

                TextColumn::make('assignedTo.name')
                    ->label('Responsable')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('published_at')
                    ->label('Publié le')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options(FunnelStatus::class),
                SelectFilter::make('offer_id')
                    ->label('Offre')
                    ->relationship('offer', 'name'),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make()->label('Voir'),
                EditAction::make()->label('Modifier'),
                Action::make('duplicate')
                    ->label('Dupliquer')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->modalHeading('Dupliquer ce tunnel')
                    ->modalDescription('Une copie sera créée en mode brouillon.')
                    ->action(function (Funnel $record) {
                        $clone = $record->duplicate();
                        return redirect()->route('filament.admin.resources.funnels.edit', $clone);
                    }),
                Action::make('view_public')
                    ->label('Voir en ligne')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('info')
                    ->url(fn(Funnel $record) => $record->getPublicUrl())
                    ->openUrlInNewTab()
                    ->visible(fn(Funnel $record) => $record->isActive()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Supprimer'),
                    ForceDeleteBulkAction::make()->label('Supprimer définitivement'),
                    RestoreBulkAction::make()->label('Restaurer'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped();
    }
}
