<?php

namespace App\Filament\Resources\TenantInvitations\Tables;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
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

                // Bouton copier avec repli execCommand (voir la vue) : le sanitizer
                // HTML de Filament supprime onclick/<script> d'un ->html() classique,
                // d'où l'usage d'une ViewColumn (template de confiance) plutôt que
                // formatStateUsing()->html().
                ViewColumn::make('invite_url')
                    ->label('Lien d\'invitation')
                    ->view('filament.tables.columns.invite-link'),

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
                // Fallback au clic-copier : navigator.clipboard peut échouer
                // silencieusement (contexte non sécurisé, permission refusée).
                // Ce champ en lecture seule reste sélectionnable/copiable à la main.
                Action::make('show_link')
                    ->label('Voir le lien')
                    ->icon('heroicon-o-link')
                    ->color('gray')
                    ->modalHeading('Lien d\'invitation')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Fermer')
                    ->schema(fn($record) => [
                        TextInput::make('invite_url')
                            ->label('Lien complet (cliquez pour tout sélectionner)')
                            ->default($record->invite_url)
                            ->readOnly()
                            ->extraInputAttributes(['onclick' => 'this.select()']),
                    ]),
                DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
