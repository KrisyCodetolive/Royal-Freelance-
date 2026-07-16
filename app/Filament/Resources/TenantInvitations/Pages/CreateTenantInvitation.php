<?php

namespace App\Filament\Resources\TenantInvitations\Pages;

use App\Filament\Resources\TenantInvitations\TenantInvitationResource;
use App\Mail\TenantInvitationMail;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Mail;

class CreateTenantInvitation extends CreateRecord
{
    protected static string $resource = TenantInvitationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['invited_by'] = auth()->id();
        $data['expires_at'] = now()->addDays(7);

        return $data;
    }

    protected function afterCreate(): void
    {
        Mail::to($this->record->email)->send(new TenantInvitationMail($this->record));
    }
}
