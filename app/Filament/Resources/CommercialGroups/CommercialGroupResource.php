<?php

namespace App\Filament\Resources\CommercialGroups;

use App\Filament\Resources\CommercialGroups\Pages\CreateCommercialGroup;
use App\Filament\Resources\CommercialGroups\Pages\EditCommercialGroup;
use App\Filament\Resources\CommercialGroups\Pages\ListCommercialGroups;
use App\Filament\Resources\CommercialGroups\Pages\ViewCommercialGroup;
use App\Filament\Resources\CommercialGroups\Schemas\CommercialGroupForm;
use App\Filament\Resources\CommercialGroups\Schemas\CommercialGroupInfolist;
use App\Filament\Resources\CommercialGroups\Tables\CommercialGroupsTable;
use App\Models\CommercialGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CommercialGroupResource extends Resource
{
    protected static ?string $model = CommercialGroup::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static string|\UnitEnum|null $navigationGroup = 'Équipe';

    protected static ?string $modelLabel = 'Groupe Commercial';

    protected static ?string $pluralModelLabel = 'Groupes Commerciaux';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return CommercialGroupForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CommercialGroupInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CommercialGroupsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\CommercialsRelationManager::class,
            RelationManagers\FunnelsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCommercialGroups::route('/'),
            'create' => CreateCommercialGroup::route('/create'),
            'view' => ViewCommercialGroup::route('/{record}'),
            'edit' => EditCommercialGroup::route('/{record}/edit'),
        ];
    }

    // Module 5 : gestion d'équipe réservée à Owner/Admin (cf. UserResource).
    public static function canViewAny(): bool
    {
        return (bool) auth()->user()?->isAdmin();
    }

    public static function canCreate(): bool
    {
        return (bool) auth()->user()?->isAdmin();
    }

    public static function canEdit(Model $record): bool
    {
        return (bool) auth()->user()?->isAdmin();
    }

    public static function canDelete(Model $record): bool
    {
        return (bool) auth()->user()?->isAdmin();
    }
}
