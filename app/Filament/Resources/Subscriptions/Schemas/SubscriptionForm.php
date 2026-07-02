<?php

namespace App\Filament\Resources\Subscriptions\Schemas;

use App\Models\Plan;
use App\Models\Tenant;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SubscriptionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Abonnement')
                    ->description('Activation manuelle du plan d\'un tenant (pas de paiement automatisé pour le moment).')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('tenant_id')
                                ->label('Tenant')
                                ->options(fn() => Tenant::query()->pluck('name', 'id'))
                                ->searchable()
                                ->required(),

                            Select::make('plan_id')
                                ->label('Plan')
                                ->options(fn() => Plan::query()->pluck('name', 'id'))
                                ->required(),
                        ]),

                        Grid::make(2)->schema([
                            Select::make('cycle')
                                ->label('Cycle')
                                ->options([
                                    'monthly' => 'Mensuel',
                                    'yearly' => 'Annuel',
                                ])
                                ->default('monthly')
                                ->required(),

                            Select::make('status')
                                ->label('Statut')
                                ->options([
                                    'active' => 'Actif',
                                    'trialing' => 'Essai',
                                    'suspended' => 'Suspendu',
                                    'expired' => 'Expiré',
                                ])
                                ->default('active')
                                ->required(),
                        ]),

                        Grid::make(2)->schema([
                            DateTimePicker::make('starts_at')
                                ->label('Débute le')
                                ->default(now())
                                ->required(),

                            DateTimePicker::make('ends_at')
                                ->label('Expire le')
                                ->helperText('Laisser vide pour un abonnement sans expiration (ex: plan Gratuit).'),
                        ]),
                    ]),
            ]);
    }
}
