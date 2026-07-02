<?php

namespace App\Filament\Resources\TenantInvitations\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TenantInvitationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Inviter un membre')
                    ->description('Un lien signé et valable 7 jours sera généré. La personne qui l\'utilise rejoint automatiquement votre espace avec le rôle choisi.')
                    ->schema([
                        TextInput::make('email')
                            ->label('Email de la personne invitée')
                            ->email()
                            ->required()
                            ->maxLength(255),

                        Select::make('role')
                            ->label('Rôle')
                            ->options([
                                'admin' => 'Administrateur — gestion opérationnelle',
                                'commercial' => 'Commercial — espace dédié',
                            ])
                            ->default('commercial')
                            ->required(),
                    ]),
            ]);
    }
}
