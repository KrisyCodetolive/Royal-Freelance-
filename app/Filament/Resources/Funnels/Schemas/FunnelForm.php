<?php

namespace App\Filament\Resources\Funnels\Schemas;

use App\Enums\FunnelStatus;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\KeyValue;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Toggle;
use Filament\Schemas\Schema;

class FunnelForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Tunnel')
                    ->columnSpanFull()
                    ->tabs([
                        Tabs\Tab::make('Informations')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Section::make('Informations générales')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Nom du tunnel')
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function ($state, callable $set, $get) {
                                                $set('slug', \Illuminate\Support\Str::slug($state));
                                                // Générer le sous-domaine seulement si vide
                                                if (empty($get('subdomain'))) {
                                                    $set('subdomain', \Illuminate\Support\Str::slug($state));
                                                }
                                            }),
                                        TextInput::make('slug')
                                            ->label('Slug URL')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(255)
                                            ->prefix('/')
                                            ->helperText('URL classique: /f/{slug}'),
                                        Select::make('offer_id')
                                            ->label('Offre associée')
                                            ->relationship('offer', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->createOptionForm([
                                                TextInput::make('name')
                                                    ->label('Nom de l\'offre')
                                                    ->required(),
                                            ]),
                                        Select::make('status')
                                            ->label('Statut')
                                            ->options(FunnelStatus::class)
                                            ->default(FunnelStatus::DRAFT)
                                            ->required(),
                                        Select::make('assigned_to')
                                            ->label('Responsable')
                                            ->relationship('assignedTo', 'name')
                                            ->searchable()
                                            ->preload(),
                                        Select::make('template')
                                            ->label('Modèle')
                                            ->options([
                                                'formation' => 'Formation',
                                                'livre' => 'Livre / Ebook',
                                                'mlm' => 'MLM / Opportunité',
                                            ])
                                            ->helperText('Génère automatiquement les pages'),
                                        Textarea::make('description')
                                            ->label('Description')
                                            ->rows(3)
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tabs\Tab::make('URL & Domaine')
                            ->icon('heroicon-o-globe-alt')
                            ->schema([
                                Section::make('Sous-domaine personnalisé')
                                    ->description('Chaque tunnel possède son propre sous-domaine pour une URL professionnelle et mémorable.')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('subdomain')
                                            ->label('Sous-domaine')
                                            ->minLength(3)
                                            ->maxLength(63)
                                            ->regex('/^[a-z][a-z0-9]*(-[a-z0-9]+)*$/')
                                            ->unique(table: 'funnels', column: 'subdomain', ignoreRecord: true)
                                            ->prefix('https://')
                                            ->suffix('.' . config('app.subdomain_base', 'votredomaine.com'))
                                            ->placeholder('mon-tunnel')
                                            ->helperText('Lettres minuscules, chiffres et tirets uniquement. Doit commencer par une lettre.'),
                                        TextInput::make('custom_domain')
                                            ->label('Domaine personnalisé (optionnel)')
                                            ->placeholder('www.monsite.com')
                                            ->helperText('Pour un domaine personnalisé, configurez les DNS pointant vers notre serveur.'),
                                    ]),
                            ]),

                        Tabs\Tab::make('WhatsApp & Paiement')
                            ->icon('heroicon-o-phone')
                            ->schema([
                                Section::make('Configuration WhatsApp')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('whatsapp_url')
                                            ->label('Numéro WhatsApp')
                                            ->placeholder('+237699000000')
                                            ->tel()
                                            ->helperText('Format international'),
                                        Textarea::make('whatsapp_message')
                                            ->label('Message pré-rempli')
                                            ->placeholder('Bonjour, je suis intéressé par...')
                                            ->rows(3)
                                            ->columnSpanFull(),
                                    ]),
                                Section::make('Configuration Paiement')
                                    ->schema([
                                        TextInput::make('payment_url')
                                            ->label('URL de paiement')
                                            ->url()
                                            ->helperText('Lien vers la page de paiement externe'),
                                    ]),
                            ]),

                        Tabs\Tab::make('SEO & Méta')
                            ->icon('heroicon-o-globe-alt')
                            ->schema([
                                Section::make('Référencement')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('meta_title')
                                            ->label('Titre SEO')
                                            ->maxLength(60)
                                            ->helperText('Max 60 caractères'),
                                        FileUpload::make('meta_image')
                                            ->label('Image de partage')
                                            ->image()
                                            ->directory('funnels/meta')
                                            ->helperText('1200x630px recommandé'),
                                        Textarea::make('meta_description')
                                            ->label('Description SEO')
                                            ->rows(3)
                                            ->maxLength(160)
                                            ->helperText('Max 160 caractères')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tabs\Tab::make('Branding')
                            ->icon('heroicon-o-paint-brush')
                            ->schema([
                                Section::make('Personnalisation visuelle')
                                    ->columns(3)
                                    ->schema([
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
