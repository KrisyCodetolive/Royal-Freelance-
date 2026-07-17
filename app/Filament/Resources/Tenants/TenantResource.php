<?php

namespace App\Filament\Resources\Tenants;

use App\Filament\Resources\Tenants\Pages\ListTenants;
use App\Filament\Resources\Tenants\Pages\ViewTenant;
use App\Filament\Resources\Tenants\Schemas\TenantInfolist;
use App\Filament\Resources\Tenants\Tables\TenantsTable;
use App\Models\Tenant;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

/**
 * Vue d'ensemble des tenants pour le super_admin (porteur de projet) :
 * coordonnées, propriétaire, plan — tout sauf le mot de passe. Lecture seule
 * (les tenants naissent de l'inscription self-service, pas d'un formulaire
 * admin) — même logique de restriction que SubscriptionResource.
 */
class TenantResource extends Resource
{
    protected static ?string $model = Tenant::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static string|\UnitEnum|null $navigationGroup = 'Super Admin';

    protected static ?string $modelLabel = 'Tenant';

    protected static ?string $pluralModelLabel = 'Tenants';

    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }

    public static function infolist(Schema $schema): Schema
    {
        return TenantInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TenantsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTenants::route('/'),
            'view' => ViewTenant::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }
}
