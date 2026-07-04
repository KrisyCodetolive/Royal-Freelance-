<?php

namespace App\Filament\Resources\Funnels;

use App\Filament\Resources\Funnels\Pages\CreateFunnel;
use App\Filament\Resources\Funnels\Pages\EditFunnel;
use App\Filament\Resources\Funnels\Pages\ListFunnels;
use App\Filament\Resources\Funnels\Pages\ManageFunnelPages;
use App\Filament\Resources\Funnels\Pages\ViewFunnel;
use App\Filament\Resources\Funnels\Schemas\FunnelForm;
use App\Filament\Resources\Funnels\Schemas\FunnelInfolist;
use App\Filament\Resources\Funnels\Tables\FunnelsTable;
use App\Models\Funnel;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FunnelResource extends Resource
{
    protected static ?string $model = Funnel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFunnel;

    protected static ?string $modelLabel = 'Tunnel';

    protected static ?string $pluralModelLabel = 'Tunnels';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->where('is_template', false);

        // Module 5 : Owner/Admin voient tout le tenant, Editor/Viewer sont
        // restreints à leurs tunnels assignés/partagés (scopeAvailableToUser
        // gère déjà le bypass admin en interne).
        if ($user = auth()->user()) {
            $query->availableToUser($user);
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return FunnelForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return FunnelInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FunnelsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PagesRelationManager::class,
            RelationManagers\LeadsRelationManager::class,
            RelationManagers\AnalyticsRelationManager::class,
            RelationManagers\EmailSequencesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFunnels::route('/'),
            'create' => CreateFunnel::route('/create'),
            'view' => ViewFunnel::route('/{record}'),
            'edit' => EditFunnel::route('/{record}/edit'),
            'edit' => EditFunnel::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_template', false)->count();
    }

    // Module 5 : seul Owner/Admin crée ou supprime des tunnels. Editor peut
    // éditer uniquement les tunnels qui lui sont assignés/partagés en
    // édition (cf. Funnel::canBeEditedBy()). Viewer ne peut jamais éditer.
    public static function canCreate(): bool
    {
        return (bool) auth()->user()?->isAdmin();
    }

    public static function canEdit(Model $record): bool
    {
        $user = auth()->user();

        return $user !== null && $record->canBeEditedBy($user);
    }

    public static function canDelete(Model $record): bool
    {
        return (bool) auth()->user()?->isAdmin();
    }
}
