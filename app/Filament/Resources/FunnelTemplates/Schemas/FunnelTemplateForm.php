<?php

namespace App\Filament\Resources\FunnelTemplates\Schemas;

use App\Enums\FunnelStatus;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class FunnelTemplateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Template')
                    ->columnSpanFull()
                    ->tabs([
                        Tabs\Tab::make('Informations')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Section::make('Identité du Template')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Nom du Template')
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn($state, callable $set) => $set('slug', Str::slug($state))),

                                        TextInput::make('slug')
                                            ->label('Slug')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(255),

                                        Select::make('template_category')
                                            ->label('Catégorie')
                                            ->options([
                                                'lead_capture' => '📧 Capture de Leads',
                                                'webinar' => '🎥 Webinaire',
                                                'sales' => '💰 Page de Vente',
                                                'thank_you' => '🙏 Page de Remerciement',
                                                'coming_soon' => '⏳ Coming Soon',
                                                'product_launch' => '🚀 Lancement de Produit',
                                                'quiz' => '❓ Quiz / Sondage',
                                                'free_training' => '🎓 Formation Gratuite',
                                            ])
                                            ->required()
                                            ->searchable(),

                                        Select::make('status')
                                            ->label('Statut')
                                            ->options(FunnelStatus::class)
                                            ->default(FunnelStatus::DRAFT)
                                            ->required(),

                                        Textarea::make('template_description')
                                            ->label('Description du Template')
                                            ->rows(3)
                                            ->columnSpanFull()
                                            ->helperText('Description affichée lors de la sélection du template'),

                                        TagsInput::make('template_tags')
                                            ->label('Tags')
                                            ->placeholder('Ajouter un tag...')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tabs\Tab::make('Apparence')
                            ->icon('heroicon-o-paint-brush')
                            ->schema([
                                Section::make('Visuel')
                                    ->schema([
                                        FileUpload::make('template_thumbnail')
                                            ->label('Aperçu du Template')
                                            ->image()
                                            ->directory('template-thumbnails')
                                            ->imageResizeMode('cover')
                                            ->imageCropAspectRatio('16:9')
                                            ->imageResizeTargetWidth('1200')
                                            ->imageResizeTargetHeight('675')
                                            ->helperText('Image d\'aperçu 16:9 (recommandé: 1200x675px)'),
                                    ]),

                                Section::make('SEO par défaut')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('meta_title')
                                            ->label('Titre Meta')
                                            ->maxLength(70)
                                            ->helperText('60-70 caractères max'),

                                        Textarea::make('meta_description')
                                            ->label('Description Meta')
                                            ->rows(2)
                                            ->maxLength(160)
                                            ->helperText('150-160 caractères max'),
                                    ]),
                            ]),

                        Tabs\Tab::make('Configuration')
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                Section::make('WhatsApp')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('whatsapp_url')
                                            ->label('Numéro WhatsApp par défaut')
                                            ->tel()
                                            ->placeholder('+237699000000'),

                                        TextInput::make('whatsapp_message')
                                            ->label('Message par défaut')
                                            ->maxLength(500),
                                    ]),

                                Section::make('Paiement')
                                    ->schema([
                                        TextInput::make('payment_url')
                                            ->label('URL de paiement par défaut')
                                            ->url()
                                            ->placeholder('https://...'),
                                    ]),

                                Section::make('Options')
                                    ->schema([
                                        Toggle::make('is_template')
                                            ->label('Est un template')
                                            ->default(true)
                                            ->disabled()
                                            ->dehydrated(),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
