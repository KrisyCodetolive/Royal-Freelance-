<?php

namespace App\Filament\Resources\Tags\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TagsTable
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
                    ->badge()
                    ->color(fn($record) => $record->color),

                TextColumn::make('description')
                    ->label('Description')
                    ->limit(50)
                    ->searchable()
                    ->toggleable(),

                ColorColumn::make('color')
                    ->label('Couleur')
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('leads_count')
                    ->label('Leads')
                    ->counts('leads')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                IconColumn::make('is_auto')
                    ->label('Auto')
                    ->boolean()
                    ->trueIcon('heroicon-o-bolt')
                    ->falseIcon('heroicon-o-hand-raised')
                    ->trueColor('success')
                    ->falseColor('gray'),

                TextColumn::make('auto_trigger')
                    ->label('Déclencheur')
                    ->formatStateUsing(fn($state) => match($state) {
                        'video_complete' => 'Vidéo 100%',
                        'whatsapp_click' => 'WhatsApp',
                        'score_threshold_31' => 'Score ≥ 31',
                        'score_threshold_61' => 'Score ≥ 61',
                        'inactive_7_days' => 'Inactif 7j',
                        'inactive_14_days' => 'Inactif 14j',
                        'form_submit' => 'Formulaire',
                        default => '-'
                    })
                    ->toggleable(),

                TextColumn::make('sort_order')
                    ->label('Ordre')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('is_auto')
                    ->label('Type')
                    ->options([
                        1 => 'Automatiques',
                        0 => 'Manuels',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order', 'asc');
    }
}
