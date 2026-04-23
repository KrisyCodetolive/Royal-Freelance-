<?php

namespace App\Filament\Resources\CommercialGroups\RelationManagers;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Actions\DetachBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions; // Using unified Actions namespace


class FunnelsRelationManager extends RelationManager
{
    protected static string $relationship = 'funnels';

    protected static ?string $title = 'Tunnels Assignés';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Nom du tunnel')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge(),

                TextColumn::make('pages_count')
                    ->label('Pages')
                    ->counts('pages')
                    ->alignCenter(),

                IconColumn::make('pivot.can_customize')
                    ->label('Personnalisable')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),

                TextColumn::make('template_uses_count')
                    ->label('Utilisations')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Actions\Action::make('attach')
                    ->label('Assigner un tunnel')
                    ->icon('heroicon-o-plus')
                    ->form([
                        Select::make('funnel_id')
                            ->label('Tunnel')
                            ->options(function ($livewire) {
                                $tenantId = $livewire->getOwnerRecord()->tenant_id;
                                $existingIds = $livewire->getOwnerRecord()->funnels->pluck('id');

                                return \App\Models\Funnel::where('tenant_id', $tenantId)
                                    ->where('is_template', false)
                                    ->active()
                                    ->whereNotIn('id', $existingIds)
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->required(),
                        Checkbox::make('can_customize')
                            ->label('Permettre la personnalisation')
                            ->helperText('Les commerciaux pourront personnaliser le branding de ce tunnel'),
                    ])
                    ->action(function (array $data, $livewire) {
                        $livewire->getOwnerRecord()->funnels()->attach($data['funnel_id'], [
                            'can_customize' => $data['can_customize'] ?? false,
                        ]);

                        Notification::make()
                            ->title('Tunnel assigné au groupe')
                            ->success()
                            ->send();
                    }),
            ])
            ->actions([
                Actions\DetachAction::make()
                    ->label('Retirer')
                    ->icon('heroicon-o-x-mark'),
            ])
            ->bulkActions([
                Actions\DetachBulkAction::make()
                    ->label('Retirer la sélection'),
            ]);
    }
}
