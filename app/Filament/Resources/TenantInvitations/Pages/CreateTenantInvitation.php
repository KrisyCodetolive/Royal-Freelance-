<?php

namespace App\Filament\Resources\TenantInvitations\Pages;

use App\Filament\Resources\TenantInvitations\Schemas\TenantInvitationForm;
use App\Filament\Resources\TenantInvitations\TenantInvitationResource;
use App\Mail\TenantInvitationMail;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Exceptions\Halt;
use Illuminate\Support\Facades\Mail;

class CreateTenantInvitation extends CreateRecord
{
    protected static string $resource = TenantInvitationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->haltIfRoleNotAllowedByPlan($data['role'] ?? null);

        $data['invited_by'] = auth()->id();
        $data['expires_at'] = now()->addDays(7);

        return $data;
    }

    protected function afterCreate(): void
    {
        Mail::to($this->record->email)->send(new TenantInvitationMail($this->record));
    }

    /**
     * Défense en profondeur : le Select du formulaire ne propose déjà que les
     * rôles autorisés par le plan (cf. TenantInvitationForm::roleOptions()),
     * mais un state manipulé (payload Livewire trafiqué) doit être rejeté ici.
     */
    private function haltIfRoleNotAllowedByPlan(?string $role): void
    {
        if ($role !== null && array_key_exists($role, TenantInvitationForm::roleOptions())) {
            return;
        }

        Notification::make()
            ->title('Rôle non disponible sur votre plan')
            ->body("Votre plan actuel ne permet pas d'inviter avec ce rôle. Passez à un plan supérieur pour le débloquer.")
            ->danger()
            ->send();

        throw new Halt();
    }
}
