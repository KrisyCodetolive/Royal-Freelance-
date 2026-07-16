<?php

namespace App\Filament\Resources\Funnels\Schemas;

use Filament\Infolists\Components\ColorEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class FunnelInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Tabs::make('Funnel')
                    ->columnSpanFull()
                    ->tabs([
                        Tabs\Tab::make('Général')
                            ->icon('heroicon-o-adjustments-horizontal')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Section::make('Informations')
                                            ->schema([
                                                TextEntry::make('name')
                                                    ->label('Nom')
                                                    ->size(10)
                                                    ->weight('bold')
                                                    ->icon('heroicon-o-funnel'),
                                                TextEntry::make('slug')
                                                    ->label('Slug')
                                                    ->copyable()
                                                    ->icon('heroicon-o-link')
                                                    ->url(fn($record) => route('funnel.root', $record))
                                                    ->openUrlInNewTab(),
                                                TextEntry::make('offer.name')
                                                    ->label('Offre liée')
                                                    ->badge()
                                                    ->color('success'),
                                                TextEntry::make('status')
                                                    ->label('Statut')
                                                    ->badge(),
                                                TextEntry::make('template')
                                                    ->label('Modèle')
                                                    ->default('Personnalisé')
                                                    ->badge()
                                                    ->color('info'),
                                            ])
                                            ->columnSpan(1),

                                        Section::make('Configuration')
                                            ->schema([
                                                TextEntry::make('assignedTo.name')
                                                    ->label('Assigné à')
                                                    ->default('Non assigné')
                                                    ->icon('heroicon-o-user'),
                                                TextEntry::make('whatsapp_url')
                                                    ->label('WhatsApp')
                                                    ->default('Non configuré')
                                                    ->copyable()
                                                    ->icon('heroicon-o-phone')
                                                    ->url(fn($record) => $record->whatsapp_url)
                                                    ->openUrlInNewTab(),
                                                TextEntry::make('payment_url')
                                                    ->label('Paiement')
                                                    ->default('Non configuré')
                                                    ->copyable()
                                                    ->icon('heroicon-o-credit-card')
                                                    ->url(fn($record) => $record->payment_url)
                                                    ->openUrlInNewTab(),
                                            ])
                                            ->columnSpan(1),
                                    ]),

                                // Module 4/6 (retour QA) : avant ceci, rien n'indiquait qu'un
                                // tunnel était partagé ni avec qui — visible uniquement en
                                // rouvrant le formulaire de partage à l'aveugle.
                                Section::make('Partage')
                                    ->icon('heroicon-o-user-group')
                                    ->columns(2)
                                    ->schema([
                                        TextEntry::make('users.name')
                                            ->label('Partagé avec (individuel)')
                                            ->badge()
                                            ->listWithLineBreaks()
                                            ->formatStateUsing(function (string $state, $record) {
                                                $canEdit = $record->users
                                                    ->firstWhere('name', $state)
                                                    ?->pivot
                                                    ?->can_edit;

                                                return $state . ($canEdit ? ' (édition)' : ' (lecture seule)');
                                            })
                                            ->color(fn($state, $record) => ($record->users->firstWhere('name', $state)?->pivot?->can_edit) ? 'success' : 'gray')
                                            ->default('Non partagé individuellement'),

                                        TextEntry::make('commercialGroups.name')
                                            ->label('Partagé avec (groupes)')
                                            ->badge()
                                            ->listWithLineBreaks()
                                            ->color('info')
                                            ->default('Non partagé avec un groupe'),
                                    ]),
                            ]),

                        Tabs\Tab::make('Performance')
                            ->icon('heroicon-o-chart-bar')
                            ->schema([
                                Section::make('Statistiques Clés')
                                    ->columns(3)
                                    ->schema([
                                        TextEntry::make('leads_count')
                                            ->label('Total Leads')
                                            ->numeric()
                                            ->icon('heroicon-o-users')
                                            ->size(10),
                                        TextEntry::make('conversions_count')
                                            ->label('Conversions')
                                            ->numeric()
                                            ->icon('heroicon-o-check-circle')
                                            ->size(10),
                                        TextEntry::make('conversion_rate')
                                            ->label('Taux de conversion')
                                            ->state(function ($record) {
                                                if ($record->leads_count > 0) {
                                                    return round(($record->conversions_count / $record->leads_count) * 100, 1);
                                                }
                                                return 0;
                                            })
                                            ->suffix('%')
                                            ->color(fn($state) => match (true) {
                                                $state >= 10 => 'success',
                                                $state >= 5 => 'warning',
                                                default => 'danger',
                                            })
                                            ->icon('heroicon-o-arrow-trending-up')
                                            ->size(10),
                                    ]),
                            ]),

                        Tabs\Tab::make('Marketing')
                            ->icon('heroicon-o-presentation-chart-line')
                            ->schema([
                                Section::make('SEO')
                                    ->columns(2)
                                    ->schema([
                                        Group::make([
                                            TextEntry::make('meta_title')
                                                ->label('Titre Meta')
                                                ->default('Non défini'),
                                            TextEntry::make('meta_description')
                                                ->label('Description Meta')
                                                ->default('Non définie')
                                                ->limit(100),
                                        ]),
                                        ImageEntry::make('meta_image')
                                            ->label('Image de partage')
                                            ->default(null)
                                            ->height(120),
                                    ]),
                                Section::make('Branding')
                                    ->columns(3)
                                    ->schema([
                                        ColorEntry::make('primary_color')->label('Primaire'),
                                        ColorEntry::make('secondary_color')->label('Secondaire'),
                                        ColorEntry::make('background_color')->label('Fond'),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
