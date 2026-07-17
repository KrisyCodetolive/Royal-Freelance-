<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Module 7 — comble le gap de l'espace commercial : contrairement au panel
 * Filament (App\Http\Middleware\Filament\Authenticate) et aux tunnels
 * publics (ResolveFunnelFromSubdomain), les routes `/commercial/*` ne sont
 * protégées que par le rôle Spatie (RoleMiddleware) — un commercial d'un
 * tenant suspendu pouvait donc encore accéder à son propre dashboard.
 */
class EnsureTenantNotSuspended
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user instanceof User && ! $user->isSuperAdmin() && $user->tenant?->isSuspended()) {
            abort(403, 'Votre espace a été suspendu. Pour toute réclamation, contactez support@royalleadpro.com.');
        }

        return $next($request);
    }
}
