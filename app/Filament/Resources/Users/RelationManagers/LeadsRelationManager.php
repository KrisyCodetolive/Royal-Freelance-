<?php

namespace App\Filament\Resources\Users\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Actions; // Using unified Actions namespace


class LeadsRelationManager extends RelationManager
{
    protected static string $relationship = 'broughtLeads';

    protected static ?string $title = 'Leads apportés';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('email')
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->placeholder('Sans nom'),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('phone')
                    ->label('Téléphone')
                    ->searchable()
                    ->placeholder('-'),

                TextColumn::make('funnel.name')
                    ->label('Tunnel')
                    ->sortable(),

                TextColumn::make('score')
                    ->label('Score')
                    ->badge()
                    ->color(fn($state) => match (true) {
                        $state >= 60 => 'danger',
                        $state >= 30 => 'warning',
                        default => 'info',
                    })
                    ->formatStateUsing(fn($state) => match (true) {
                        $state >= 60 => "🔥 {$state}",
                        $state >= 30 => "☀️ {$state}",
                        default => "❄️ {$state}",
                    }),

                TextColumn::make('converted_at')
                    ->label('Converti')
                    ->date('d/m/Y')
                    ->badge()
                    ->color('success')
                    ->placeholder('Non converti'),

                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'hot' => '🔥 Chauds',
                        'warm' => '☀️ Tièdes',
                        'cold' => '❄️ Froids',
                        'converted' => '✓ Convertis',
                    ])
                    ->query(function ($query, $data) {
                        return match ($data['value']) {
                            'hot' => $query->where('score', '>=', 60),
                            'warm' => $query->where('score', '>=', 30)->where('score', '<', 60),
                            'cold' => $query->where('score', '<', 30),
                            'converted' => $query->whereNotNull('converted_at'),
                            default => $query,
                        };
                    }),

                SelectFilter::make('funnel_id')
                    ->label('Tunnel')
                    ->relationship('funnel', 'name'),
            ])
            ->headerActions([
                //
            ])
            ->actions([
                Actions\Action::make('contact_whatsapp')
                    ->label('WhatsApp')
                    ->icon('heroicon-o-phone')
                    ->color('success')
                    ->visible(fn($record) => !empty($record->phone))
                    ->url(fn($record) => 'https://wa.me/' . preg_replace('/[^0-9]/', '', $record->phone))
                    ->openUrlInNewTab(),

                Actions\Action::make('view_lead')
                    ->label('Voir')
                    ->icon('heroicon-o-eye')
                    ->url(fn($record) => route('filament.admin.resources.leads.view', $record))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                //
            ]);
    }
}
