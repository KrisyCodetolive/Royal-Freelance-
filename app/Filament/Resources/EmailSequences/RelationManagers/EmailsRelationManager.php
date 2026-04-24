<?php

namespace App\Filament\Resources\EmailSequences\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class EmailsRelationManager extends RelationManager
{
    protected static string $relationship = 'emails';

    protected static ?string $title = 'Emails de la Séquence';

    protected static ?string $recordTitleAttribute = 'subject';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Contenu de l\'Email')
                    ->schema([
                        Forms\Components\TextInput::make('subject')
                            ->label('Sujet')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ex: {first_name}, avez-vous vu notre offre ?')
                            ->helperText('Variables: {first_name}, {last_name}, {funnel_name}')
                            ->columnSpanFull(),

                        Forms\Components\RichEditor::make('content')
                            ->label('Contenu')
                            ->required()
                            ->placeholder('Écrivez le contenu de votre email...')
                            ->helperText('Variables: {first_name}, {last_name}, {full_name}, {email}, {score}, {funnel_name}')
                            ->columnSpanFull()
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'link',
                                'bulletList',
                                'orderedList',
                                'h2',
                                'h3',
                            ]),
                    ]),

                Section::make('Paramètres d\'Envoi')
                    ->schema([
                        Grid::make(2)->schema([
                            Forms\Components\TextInput::make('send_after_hours')
                                ->label('Délai d\'envoi (heures)')
                                ->numeric()
                                ->default(0)
                                ->minValue(0)
                                ->required()
                                ->helperText('0 = immédiat, 24 = 1 jour, 168 = 7 jours')
                                ->suffix('heures'),

                            Forms\Components\Toggle::make('is_active')
                                ->label('Email actif')
                                ->default(true)
                                ->helperText('Désactivez pour mettre en pause cet email'),
                        ]),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->reorderable('send_after_hours')
            ->columns([
                Tables\Columns\TextColumn::make('send_after_hours')
                    ->label('Délai')
                    ->formatStateUsing(fn($state) => match(true) {
                        $state === 0 => '⚡ Immédiat',
                        $state < 24 => $state . 'h',
                        $state < 168 => round($state / 24, 1) . 'j',
                        default => round($state / 168, 1) . ' sem.'
                    })
                    ->badge()
                    ->color(fn($state) => match(true) {
                        $state === 0 => 'danger',
                        $state < 24 => 'warning',
                        $state < 168 => 'info',
                        default => 'success'
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('subject')
                    ->label('Sujet')
                    ->searchable()
                    ->limit(50)
                    ->weight('bold'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('stats')
                    ->label('Envoyés')
                    ->formatStateUsing(fn($state) => $state['sent'] ?? 0)
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('stats')
                    ->label('Ouvertures')
                    ->formatStateUsing(function($record) {
                        $sent = $record->stats['sent'] ?? 0;
                        $opened = $record->stats['opened'] ?? 0;
                        return $sent > 0 ? round(($opened / $sent) * 100, 1) . '%' : '-';
                    })
                    ->color('success')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('stats')
                    ->label('Clics')
                    ->formatStateUsing(function($record) {
                        $sent = $record->stats['sent'] ?? 0;
                        $clicked = $record->stats['clicked'] ?? 0;
                        return $sent > 0 ? round(($clicked / $sent) * 100, 1) . '%' : '-';
                    })
                    ->color('warning')
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Actif')
                    ->placeholder('Tous')
                    ->trueLabel('Actifs uniquement')
                    ->falseLabel('Inactifs uniquement'),
            ])
            ->headerActions([
                CreateAction::make()->label('Ajouter un email'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            
            ->emptyStateHeading('Aucun email dans cette séquence')
            ->emptyStateDescription('Ajoutez votre premier email pour commencer')
            ->emptyStateIcon('heroicon-o-envelope')
            ->defaultSort('send_after_hours', 'asc');
    }
}
