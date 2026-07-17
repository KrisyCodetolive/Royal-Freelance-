<?php

namespace App\Http\Middleware\Filament;

use App\Models\User;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate as BaseAuthenticate;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Model;

class Authenticate extends BaseAuthenticate
{
    /**
     * Distingue le cas "tenant suspendu" du 403 générique de Filament : sans
     * ça, un admin dont le tenant vient d'être suspendu pendant sa session
     * tombe sur "Forbidden" sans explication (cf. resources/views/errors/403,
     * qui affiche déjà $exception->getMessage() — on se contente de le
     * renseigner avant que canAccessPanel() ne fasse échouer l'accès).
     */
    protected function authenticate($request, array $guards): void
    {
        $guard = Filament::auth();

        if (! $guard->check()) {
            $this->unauthenticated($request, $guards);

            return; /** @phpstan-ignore-line */
        }

        $this->auth->shouldUse(Filament::getAuthGuard());

        /** @var Model $user */
        $user = $guard->user();

        $panel = Filament::getCurrentOrDefaultPanel();

        if ($user instanceof User && ! $user->isSuperAdmin() && $user->tenant?->isSuspended()) {
            abort(403, 'Votre espace a été suspendu. Pour toute réclamation, contactez support@royalleadpro.com.');
        }

        abort_if(
            $user instanceof FilamentUser ?
                (! $user->canAccessPanel($panel)) :
                (config('app.env') !== 'local'),
            403,
        );
    }
}
