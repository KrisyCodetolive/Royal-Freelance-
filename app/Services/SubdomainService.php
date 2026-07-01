<?php

namespace App\Services;

use App\Models\Funnel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class SubdomainService
{
    /**
     * Liste des sous-domaines réservés (système, admin, etc.)
     */
    protected array $reservedSubdomains = [
        'www',
        'app',
        'api',
        'admin',
        'dashboard',
        'panel',
        'mail',
        'email',
        'ftp',
        'sftp',
        'cdn',
        'assets',
        'static',
        'media',
        'images',
        'files',
        'blog',
        'news',
        'help',
        'support',
        'docs',
        'documentation',
        'dev',
        'staging',
        'test',
        'demo',
        'beta',
        'alpha',
        'preview',
        'sandbox',
        'auth',
        'login',
        'logout',
        'register',
        'signup',
        'signin',
        'account',
        'billing',
        'payment',
        'checkout',
        'cart',
        'shop',
        'store',
        'buy',
        'webhook',
        'webhooks',
        'callback',
        'oauth',
        'sso',
        'saml',
        'status',
        'health',
        'ping',
        'metrics',
        'analytics',
        'tracking',
        'filament',
        'nova',
        'horizon',
        'telescope',
        'pulse',
        'royal',
        'leadmagnet',
        'funnel',
        'funnels',
        'tunnel',
        'tunnels',
    ];

    /**
     * Domaine de base de l'application (configuré dans .env)
     */
    public function getBaseDomain(): string
    {
        return config('app.subdomain_base', parse_url(config('app.url'), PHP_URL_HOST) ?? 'localhost');
    }

    /**
     * Vérifie si un sous-domaine est valide (format)
     */
    public function isValidFormat(string $subdomain): bool
    {
        // Règles DNS : 
        // - 3 à 63 caractères
        // - Commence par une lettre
        // - Finit par une lettre ou un chiffre
        // - Contient seulement lettres, chiffres et tirets
        // - Pas de tirets consécutifs
        if (strlen($subdomain) < 3 || strlen($subdomain) > 63) {
            return false;
        }

        // Regex pour validation DNS
        return (bool) preg_match('/^[a-z][a-z0-9]*(-[a-z0-9]+)*$/', strtolower($subdomain));
    }

    /**
     * Vérifie si un sous-domaine est réservé
     */
    public function isReserved(string $subdomain): bool
    {
        return in_array(strtolower($subdomain), $this->reservedSubdomains);
    }

    /**
     * Vérifie si un sous-domaine est disponible
     */
    public function isAvailable(string $subdomain, ?int $excludeFunnelId = null): bool
    {
        $subdomain = strtolower($subdomain);

        // Vérifier si réservé
        if ($this->isReserved($subdomain)) {
            return false;
        }

        // Vérifier si déjà pris par un autre funnel
        $query = Funnel::where('subdomain', $subdomain);

        if ($excludeFunnelId) {
            $query->where('id', '!=', $excludeFunnelId);
        }

        return !$query->exists();
    }

    /**
     * Génère un sous-domaine unique à partir d'un nom
     */
    public function generateFromName(string $name, ?int $excludeFunnelId = null): string
    {
        $base = Str::slug($name);
        $base = preg_replace('/[^a-z0-9-]/', '', $base);

        // S'assurer qu'il commence par une lettre
        if (!preg_match('/^[a-z]/', $base)) {
            $base = 'f-' . $base;
        }

        // Limiter la longueur
        $base = Str::limit($base, 50, '');

        $subdomain = $base;
        $counter = 1;

        while (!$this->isAvailable($subdomain, $excludeFunnelId)) {
            $subdomain = $base . '-' . $counter;
            $counter++;

            if ($counter > 100) {
                // Fallback avec UUID court
                $subdomain = $base . '-' . Str::random(6);
                break;
            }
        }

        return strtolower($subdomain);
    }

    /**
     * Met à jour le sous-domaine d'un funnel
     */
    public function updateSubdomain(Funnel $funnel, string $subdomain): array
    {
        $subdomain = strtolower(trim($subdomain));

        // Validation du format
        if (!$this->isValidFormat($subdomain)) {
            return [
                'success' => false,
                'error' => 'Format invalide. Le sous-domaine doit contenir entre 3 et 63 caractères, commencer par une lettre et ne contenir que des lettres, chiffres et tirets.',
            ];
        }

        // Vérification disponibilité
        if (!$this->isAvailable($subdomain, $funnel->id)) {
            return [
                'success' => false,
                'error' => 'Ce sous-domaine est déjà utilisé ou réservé.',
            ];
        }

        $oldSubdomain = $funnel->subdomain;
        $funnel->subdomain = $subdomain;
        $funnel->save();

        // Invalider le cache
        $this->clearCache($oldSubdomain);
        $this->clearCache($subdomain);

        return [
            'success' => true,
            'subdomain' => $subdomain,
            'url' => $this->getFunnelUrl($funnel),
        ];
    }

    /**
     * Trouve un funnel par son sous-domaine
     */
    public function findBySubdomain(string $subdomain): ?Funnel
    {
        $subdomain = strtolower($subdomain);

        return Cache::remember(
            "funnel_subdomain_{$subdomain}",
            now()->addHours(1),
            fn() => Funnel::where('subdomain', $subdomain)->with('tenant')->first()
        );
    }

    /**
     * Trouve un funnel par son domaine personnalisé
     */
    public function findByCustomDomain(string $domain): ?Funnel
    {
        $domain = strtolower($domain);

        return Cache::remember(
            "funnel_domain_{$domain}",
            now()->addHours(1),
            fn() => Funnel::where('custom_domain', $domain)
                ->where('domain_verified_at', '!=', null)
                ->with('tenant')
                ->first()
        );
    }

    /**
     * Génère l'URL complète d'un funnel
     */
    public function getFunnelUrl(Funnel $funnel, ?string $pageSlug = null): string
    {
        $scheme = config('app.env') === 'production' ? 'https' : 'http';
        $baseDomain = $this->getBaseDomain();
        $parsedAppUrl = parse_url(config('app.url'));
        $port = isset($parsedAppUrl['port']) ? ':' . $parsedAppUrl['port'] : '';

        // Priorité : Custom Domain > Subdomain > Slug classique
        if ($funnel->custom_domain && $funnel->domain_verified_at) {
            $url = "{$scheme}://{$funnel->custom_domain}";
        } elseif ($funnel->subdomain) {
            $url = "{$scheme}://{$funnel->subdomain}.{$baseDomain}{$port}";
        } else {
            // Fallback vers l'URL classique
            return route('funnel.root', ['funnelSlug' => $funnel->slug]);
        }

        // Ajouter le slug de page si spécifié
        if ($pageSlug) {
            $url .= "/{$pageSlug}";
        }

        return $url;
    }

    /**
     * Génère l'URL d'une page spécifique du funnel
     */
    public function getPageUrl(Funnel $funnel, string $pageSlug): string
    {
        return $this->getFunnelUrl($funnel, $pageSlug);
    }

    /**
     * Extrait le sous-domaine de la requête courante
     */
    public function extractSubdomainFromRequest(): ?string
    {
        $host = request()->getHost();
        $baseDomain = $this->getBaseDomain();

        // Si c'est exactement le domaine de base, pas de sous-domaine
        if ($host === $baseDomain || $host === "www.{$baseDomain}") {
            return null;
        }

        // Vérifier si c'est un sous-domaine du domaine de base
        if (Str::endsWith($host, ".{$baseDomain}")) {
            return Str::beforeLast($host, ".{$baseDomain}");
        }

        return null;
    }

    /**
     * Nettoie le cache pour un sous-domaine
     */
    public function clearCache(?string $subdomain): void
    {
        if ($subdomain) {
            Cache::forget("funnel_subdomain_{$subdomain}");
        }
    }

    /**
     * Configure le domaine personnalisé (génère les instructions DNS)
     */
    public function setupCustomDomain(Funnel $funnel, string $domain): array
    {
        $domain = strtolower(trim($domain));

        // Validation basique du format de domaine
        if (!filter_var("http://{$domain}", FILTER_VALIDATE_URL)) {
            return [
                'success' => false,
                'error' => 'Format de domaine invalide.',
            ];
        }

        // Sauvegarder le domaine (non vérifié)
        $funnel->custom_domain = $domain;
        $funnel->domain_verified_at = null;
        $funnel->ssl_active = false;
        $funnel->save();

        // Générer les instructions DNS
        $appIp = config('app.server_ip', '0.0.0.0'); // À configurer dans .env
        $verificationCname = "verify.{$this->getBaseDomain()}";

        return [
            'success' => true,
            'domain' => $domain,
            'dns_records' => [
                [
                    'type' => 'A',
                    'host' => '@',
                    'value' => $appIp,
                    'description' => 'Enregistrement A principal',
                ],
                [
                    'type' => 'CNAME',
                    'host' => 'www',
                    'value' => $domain,
                    'description' => 'Redirection www',
                ],
                [
                    'type' => 'TXT',
                    'host' => '_royal-verify',
                    'value' => "royal-leadmagnet-verify={$funnel->uuid}",
                    'description' => 'Vérification de propriété',
                ],
            ],
            'instructions' => "Ajoutez ces enregistrements DNS chez votre registrar. La vérification peut prendre jusqu'à 48h.",
        ];
    }

    /**
     * Vérifie le domaine personnalisé (vérification DNS)
     */
    public function verifyCustomDomain(Funnel $funnel): array
    {
        if (!$funnel->custom_domain) {
            return ['success' => false, 'error' => 'Aucun domaine configuré.'];
        }

        $domain = $funnel->custom_domain;

        // Vérifier l'enregistrement TXT de vérification
        $txtRecords = @dns_get_record("_royal-verify.{$domain}", DNS_TXT);

        $verified = false;
        if ($txtRecords) {
            foreach ($txtRecords as $record) {
                if (isset($record['txt']) && $record['txt'] === "royal-leadmagnet-verify={$funnel->uuid}") {
                    $verified = true;
                    break;
                }
            }
        }

        if ($verified) {
            $funnel->domain_verified_at = now();
            $funnel->save();

            Cache::forget("funnel_domain_{$domain}");

            return [
                'success' => true,
                'message' => 'Domaine vérifié avec succès ! ',
            ];
        }

        return [
            'success' => false,
            'error' => 'Enregistrement TXT de vérification non trouvé. Veuillez patienter ou vérifier vos DNS.',
        ];
    }

    /**
     * Retourne les règles de validation pour le champ subdomain (Filament)
     */
    public function getValidationRules(?int $excludeFunnelId = null): array
    {
        return [
            'min:3',
            'max:63',
            'regex:/^[a-z][a-z0-9]*(-[a-z0-9]+)*$/',
            function (string $attribute, mixed $value, \Closure $fail) use ($excludeFunnelId) {
                if (!$this->isAvailable($value, $excludeFunnelId)) {
                    $fail('Ce sous-domaine est déjà utilisé ou réservé.');
                }
            },
        ];
    }
}
