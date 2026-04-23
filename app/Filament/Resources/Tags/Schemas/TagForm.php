<?php

namespace App\Filament\Resources\Tags\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\ColorPicker;
use Filament\Schemas\Schema;

class TagForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations de base')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('Nom du tag')
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn($state, callable $set) => 
                                    $set('slug', \Illuminate\Support\Str::slug($state))
                                ),
                            
                            TextInput::make('slug')
                                ->label('Slug')
                                ->required()
                                ->disabled()
                                ->dehydrated()
                                ->helperText('Généré automatiquement depuis le nom'),
                        ]),

                        Grid::make(2)->schema([
                            ColorPicker::make('color')
                                ->label('Couleur du badge')
                                ->required()
                                ->default('gray'),
                            
                            TextInput::make('sort_order')
                                ->label('Ordre d\'affichage')
                                ->numeric()
                                ->default(0)
                                ->helperText('Ordre croissant'),
                        ]),
                        
                        Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Section::make('Configuration automatisation')
                    ->description('Pour les tags assignés automatiquement')
                    ->schema([
                        Toggle::make('is_auto')
                            ->label('Tag automatique')
                            ->helperText('Si activé, ce tag sera assigné automatiquement selon le trigger')
                            ->live(),
                        
                        Select::make('auto_trigger')
                            ->label('Déclencheur')
                            ->options([
                                'video_complete' => 'Vidéo vue à 100%',
                                'whatsapp_click' => 'Clic WhatsApp',
                                'score_threshold_31' => 'Score ≥ 31 (HOT)',
                                'score_threshold_61' => 'Score ≥ 61 (ULTRA HOT)',
                                'inactive_7_days' => 'Inactif depuis 7 jours',
                                'inactive_14_days' => 'Inactif depuis 14 jours',
                                'form_submit' => 'Soumission formulaire',
                            ])
                            ->visible(fn($get) => $get('is_auto'))
                            ->helperText('Choisir quand le tag doit être assigné'),
                    ])
                    ->collapsed(),
            ]);
    }
}
