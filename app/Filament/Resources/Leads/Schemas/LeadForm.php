<?php

namespace App\Filament\Resources\Leads\Schemas;

use App\Enums\LeadStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;

class LeadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Tabs::make('Lead')
                    ->columnSpanFull()
                    ->tabs([
                        Tabs\Tab::make('Contact')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Section::make('Informations de contact')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('first_name')
                                            ->label('Prénom')
                                            ->maxLength(255),
                                        TextInput::make('last_name')
                                            ->label('Nom')
                                            ->maxLength(255),
                                        TextInput::make('email')
                                            ->label('Email')
                                            ->email()
                                            ->maxLength(255),
                                        TextInput::make('phone')
                                            ->label('Téléphone')
                                            ->tel()
                                            ->maxLength(255),
                                    ]),
                                Section::make('Assignation')
                                    ->columns(2)
                                    ->schema([
                                        Select::make('funnel_id')
                                            ->label('Tunnel')
                                            ->relationship('funnel', 'name')
                                            ->required()
                                            ->searchable()
                                            ->preload(),
                                        Select::make('assigned_to')
                                            ->label('Assigné à')
                                            ->relationship('assignedTo', 'name')
                                            ->searchable()
                                            ->preload(),
                                        Select::make('status')
                                            ->label('Statut')
                                            ->options(LeadStatus::class)
                                            ->default(LeadStatus::COLD)
                                            ->required(),
                                        TextInput::make('score')
                                            ->label('Score')
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0)
                                            ->maxValue(100)
                                            ->helperText('Modifier le score met à jour le statut automatiquement'),
                                    ]),
                            ]),

                        Tabs\Tab::make('Tracking')
                            ->icon('heroicon-o-chart-bar')
                            ->schema([
                                Section::make('Source')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('source')
                                            ->label('Source (utm_source)'),
                                        TextInput::make('medium')
                                            ->label('Medium (utm_medium)'),
                                        TextInput::make('campaign')
                                            ->label('Campagne (utm_campaign)'),
                                        TextInput::make('referrer')
                                            ->label('Référent')
                                            ->columnSpanFull(),
                                    ]),
                                Section::make('Localisation')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('country')
                                            ->label('Pays'),
                                        TextInput::make('city')
                                            ->label('Ville'),
                                        TextInput::make('ip_address')
                                            ->label('Adresse IP')
                                            ->disabled(),
                                    ]),
                            ]),

                        Tabs\Tab::make('Activité')
                            ->icon('heroicon-o-clock')
                            ->schema([
                                Section::make('Historique')
                                    ->columns(2)
                                    ->schema([
                                        DateTimePicker::make('last_activity_at')
                                            ->label('Dernière activité')
                                            ->disabled(),
                                        DateTimePicker::make('converted_at')
                                            ->label('Date de conversion'),
                                        DateTimePicker::make('created_at')
                                            ->label('Créé le')
                                            ->disabled(),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
