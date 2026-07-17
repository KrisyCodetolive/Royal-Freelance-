<?php

namespace App\Http\Middleware;

use App\Models\Funnel;
use App\Services\SubdomainService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ResolveFunnelFromSubdomain
{
    public function __construct(protected SubdomainService $subdomainService)
    {
    }

    /**
     * Handle an incoming request.
     * 
     * Ce middleware résout le funnel à partir du sous-domaine ou du domaine personnalisé.
     * Compatible avec l'approche Wildcard DNS sur cPanel.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        $baseDomain = $this->subdomainService->getBaseDomain();

        // Debug en développement
        if (config('app.debug')) {
            Log::debug('SubdomainMiddleware', [
                'host' => $host,
                'baseDomain' => $baseDomain,
                'uri' => $request->getRequestUri(),
            ]);
        }

        // Cas spécial : localhost en développement
        if ($this->isLocalDevelopment($host)) {
            return $next($request);
        }

        // 1. Vérifier si c'est un domaine personnalisé (pas un sous-domaine du domaine de base)
        if (!$this->isSubdomainOfBase($host, $baseDomain)) {
            return $this->handleCustomDomain($host, $request, $next);
        }

        // 2. Extraire le sous-domaine
        $subdomain = $this->extractSubdomain($host, $baseDomain);

        if ($subdomain) {
            return $this->handleSubdomain($subdomain, $request, $next);
        }

        // 3. Pas de sous-domaine : requête normale (routes classiques /f/{slug})
        return $next($request);
    }

    /**
     * Vérifie si on est en développement local
     */
    protected function isLocalDevelopment(string $host): bool
    {
        $localHosts = ['localhost', '127.0.0.1', '::1'];

        // Vérifier si c'est un host local (avec ou sans port)
        $hostWithoutPort = explode(':', $host)[0];

        return in_array($hostWithoutPort, $localHosts) ||
            str_ends_with($hostWithoutPort, '.test') ||
            str_ends_with($hostWithoutPort, '.local');
    }

    /**
     * Vérifie si le host est un sous-domaine du domaine de base
     */
    protected function isSubdomainOfBase(string $host, string $baseDomain): bool
    {
        // Exact match du domaine de base
        if ($host === $baseDomain || $host === "www.{$baseDomain}") {
            return true;
        }

        // Sous-domaine du domaine de base
        return str_ends_with($host, ".{$baseDomain}");
    }

    /**
     * Extrait le sous-domaine du host
     */
    protected function extractSubdomain(string $host, string $baseDomain): ?string
    {
        // Si c'est le domaine de base exact
        if ($host === $baseDomain || $host === "www.{$baseDomain}") {
            return null;
        }

        // Extraire la partie avant le domaine de base
        if (str_ends_with($host, ".{$baseDomain}")) {
            $subdomain = substr($host, 0, strlen($host) - strlen(".{$baseDomain}"));

            // Ignorer 'www' comme sous-domaine
            if ($subdomain === 'www') {
                return null;
            }

            return $subdomain;
        }

        return null;
    }

    /**
     * Gère un accès via domaine personnalisé
     */
    protected function handleCustomDomain(string $host, Request $request, Closure $next): Response
    {
        $funnel = $this->subdomainService->findByCustomDomain($host);

        if ($funnel) {
            if ($funnel->tenant?->isSuspended()) {
                return response()->view('funnel.suspended', [], 403);
            }

            if (!$funnel->isPubliclyAccessible()) {
                abort(404, 'Tunnel non disponible');
            }

            $request->attributes->set('funnel', $funnel);
            $request->attributes->set('funnel_access_type', 'custom_domain');
            return $next($request);
        }

        // Domaine personnalisé non trouvé : on laisse passer (peut-être une autre route)
        // En production, on pourrait afficher une page d'erreur ou rediriger
        if (config('app.env') === 'production') {
            abort(404, 'Tunnel non trouvé');
        }

        return $next($request);
    }

    /**
     * Gère un accès via sous-domaine
     */
    protected function handleSubdomain(string $subdomain, Request $request, Closure $next): Response
    {
        // Ignorer les sous-domaines réservés système
        if ($this->subdomainService->isReserved($subdomain)) {
            return $next($request);
        }

        $funnel = $this->subdomainService->findBySubdomain($subdomain);

        if ($funnel) {
            if ($funnel->tenant?->isSuspended()) {
                return response()->view('funnel.suspended', [], 403);
            }

            // Vérifier que le funnel est actif
            if (!$funnel->isPubliclyAccessible()) {
                abort(404, 'Tunnel non disponible');
            }

            $request->attributes->set('funnel', $funnel);
            $request->attributes->set('funnel_access_type', 'subdomain');

            Log::info('Funnel accessed via subdomain', [
                'subdomain' => $subdomain,
                'funnel_id' => $funnel->id,
                'funnel_name' => $funnel->name,
            ]);

            return $next($request);
        }

        // Sous-domaine non trouvé comme funnel — vérifier si c'est un sous-domaine commercial
        // Dans ce cas le controller résoudra le funnel via le slug dans le path (/f/{slug})
        if (\App\Models\User::where('subdomain', $subdomain)->exists()) {
            return $next($request);
        }

        abort(404, 'Tunnel non trouvé');
    }
}
