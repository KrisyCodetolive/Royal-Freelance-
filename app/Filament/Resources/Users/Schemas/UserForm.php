<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\KeyValue;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;


class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Utilisateur')
                    ->columnSpanFull()
                    ->tabs([
                        Tabs\Tab::make('Compte')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Section::make('Informations de connexion')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Nom complet')
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('email')
                                            ->label('Email')
                                            ->email()
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(255),
                                        TextInput::make('password')
                                            ->label('Mot de passe')
                                            ->password()
                                            ->dehydrateStateUsing(fn($state) => filled($state) ? bcrypt($state) : null)
                                            ->dehydrated(fn($state) => filled($state))
                                            ->required(fn(string $operation): bool => $operation === 'create')
                                            ->minLength(8)
                                            ->revealable(),
                                        TextInput::make('phone')
                                            ->label('Téléphone')
                                            ->tel(),
                                        Select::make('roles')
                                            ->label('Rôles')
                                            ->relationship('roles', 'name')
                                            ->multiple()
                                            ->preload()
                                            ->required(),
                                        Toggle::make('is_active')
                                            ->label('Compte actif')
                                            ->default(true),
                                    ]),
                            ]),

                        Tabs\Tab::make('Commercial')
                            ->icon('heroicon-o-building-storefront')
                            ->schema([
                                Section::make('Profil Commercial')
                                    ->description('Ces informations sont utilisées pour les commerciaux uniquement.')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('shop_name')
                                            ->label('Nom de boutique')
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function ($state, callable $set, $get) {
                                                if ($state && !$get('subdomain')) {
                                                    $set('subdomain', Str::slug($state));
                                                }
                                            })
                                            ->helperText('Le nom affiché pour ce commercial'),
                                        TextInput::make('subdomain')
                                            ->label('Sous-domaine')
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(30)
                                            ->prefix('https://')
                                            ->suffix('.royalleadmagnet.com')
                                            ->helperText('URL personnalisée du commercial'),
                                        TextInput::make('whatsapp_number')
                                            ->label('WhatsApp')
                                            ->tel()
                                            ->placeholder('+237699000000'),
                                        Textarea::make('bio')
                                            ->label('Biographie')
                                            ->rows(3)
                                            ->columnSpanFull(),
                                    ]),
                                Section::make('Groupes Commerciaux')
                                    ->schema([
                                        Select::make('commercialGroups')
                                            ->label('Groupes')
                                            ->relationship('commercialGroups', 'name')
                                            ->multiple()
                                            ->preload()
                                            ->searchable(),
                                    ]),
                            ]),

                        Tabs\Tab::make('Réseaux sociaux')
                            ->icon('heroicon-o-share')
                            ->schema([
                                Section::make('Liens sociaux')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('social_links.facebook')
                                            ->label('Facebook')
                                            ->url()
                                            ->prefix('https://'),
                                        TextInput::make('social_links.instagram')
                                            ->label('Instagram')
                                            ->url()
                                            ->prefix('https://'),
                                        TextInput::make('social_links.tiktok')
                                            ->label('TikTok')
                                            ->url()
                                            ->prefix('https://'),
                                        TextInput::make('social_links.youtube')
                                            ->label('YouTube')
                                            ->url()
                                            ->prefix('https://'),
                                    ]),
                            ]),

                        Tabs\Tab::make('Branding')
                            ->icon('heroicon-o-paint-brush')
                            ->schema([
                                Section::make('Personnalisation')
                                    ->columns(3)
                                    ->schema([
                                        FileUpload::make('avatar')
                                            ->label('Photo de profil')
                                            ->image()
                                            ->avatar()
                                            ->directory('avatars')
                                            ->columnSpanFull(),
                                        ColorPicker::make('branding.primary_color')
                                            ->label('Couleur principale'),
                                        ColorPicker::make('branding.secondary_color')
                                            ->label('Couleur secondaire'),
                                        ColorPicker::make('branding.accent_color')
                                            ->label('Couleur d\'accent'),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
