<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        using: function (): void {
            // tenant.php AVANT web.php : web.php contient un catch-all de domaine
            // (Route::domain('{domain}')->get('/{pageSlug}', ...)) qui capturerait
            // sinon des routes fixes comme /demarrer en les traitant comme un slug de funnel.
            Route::middleware('web')->group(__DIR__ . '/../routes/tenant.php');
            Route::middleware('web')->group(__DIR__ . '/../routes/web.php');
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Faire confiance aux proxies (Nginx, Cloudflare, Load Balancer)
        $middleware->trustProxies(at: '*');

        // Désactiver CSRF pour toutes les soumissions de formulaires (Lead Magnet public)
        $middleware->validateCsrfTokens(except: [
            '/f/*',          // Couvre /f/funnel/page/submit
            '/*/submit',     // Couvre /page/submit (sur sous-domaine)
            '/submit',       // Couvre /submit (racine)
        ]);

        // Middleware pour résoudre le funnel depuis le sous-domaine
        $middleware->alias([
            'subdomain.funnel' => \App\Http\Middleware\ResolveFunnelFromSubdomain::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'tenant.active' => \App\Http\Middleware\EnsureTenantNotSuspended::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
