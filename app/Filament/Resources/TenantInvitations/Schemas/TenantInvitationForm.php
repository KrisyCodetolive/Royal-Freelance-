<?php

namespace App\Filament\Resources\TenantInvitations\Schemas;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TenantInvitationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Inviter un commercial')
                    ->description('Un lien signé et valable 7 jours sera généré. Le commercial qui l\'utilise rejoint automatiquement votre espace.')
                    ->schema([
                        TextInput::make('email')
                            ->label('Email du commercial')
                            ->email()
                            ->required()
                            ->maxLength(255),
                    ]),
            ]);
    }
}
