<?php

namespace App\Filament\Resources\EmailSequences;

use App\Filament\Resources\EmailSequences\Pages\CreateEmailSequence;
use App\Filament\Resources\EmailSequences\Pages\EditEmailSequence;
use App\Filament\Resources\EmailSequences\Pages\ListEmailSequences;
use App\Filament\Resources\EmailSequences\RelationManagers\EmailsRelationManager;
use App\Filament\Resources\EmailSequences\Schemas\EmailSequenceForm;
use App\Filament\Resources\EmailSequences\Tables\EmailSequencesTable;
use App\Models\EmailSequence;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class EmailSequenceResource extends Resource
{
    protected static ?string $model = EmailSequence::class;

    protected static ?string $modelLabel = 'Séquence Email';

    protected static ?string $pluralModelLabel = 'Séquences Email';

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-envelope-open';
    }

    public static function form(Schema $schema): Schema
    {
        return EmailSequenceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EmailSequencesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            EmailsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEmailSequences::route('/'),
            'create' => CreateEmailSequence::route('/create'),
            'edit' => EditEmailSequence::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'active')->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'success';
    }
}
