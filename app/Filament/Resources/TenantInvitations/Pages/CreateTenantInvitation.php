<?php

namespace App\Filament\Resources\TenantInvitations\Pages;

use App\Filament\Resources\TenantInvitations\TenantInvitationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTenantInvitation extends CreateRecord
{
    protected static string $resource = TenantInvitationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['invited_by'] = auth()->id();
        $data['expires_at'] = now()->addDays(7);

        return $data;
    }
}
