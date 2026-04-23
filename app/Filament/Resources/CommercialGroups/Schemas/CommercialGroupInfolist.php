<?php

namespace App\Filament\Resources\CommercialGroups\Schemas;

use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Infolists\Components\IconEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CommercialGroupInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Tabs::make('GroupDetails')
                    ->columnSpanFull()
                    ->tabs([
                        Tabs\Tab::make('Aperçu')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Section::make('Informations Générales')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            TextEntry::make('name')
                                                ->label('Nom du groupe')
                                                ->size(10)
                                                ->weight('bold')
                                                ->icon('heroicon-o-user-group'),
                                            TextEntry::make('code')
                                                ->label('Code de référence')
                                                ->copyable()
                                                ->icon('heroicon-o-tag')
                                                ->badge()
                                                ->color('gray'),
                                        ]),
                                        TextEntry::make('description')
                                            ->label('Description')
                                            ->default('Aucune description')
                                            ->columnSpanFull(),
                                    ]),
                                Section::make('Dates')
                                    ->columns(2)
                                    ->schema([
                                        TextEntry::make('created_at')->label('Créé le')->dateTime('d/m/Y'),
                                        TextEntry::make('updated_at')->label('Mis à jour le')->since(),
                                    ]),
                            ]),

                        Tabs\Tab::make('Membres')
                            ->icon('heroicon-o-users')
                            ->schema([
                                Section::make()
                                    ->schema([
                                        TextEntry::make('users.name')
                                            ->label('Commerciaux membres')
                                            ->listWithLineBreaks()
                                            ->bulleted()
                                            ->limitList(10)
                                            ->expandableLimitedList()
                                            ->default('Aucun membre'),
                                    ]),
                            ]),

                        Tabs\Tab::make('Ressources')
                            ->icon('heroicon-o-server-stack')
                            ->schema([
                                Section::make('Accès Tunnels')
                                    ->schema([
                                        TextEntry::make('funnels.name')
                                            ->label('Tunnels assignés')
                                            ->listWithLineBreaks()
                                            ->bulleted()
                                            ->limitList(5)
                                            ->expandableLimitedList()
                                            ->default('Aucun tunnel assigné'),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
