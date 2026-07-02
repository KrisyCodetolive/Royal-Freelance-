<?php

namespace App\Filament\Resources\TenantInvitations\Tables;

use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TenantInvitationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('email')
                    ->label('Email invité')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('role')
                    ->label('Rôle')
                    ->formatStateUsing(fn(string $state) => match ($state) {
                        'admin' => 'Administrateur',
                        'commercial' => 'Commercial',
                        default => $state,
                    })
                    ->badge(),

                TextColumn::make('invite_url')
                    ->label('Lien d\'invitation')
                    ->copyable()
                    ->copyMessage('Lien copié !')
                    ->limit(40)
                    ->tooltip(fn($record) => $record->invite_url),

                TextColumn::make('invitedBy.name')
                    ->label('Invité par')
                    ->toggleable(),

                IconColumn::make('used_at')
                    ->label('Statut')
                    ->boolean()
                    ->state(fn($record) => $record->isUsed())
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon(fn($record) => $record->isExpired() ? 'heroicon-o-x-circle' : 'heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor(fn($record) => $record->isExpired() ? 'danger' : 'warning'),

                TextColumn::make('expires_at')
                    ->label('Expire le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Créée le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
