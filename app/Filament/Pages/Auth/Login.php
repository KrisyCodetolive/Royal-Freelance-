<?php

namespace App\Filament\Pages\Auth;

use App\Models\User;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;

class Login extends BaseLogin
{
    /**
     * Court-circuite l'authentification pour renvoyer un message explicite
     * quand les identifiants sont corrects mais que le tenant est suspendu
     * — sinon Filament::attemptWhen() échoue silencieusement sur
     * User::canAccessPanel() et affiche "identifiants invalides", ce qui
     * laisse croire à l'admin que son mot de passe est faux.
     */
    public function authenticate(): ?LoginResponse
    {
        $credentials = $this->getCredentialsFromFormData($this->form->getState());

        $authProvider = Filament::auth()->getProvider();
        $user = $authProvider->retrieveByCredentials($credentials);

        if (
            $user instanceof User
            && $authProvider->validateCredentials($user, $credentials)
            && $user->tenant?->isSuspended()
        ) {
            Notification::make()
                ->danger()
                ->title('Espace suspendu')
                ->body('Votre espace a été suspendu. Pour toute réclamation, contactez support@royalleadpro.com.')
                ->persistent()
                ->send();

            return null;
        }

        return parent::authenticate();
    }
}
