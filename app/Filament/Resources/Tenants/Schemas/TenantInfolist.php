<?php

namespace App\Filament\Resources\Tenants\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TenantInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Entreprise')
                    ->icon('heroicon-o-building-office-2')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Nom')
                                    ->weight('bold'),
                                TextEntry::make('email')
                                    ->label('Email')
                                    ->copyable()
                                    ->icon('heroicon-o-envelope')
                                    ->placeholder('Non renseigné')
                                    ->url(fn($record) => $record->email ? "mailto:{$record->email}" : null),
                                TextEntry::make('phone')
                                    ->label('Téléphone')
                                    ->copyable()
                                    ->placeholder('Non renseigné'),
                                TextEntry::make('domain')
                                    ->label('Domaine personnalisé')
                                    ->placeholder('Aucun'),
                                TextEntry::make('slug')
                                    ->label('Identifiant (slug)'),
                                TextEntry::make('created_at')
                                    ->label('Inscrit le')
                                    ->dateTime('d/m/Y à H:i'),
                            ]),
                    ]),

                Section::make('Propriétaire (Owner)')
                    ->description('Le contact principal du workspace — mot de passe non affiché, non récupérable.')
                    ->icon('heroicon-o-user-circle')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('owner_name')
                                    ->label('Nom')
                                    ->state(fn($record) => $record->ownerUser()?->name ?? 'Aucun owner identifié'),
                                TextEntry::make('owner_email')
                                    ->label('Email')
                                    ->copyable()
                                    ->icon('heroicon-o-envelope')
                                    ->state(fn($record) => $record->ownerUser()?->email)
                                    ->placeholder('—')
                                    ->url(fn($record) => ($email = $record->ownerUser()?->email) ? "mailto:{$email}" : null),
                                TextEntry::make('owner_phone')
                                    ->label('Téléphone')
                                    ->copyable()
                                    ->state(fn($record) => $record->ownerUser()?->phone)
                                    ->placeholder('Non renseigné'),
                            ]),
                    ]),

                Section::make('Abonnement')
                    ->icon('heroicon-o-credit-card')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('plan')
                                    ->label('Plan actuel')
                                    ->badge()
                                    ->state(fn($record) => $record->currentPlan()?->name ?? 'Aucun'),
                                TextEntry::make('subscription_status')
                                    ->label('Statut')
                                    ->badge()
                                    ->state(fn($record) => $record->activeSubscription()?->status ?? 'Aucun abonnement actif'),
                                TextEntry::make('subscription_ends_at')
                                    ->label('Expire le')
                                    ->state(fn($record) => $record->activeSubscription()?->ends_at?->format('d/m/Y'))
                                    ->placeholder('Jamais'),
                            ]),
                    ]),

                Section::make('Activité')
                    ->icon('heroicon-o-clock')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('users_count')
                                    ->label('Membres')
                                    ->state(fn($record) => $record->users()->count()),
                                IconEntry::make('is_active')
                                    ->label('Actif')
                                    ->boolean(),
                                IconEntry::make('suspended')
                                    ->label('Suspendu')
                                    ->boolean()
                                    ->state(fn($record) => $record->isSuspended())
                                    ->trueColor('danger')
                                    ->falseColor('gray'),
                            ]),
                    ]),
            ]);
    }
}
