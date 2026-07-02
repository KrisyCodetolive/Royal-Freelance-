<?php

namespace App\Filament\Resources\TenantInvitations;

use App\Filament\Resources\TenantInvitations\Pages\CreateTenantInvitation;
use App\Filament\Resources\TenantInvitations\Pages\ListTenantInvitations;
use App\Filament\Resources\TenantInvitations\Schemas\TenantInvitationForm;
use App\Filament\Resources\TenantInvitations\Tables\TenantInvitationsTable;
use App\Models\TenantInvitation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TenantInvitationResource extends Resource
{
    protected static ?string $model = TenantInvitation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static string|\UnitEnum|null $navigationGroup = 'Équipe';

    protected static ?string $modelLabel = 'Invitation';

    protected static ?string $pluralModelLabel = 'Invitations';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return TenantInvitationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TenantInvitationsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTenantInvitations::route('/'),
            'create' => CreateTenantInvitation::route('/create'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereNull('used_at')->where('expires_at', '>', now())->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }
}
