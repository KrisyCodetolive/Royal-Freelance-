<?php

namespace App\Filament\Resources\CommercialGroups\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CommercialGroupForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Groupe Commercial')
                    ->columnSpanFull()
                    ->tabs([
                        Tabs\Tab::make('Informations')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Section::make('Informations du groupe')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Nom du groupe')
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function ($state, callable $set, $get) {
                                                // Générer le slug automatiquement seulement si vide ou si c'est une création
                                                if (empty($get('slug')) || $get('slug') === Str::slug($get('name') ?? '')) {
                                                    $set('slug', Str::slug($state));
                                                }
                                            }),

                                        TextInput::make('slug')
                                            ->label('Identifiant unique')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(255)
                                            ->prefix('ID:')
                                            ->helperText('Généré automatiquement à partir du nom')
                                            ->dehydrateStateUsing(fn($state) => $state ?: Str::slug(Str::random(8))),

                                        Textarea::make('description')
                                            ->label('Description')
                                            ->rows(2)
                                            ->columnSpanFull()
                                            ->placeholder('Décrivez le groupe et son objectif...'),

                                        Toggle::make('is_active')
                                            ->label('Groupe actif')
                                            ->helperText('Les commerciaux inactifs ne peuvent pas accéder aux tunnels')
                                            ->default(true),
                                    ]),
                            ]),

                        Tabs\Tab::make('Commerciaux')
                            ->icon('heroicon-o-users')
                            ->badge(fn($get) => count($get('users') ?? []) ?: null)
                            ->schema([
                                Section::make('Membres du groupe')
                                    ->description('Sélectionnez les commerciaux qui feront partie de ce groupe.')
                                    ->schema([
                                        Select::make('users')
                                            ->label('Commerciaux')
                                            ->relationship('users', 'name')
                                            ->multiple()
                                            ->preload()
                                            ->searchable()
                                            ->columnSpanFull()
                                            ->helperText('Les commerciaux sélectionnés auront accès aux tunnels assignés à ce groupe.'),
                                    ]),
                            ]),

                        Tabs\Tab::make('Tunnels')
                            ->icon('heroicon-o-funnel')
                            ->badge(fn($get) => count($get('funnels') ?? []) ?: null)
                            ->schema([
                                Section::make('Tunnels assignés')
                                    ->description('Les commerciaux de ce groupe pourront utiliser ces tunnels.')
                                    ->schema([
                                        Select::make('funnels')
                                            ->label('Tunnels disponibles')
                                            ->relationship('funnels', 'name')
                                            ->multiple()
                                            ->preload()
                                            ->searchable()
                                            ->columnSpanFull()
                                            ->helperText('Sélectionnez les tunnels que les commerciaux de ce groupe peuvent utiliser et personnaliser.'),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
