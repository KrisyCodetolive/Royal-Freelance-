<?php

namespace App\Filament\Resources\EmailSequences\Schemas;

use App\Enums\EmailSequenceStatus;
use App\Enums\EmailSequenceTrigger;
use App\Enums\LeadStatus;
use App\Models\Funnel;
use App\Models\Page;
use App\Models\Tag;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;

class EmailSequenceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make('Informations')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nom de la séquence')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Ex: Relance leads inactifs')
                                    ->columnSpanFull(),

                                Textarea::make('description')
                                    ->label('Description')
                                    ->rows(3)
                                    ->maxLength(1000)
                                    ->placeholder('Décrivez l\'objectif de cette séquence...')
                                    ->columnSpanFull(),

                                Grid::make(2)->schema([
                                    Select::make('status')
                                        ->label('Statut')
                                        ->options(EmailSequenceStatus::class)
                                        ->default(EmailSequenceStatus::DRAFT)
                                        ->required()
                                        ->helperText('Brouillon = visible mais non active'),

                                    Select::make('funnel_id')
                                        ->label('Tunnel (optionnel)')
                                        ->relationship('funnel', 'name')
                                        ->searchable()
                                        ->preload()
                                        ->helperText('Lier à un tunnel spécifique'),
                                ]),
                            ]),

                        Tabs\Tab::make('Déclenchement')
                            ->icon('heroicon-o-bolt')
                            ->schema([
                                Select::make('trigger')
                                    ->label('Déclencheur')
                                    ->options(EmailSequenceTrigger::class)
                                    ->required()
                                    ->live()
                                    ->helperText('Choisissez quand démarrer la séquence'),

                                TextInput::make('trigger_conditions.min_score')
                                    ->label('Score minimum')
                                    ->numeric()
                                    ->default(10)
                                    ->minValue(1)
                                    ->maxValue(100)
                                    ->visible(fn(Get $get) => $get('trigger') === EmailSequenceTrigger::SCORE_THRESHOLD->value)
                                    ->helperText('Score à atteindre pour déclencher'),

                                Select::make('trigger_conditions.page_id')
                                    ->label('Page spécifique')
                                    ->options(fn() => Page::pluck('title', 'id'))
                                    ->searchable()
                                    ->visible(fn(Get $get) => $get('trigger') === EmailSequenceTrigger::PAGE_VIEW->value)
                                    ->helperText('Page à visiter pour déclencher'),

                                Select::make('trigger_conditions.tag_id')
                                    ->label('Tag spécifique')
                                    ->options(fn() => Tag::pluck('name', 'id'))
                                    ->searchable()
                                    ->visible(fn(Get $get) => $get('trigger') === EmailSequenceTrigger::TAG_ASSIGNED->value)
                                    ->helperText('Tag à assigner pour déclencher'),

                                TextInput::make('trigger_conditions.days')
                                    ->label('Nombre de jours d\'inactivité')
                                    ->numeric()
                                    ->default(7)
                                    ->minValue(1)
                                    ->maxValue(365)
                                    ->visible(fn(Get $get) => $get('trigger') === EmailSequenceTrigger::INACTIVITY->value)
                                    ->helperText('Jours d\'inactivité avant déclenchement'),

                                Select::make('trigger_conditions.status')
                                    ->label('Statut du lead')
                                    ->options([
                                        'cold'      => '❄️ Froid',
                                        'warm'      => '☀️ Tiède',
                                        'hot'       => '🔥 Chaud',
                                        'ultra_hot' => '⚡ Ultra Chaud',
                                        'client'    => '✅ Client',
                                        'member'    => '👤 Membre',
                                    ])
                                    ->visible(fn(Get $get) => $get('trigger') === EmailSequenceTrigger::STATUS_CHANGED->value)
                                    ->helperText('Email envoyé dès que le lead atteint ce statut')
                                    ->required(fn(Get $get) => $get('trigger') === EmailSequenceTrigger::STATUS_CHANGED->value),
                            ]),

                        Tabs\Tab::make('Paramètres')
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                Toggle::make('settings.send_on_weekends')
                                    ->label('Envoyer le week-end')
                                    ->default(false)
                                    ->helperText('Autoriser l\'envoi samedi et dimanche')
                                    ->inline(false),

                                Toggle::make('settings.send_at_optimal_time')
                                    ->label('Heure optimale')
                                    ->default(true)
                                    ->helperText('Envoyer à l\'heure optimale du lead')
                                    ->inline(false),

                                Toggle::make('settings.stop_on_reply')
                                    ->label('Arrêter si réponse')
                                    ->default(true)
                                    ->helperText('Stopper si le lead répond')
                                    ->inline(false),

                                Toggle::make('settings.stop_on_conversion')
                                    ->label('Arrêter si conversion')
                                    ->default(true)
                                    ->helperText('Stopper si le lead convertit')
                                    ->inline(false),
                            ])->columns(2),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
