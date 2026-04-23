<?php

namespace App\Filament\Resources\FunnelTemplates;

use App\Filament\Resources\FunnelTemplates\Pages\CreateFunnelTemplate;
use App\Filament\Resources\FunnelTemplates\Pages\EditFunnelTemplate;
use App\Filament\Resources\FunnelTemplates\Pages\ListFunnelTemplates;
use App\Filament\Resources\FunnelTemplates\Pages\ViewFunnelTemplate;
use App\Filament\Resources\FunnelTemplates\Schemas\FunnelTemplateForm;
use App\Filament\Resources\FunnelTemplates\Tables\FunnelTemplatesTable;
use App\Models\Funnel;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FunnelTemplateResource extends Resource
{
    protected static ?string $model = Funnel::class;


    protected static ?string $navigationLabel = 'Templates Tunnels';

    protected static ?string $modelLabel = 'Template';

    protected static ?string $pluralModelLabel = 'Templates';

    protected static ?int $navigationSort = 0;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('is_template', true);
    }

    public static function form(Schema $schema): Schema
    {
        return FunnelTemplateForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FunnelTemplatesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\Funnels\RelationManagers\PagesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFunnelTemplates::route('/'),
            'create' => CreateFunnelTemplate::route('/create'),
            'view' => ViewFunnelTemplate::route('/{record}'),
            'edit' => EditFunnelTemplate::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_template', true)->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }
}
