<?php

namespace App\Filament\Resources\TenantInvitations\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TenantInvitationForm
{
    private const ROLE_LABELS = [
        'admin' => 'Administrateur — gestion opérationnelle',
        'editor' => 'Editor — édite les tunnels qui lui sont assignés/partagés',
        'viewer' => 'Viewer — consultation seule des tunnels partagés',
        'commercial' => 'Commercial — espace dédié',
    ];

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
                            ->options(fn () => static::roleOptions())
                            ->default('commercial')
                            ->required(),
                    ]),
            ]);
    }

    /**
     * Rôles workspace (Admin/Editor/Viewer) filtrés par `Plan::available_roles`
     * du tenant courant. "commercial" reste toujours disponible : le plan
     * Gratuit bloque déjà toute invitation en amont (TenantInvitationResource
     * ::canCreate()), donc cette méthode n'est évaluée que pour un plan payant.
     */
    public static function roleOptions(): array
    {
        $allowed = auth()->user()?->tenant?->currentPlan()?->available_roles ?? [];

        return collect(self::ROLE_LABELS)
            ->filter(fn (string $label, string $role) => $role === 'commercial' || in_array($role, $allowed, true))
            ->all();
    }
}
