<?php

namespace App\Filament\Resources\TenantInvitations\Pages;

use App\Filament\Resources\TenantInvitations\Schemas\TenantInvitationForm;
use App\Filament\Resources\TenantInvitations\TenantInvitationResource;
use App\Mail\TenantInvitationMail;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Exceptions\Halt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

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

    /**
     * L'invitation (avec son lien) est déjà en base à ce stade — un souci SMTP
     * transitoire (ex: Mailpit non démarré en local, panne du fournisseur mail
     * en prod) ne doit pas faire planter toute la requête et laisser l'admin
     * croire que rien n'a été créé. On avertit plutôt et on laisse le lien
     * copiable (cf. TenantInvitationsTable) servir de repli.
     */
    protected function afterCreate(): void
    {
        try {
            Mail::to($this->record->email)->send(new TenantInvitationMail($this->record));
        } catch (Throwable $e) {
            Log::error('Échec de l\'envoi de l\'email d\'invitation', [
                'invitation_id' => $this->record->id,
                'email' => $this->record->email,
                'error' => $e->getMessage(),
            ]);

            Notification::make()
                ->title('Invitation créée, mais l\'email n\'a pas pu être envoyé')
                ->body('Copiez le lien d\'invitation depuis le tableau pour le transmettre manuellement.')
                ->warning()
                ->send();
        }
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
