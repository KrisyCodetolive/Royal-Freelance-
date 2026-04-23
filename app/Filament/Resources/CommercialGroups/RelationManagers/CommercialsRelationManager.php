<?php

namespace App\Filament\Resources\CommercialGroups\RelationManagers;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Actions\DetachBulkAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Filament\Actions; // Using unified Actions namespace

class CommercialsRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    protected static ?string $title = 'Commerciaux';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                ImageColumn::make('avatar')
                    ->label('Photo')
                    ->circular()
                    ->defaultImageUrl(fn($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&color=7F9CF5&background=EBF4FF'),

                TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),

                TextColumn::make('shop_name')
                    ->label('Boutique')
                    ->placeholder('Non défini'),

                TextColumn::make('whatsapp_number')
                    ->label('WhatsApp')
                    ->placeholder('Non défini'),

                TextColumn::make('brought_leads_count')
                    ->label('Leads')
                    ->counts('broughtLeads')
                    ->badge()
                    ->color('success'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Actions\Action::make('attach')
                    ->label('Ajouter un commercial')
                    ->icon('heroicon-o-plus')
                    ->form([
                        Select::make('user_id')
                            ->label('Commercial')
                            ->options(function ($livewire) {
                                // Commercials in the same tenant not already in this group
                                $tenantId = $livewire->getOwnerRecord()->tenant_id;
                                $existingIds = $livewire->getOwnerRecord()->users->pluck('id');

                                return \App\Models\User::where('tenant_id', $tenantId)
                                    ->role('commercial')
                                    ->whereNotIn('id', $existingIds)
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->required(),
                    ])
                    ->action(function (array $data, $livewire) {
                        $livewire->getOwnerRecord()->users()->attach($data['user_id']);

                        Notification::make()
                            ->title('Commercial ajouté au groupe')
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
