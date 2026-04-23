<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\ColorEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Tabs::make('Utilisateur')
                    ->columnSpanFull()
                    ->tabs([
                        Tabs\Tab::make('Compte')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Section::make('Profil')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                ImageEntry::make('avatar')
                                                    ->label('Avatar')
                                                    ->circular()
                                                    ->height(120),
                                                Group::make([
                                                    TextEntry::make('name')
                                                        ->label('Nom')
                                                        ->size(10)
                                                        ->weight('bold'),
                                                    TextEntry::make('email')
                                                        ->label('Email')
                                                        ->copyable()
                                                        ->icon('heroicon-o-envelope')
                                                        ->url(fn($record) => "mailto:{$record->email}"),
                                                    TextEntry::make('phone')
                                                        ->label('Téléphone')
                                                        ->copyable()
                                                        ->icon('heroicon-o-phone')
                                                        ->default('Non renseigné'),
                                                ])
                                                    ->columnSpan(2),
                                            ]),
                                    ]),

                                Section::make('Rôles & Permissions')
                                    ->icon('heroicon-o-shield-check')
                                    ->schema([
                                        TextEntry::make('roles.name')
                                            ->label('Rôles')
                                            ->badge()
                                            ->separator(','),
                                        IconEntry::make('is_active')
                                            ->label('Actif')
                                            ->boolean()
                                            ->trueIcon('heroicon-o-check-circle')
                                            ->falseIcon('heroicon-o-x-circle')
                                            ->trueColor('success')
                                            ->falseColor('danger'),
                                    ])
                                    ->columns(2),
                            ]),

                        Tabs\Tab::make('Commercial')
                            ->icon('heroicon-o-building-storefront')
                            ->schema([
                                Section::make('Profil Commercial')
                                    ->icon('heroicon-o-briefcase')
                                    ->schema([
                                        TextEntry::make('shop_name')
                                            ->label('Nom de boutique')
                                            ->default('Non défini')
                                            ->icon('heroicon-o-building-storefront'),
                                        TextEntry::make('subdomain')
                                            ->label('Sous-domaine')
                                            ->default('Non défini')
                                            ->copyable()
                                            ->icon('heroicon-o-link')
                                            ->url(fn($record) => $record->subdomain ? "https://{$record->subdomain}.example.com" : null)
                                            ->openUrlInNewTab(),
                                        TextEntry::make('whatsapp_number')
                                            ->label('WhatsApp')
                                            ->default('Non renseigné')
                                            ->copyable()
                                            ->icon('heroicon-o-phone')
                                            ->url(fn($record) => "https://wa.me/" . preg_replace('/[^0-9]/', '', $record->whatsapp_number ?? ''))
                                            ->openUrlInNewTab(),
                                        TextEntry::make('bio')
                                            ->label('Bio')
                                            ->default('Aucune bio')
                                            ->columnSpanFull()
                                            ->limit(200),
                                    ])
                                    ->columns(3),

                                Section::make('Groupes Commerciaux')
                                    ->icon('heroicon-o-user-group')
                                    ->schema([
                                        TextEntry::make('commercialGroups.name')
                                            ->label('Groupes')
                                            ->badge()
                                            ->separator(',')
                                            ->default('Aucun groupe'),
                                    ]),
                            ]),

                        Tabs\Tab::make('Réseaux Sociaux')
                            ->icon('heroicon-o-globe-alt')
                            ->schema([
                                Section::make('Liens sociaux')
                                    ->schema([
                                        TextEntry::make('social_links.facebook')
                                            ->label('Facebook')
                                            ->default('Non renseigné')
                                            ->url(fn($record) => $record->social_links['facebook'] ?? null)
                                            ->openUrlInNewTab(),
                                        TextEntry::make('social_links.instagram')
                                            ->label('Instagram')
                                            ->default('Non renseigné')
                                            ->url(fn($record) => $record->social_links['instagram'] ?? null)
                                            ->openUrlInNewTab(),
                                        TextEntry::make('social_links.linkedin')
                                            ->label('LinkedIn')
                                            ->default('Non renseigné')
                                            ->url(fn($record) => $record->social_links['linkedin'] ?? null)
                                            ->openUrlInNewTab(),
                                        TextEntry::make('social_links.twitter')
                                            ->label('Twitter')
                                            ->default('Non renseigné')
                                            ->url(fn($record) => $record->social_links['twitter'] ?? null)
                                            ->openUrlInNewTab(),
                                    ])
                                    ->columns(4),
                            ]),

                        Tabs\Tab::make('Branding')
                            ->icon('heroicon-o-paint-brush')
                            ->schema([
                                Section::make('Branding')
                                    ->schema([
                                        ColorEntry::make('branding.primary_color')
                                            ->label('Couleur principale'),
                                        ColorEntry::make('branding.secondary_color')
                                            ->label('Couleur secondaire'),
                                        ColorEntry::make('branding.accent_color')
                                            ->label('Couleur d\'accent'),
                                    ])
                                    ->columns(3),
                            ]),

                        Tabs\Tab::make('Activité')
                            ->icon('heroicon-o-clock')
                            ->schema([
                                Section::make('Dates')
                                    ->schema([
                                        TextEntry::make('email_verified_at')
                                            ->label('Email vérifié le')
                                            ->dateTime('d/m/Y à H:i')
                                            ->placeholder('Non vérifié')
                                            ->icon('heroicon-o-envelope-open'),
                                        TextEntry::make('created_at')
                                            ->label('Créé le')
                                            ->dateTime('d/m/Y à H:i')
                                            ->icon('heroicon-o-calendar'),
                                        TextEntry::make('updated_at')
                                            ->label('Modifié le')
                                            ->since()
                                            ->icon('heroicon-o-clock'),
                                    ])
                                    ->columns(3),
                            ]),
                    ]),
            ]);
    }
}
