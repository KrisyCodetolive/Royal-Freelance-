<?php

namespace App\Http\Controllers;

use App\Models\Funnel;
use App\Models\Lead;
use App\Models\Page;
use App\Services\PageBuilderService;
use App\Services\EmailService;
use App\Services\GeolocationService;
use App\Services\TrackingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FunnelController extends Controller
{
    public function __construct(
        protected PageBuilderService $builderService,
        protected EmailService $emailService,
        protected GeolocationService $geolocationService,
        protected TrackingService $trackingService,
        protected \App\Services\SubdomainService $subdomainService
    ) {
    }

    /**
     * Show the first page of a funnel
     */
    public function showRoot(Request $request, $subdomain = null)
    {
        // Récupérer le funnel par sous-domaine ou slug
        $funnel = $this->resolveFunnel($request, $subdomain);

        if (!$funnel || !$funnel->isActive()) {
            $host = $request->getHost();
            $baseDomain = config('app.subdomain_base', 'localhost');
            if (str_ends_with($host, ".{$baseDomain}")) {
                $extractedSubdomain = str_replace(".{$baseDomain}", '', $host);
                $commercialUser = \App\Models\User::where('subdomain', $extractedSubdomain)->first();
                if ($commercialUser) {
                    return response()->view('funnel.placeholder', ['commercial' => $commercialUser]);
                }
            }
            abort(404);
        }

        $page = $funnel->pages()
            ->active()
            ->ordered()
            ->first();

        if (!$page) {
            abort(404);
        }

        return $this->renderPage($funnel, $page);
    }

    /**
     * Show a specific page of a funnel
     */
    public function showPage(Request $request, $pageSlug, $subdomain = null)
    {
        // Récupérer le funnel par sous-domaine ou slug
        $funnel = $this->resolveFunnel($request, $subdomain);

        if (!$funnel || !$funnel->isActive()) {
            abort(404);
        }

        // Récupérer la page
        $page = $funnel->pages()
            ->where('slug', $pageSlug)
            ->active()
            ->first();

        if (!$page) {
            abort(404);
        }

        return $this->renderPage($funnel, $page);
    }

    /**
     * Handle form submission on a page
     */
    public function submit(Request $request, $pageSlug, $subdomain = null)
    {
        // Récupérer le funnel par sous-domaine ou slug
        $funnel = $this->resolveFunnel($request, $subdomain);

        if (!$funnel || !$funnel->isActive()) {
            abort(404);
        }

        // Récupérer la page
        $page = $funnel->pages()
            ->where('slug', $pageSlug)
            ->active()
            ->first();

        if (!$page) {
            abort(404);
        }

        return $this->handleFormSubmission($funnel, $page, $request);
    }

    /**
     * Show first page with explicit funnel slug (for /f/{funnelSlug} routes)
     */
    public function showRootWithSlug(Request $request, string $funnelSlugOrSubdomain, string $funnelSlug = null)
    {
        // Si on a 2 paramètres (subdomain + funnelSlug), utiliser le 2ème
        // Sinon, utiliser le 1er (pas de subdomain dans l'URL)
        $slug = $funnelSlug ?? $funnelSlugOrSubdomain;
        return $this->showRoot($request, $slug);
    }

    /**
     * Show specific page with explicit funnel slug (for /f/{funnelSlug}/{pageSlug} routes)
     */
    public function showPageWithSlug(Request $request, string $funnelSlugOrSubdomain, string $pageSlugOrFunnelSlug, string $pageSlug = null)
    {
        // Si on a 3 paramètres (subdomain + funnelSlug + pageSlug), utiliser les 2 derniers
        // Sinon, utiliser les 2 premiers (pas de subdomain)
        if ($pageSlug !== null) {
            return $this->showPage($request, $pageSlug, $pageSlugOrFunnelSlug);
        }
        return $this->showPage($request, $pageSlugOrFunnelSlug, $funnelSlugOrSubdomain);
    }

    /**
     * Handle form submission with explicit funnel slug (for /f/{funnelSlug}/{pageSlug}/submit routes)
     */
    public function submitWithSlug(Request $request, string $funnelSlugOrSubdomain, string $pageSlugOrFunnelSlug, string $pageSlug = null)
    {
        // Si on a 3 paramètres (subdomain + funnelSlug + pageSlug), utiliser les 2 derniers
        // Sinon, utiliser les 2 premiers (pas de subdomain)
        if ($pageSlug !== null) {
            return $this->submit($request, $pageSlug, $pageSlugOrFunnelSlug);
        }
        return $this->submit($request, $pageSlugOrFunnelSlug, $funnelSlugOrSubdomain);
    }

    /**
     * Render a funnel page
     */
    protected function renderPage(Funnel $funnel, Page $page)
    {
        // 1. Identification & Tracking via Service
        // Gère : Cookie visiteur, création Lead anonyme, GeoIP, Event PageView
        $lead = $this->trackingService->trackVisitor($funnel, $page);

        Funnel::withoutEvents(fn () => $funnel->increment('views_count'));

        // 2. Préparation des données de la vue
        $branding = $this->builderService->getPageBranding($page);
        $blocks = $page->blocks()->active()->ordered()->get();

        // 3. Récupérer les infos WhatsApp du commercial si présent
        $commercialWhatsApp = null;
        $commercialWhatsAppMessage = null;

        // On cherche le commercial par priorité : session, cookie, ou host actuel (très important pour les sous-domaines)
        $sessionRef = session('commercial_ref');
        $rlmRefSession = session('rlm_ref');
        $rlmRefCookie = request()->cookie('rlm_ref');

        $commercialId = $sessionRef ?? $rlmRefSession ?? $rlmRefCookie;

        \Log::info('📱 [WHATSAPP TRACK] Recherche commercialId', [
            'session_commercial_ref' => $sessionRef,
            'session_rlm_ref' => $rlmRefSession,
            'cookie_rlm_ref' => $rlmRefCookie,
            'final_commercial_id' => $commercialId
        ]);

        if (!$commercialId) {
            $extractedSubdomain = $this->subdomainService->extractSubdomainFromRequest();
            \Log::info('📱 [WHATSAPP TRACK] Tentative extraction sous-domaine', [
                'subdomain' => $extractedSubdomain,
                'full_host' => request()->getHost()
            ]);

            if ($extractedSubdomain) {
                $commercialUserBySub = \App\Models\User::where('subdomain', $extractedSubdomain)->first();
                if ($commercialUserBySub) {
                    $commercialId = $commercialUserBySub->id;
                    // Persister pour les futurs hits sur d'autres pages
                    session(['commercial_ref' => $commercialId]);
                    \Log::info('📱 [WHATSAPP TRACK] Commercial identifié via URL et sauvegardé en session', [
                        'id' => $commercialId,
                        'name' => $commercialUserBySub->name
                    ]);
                }
            }
        }

        if ($commercialId) {
            $commercialUser = \App\Models\User::find($commercialId);

            if ($commercialUser) {
                // Par défaut, on prend le numéro du profil
                $commercialWhatsApp = $commercialUser->whatsapp_number;
                \Log::info('📱 [WHATSAPP TRACK] Commercial identifié', [
                    'id' => $commercialUser->id,
                    'name' => $commercialUser->name,
                    'default_whatsapp' => $commercialWhatsApp
                ]);

                // On vérifie s'il y a une configuration spécifique pour ce funnel
                $commercialConfig = \DB::table('funnel_user')
                    ->where('user_id', $commercialId)
                    ->where('funnel_id', $funnel->id)
                    ->first();

                if ($commercialConfig) {
                    \Log::info('📱 [WHATSAPP TRACK] Configuration spécifique trouvée', [
                        'has_redirect' => !empty($commercialConfig->whatsapp_redirect),
                        'redirect_val' => $commercialConfig->whatsapp_redirect,
                        'has_custom_message' => !empty($commercialConfig->whatsapp_message)
                    ]);

                    if ($commercialConfig->whatsapp_redirect) {
                        $commercialWhatsApp = $commercialConfig->whatsapp_redirect;
                    }
                    if ($commercialConfig->whatsapp_message) {
                        $commercialWhatsAppMessage = $commercialConfig->whatsapp_message;
                    }
                } else {
                    \Log::info('📱 [WHATSAPP TRACK] Pas de config spécifique funnel_user');
                }
            }
        } else {
            \Log::info('📱 [WHATSAPP TRACK] Aucun commercial identifié pour ce rendu de page');
        }

        \Log::info('📱 [WHATSAPP TRACK] Valeurs finales injectées', [
            'commercialWhatsApp' => $commercialWhatsApp,
            'commercialWhatsAppMessage' => $commercialWhatsAppMessage,
            'funnel_default' => $funnel->whatsapp_url
        ]);

        return view('funnel.page', [
            'funnel' => $funnel,
            'page' => $page,
            'blocks' => $blocks,
            'branding' => $branding,
            'nextPage' => $page->getNextPage(),
            'previousPage' => $page->getPreviousPage(),
            'currentLead' => $lead,
            'commercialWhatsApp' => $commercialWhatsApp,
            'commercialWhatsAppMessage' => $commercialWhatsAppMessage,
            'commercial_ref' => session('commercial_ref'), // Passer explicitement la ref
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Subdomain-based Routes
    |--------------------------------------------------------------------------
    | Ces méthodes récupèrent le funnel depuis l'attribut de requête
    | (ajouté par le middleware ResolveFunnelFromSubdomain)
    */

    /**
     * Show first page via subdomain
     */
    public function showFromSubdomain(Request $request)
    {
        $funnel = $request->attributes->get('funnel');

        if (!$funnel) {
            abort(404);
        }

        $page = $funnel->pages()->active()->ordered()->first();

        if (!$page) {
            abort(404);
        }

        return $this->renderPage($funnel, $page);
    }

    /**
     * Show specific page via subdomain
     */
    public function showPageFromSubdomain(Request $request, string $pageSlug)
    {
        $funnel = $request->attributes->get('funnel');

        if (!$funnel) {
            abort(404);
        }

        $page = $funnel->pages()->where('slug', $pageSlug)->first();

        if (!$page || !$page->is_active) {
            abort(404);
        }

        return $this->renderPage($funnel, $page);
    }

    /**
     * Handle form submission via subdomain
     */
    public function submitFromSubdomain(Request $request, string $pageSlug)
    {
        $funnel = $request->attributes->get('funnel');

        if (!$funnel) {
            abort(404);
        }

        $page = $funnel->pages()->where('slug', $pageSlug)->first();

        if (!$page) {
            abort(404);
        }

        // Réutiliser la logique de submit standard
        return $this->handleFormSubmission($funnel, $page, $request);
    }

    /**
     * Logic partagée pour le traitement des soumissions de formulaire
     */
    protected function handleFormSubmission(Funnel $funnel, Page $page, Request $request)
    {
        Log::info('Funnel submit hit', [
            'funnel' => $funnel->slug,
            'page' => $page->slug,
            'method' => $request->method(),
            'access_type' => $request->attributes->get('funnel_access_type', 'classic'),
        ]);

        // 1. Validation dynamique basée sur les champs du bloc Formulaire
        $formBlock = $page->blocks()->where('type', 'form')->first();
        $fields = $formBlock->content['fields'] ?? [];

        $rules = [];
        foreach ($fields as $index => $field) {
            $fieldName = 'field_' . ($index + 1);
            if ($field['required'] ?? false) {
                $rules[$fieldName] = 'required';
            }
            if (($field['type'] ?? 'text') === 'email') {
                $rules[$fieldName] = ($rules[$fieldName] ?? '') . '|email';
            }
        }

        // Validation basique si aucun bloc form trouvé (sécurité)
        if (empty($rules)) {
            $rules = ['email' => 'required|email'];
        }

        $validatedData = $request->validate($rules);

        // 1.2 Mapping intelligent des données (field_X -> email, first_name, etc.)
        $formData = $request->all();
        $mappedData = [];
        $customFields = [];

        foreach ($fields as $index => $field) {
            $name = 'field_' . ($index + 1); // Correspond au nouveau nom dans Blade
            $type = $field['type'] ?? 'text';
            $label = $field['label'] ?? '';
            $lowLabel = \Illuminate\Support\Str::lower($label);
            $value = $request->input($name);
            $countryPrefix = $request->input($name . '_country');
            
            if ($value && $countryPrefix && !str_starts_with($value, '+')) {
                // Nettoyer le numéro si nécessaire (enlever le 0 initial si présent après indicatif)
                $cleanValue = preg_replace('/^0/', '', $value);
                $value = $countryPrefix . $cleanValue;
            }

            if ($value === null || $value === '')
                continue;

            $isEmailValue = filter_var($value, FILTER_VALIDATE_EMAIL);
            $nature = null;

            // A. Détection par Type (Priorité absolue)
            if ($type === 'email' || ($type === 'text' && $isEmailValue)) {
                $nature = 'email';
            } elseif ($type === 'tel' || $type === 'whatsapp') {
                $nature = 'phone';
            }

            // B. Détection par Labels (Si pas encore identifié ou pour les textes)
            if (!$nature) {
                if (str_contains($lowLabel, 'prénom') || str_contains($lowLabel, 'firstname')) {
                    $nature = 'first_name';
                } elseif (str_contains($lowLabel, 'nom de famille') || str_contains($lowLabel, 'lastname')) {
                    $nature = 'last_name';
                } elseif (str_contains($lowLabel, 'nom') || str_contains($lowLabel, 'name')) {
                    // Si on a déjà un first_name, on le met en last_name, sinon first_name (nom complet)
                    $nature = isset($mappedData['first_name']) ? 'last_name' : 'first_name';
                } elseif (str_contains($lowLabel, 'tél') || str_contains($lowLabel, 'phone') || str_contains($lowLabel, 'whatsapp') || str_contains($lowLabel, 'contact')) {
                    $nature = 'phone';
                } elseif (str_contains($lowLabel, 'pays') || str_contains($lowLabel, 'country')) {
                    $nature = 'country';
                } elseif (str_contains($lowLabel, 'ville') || str_contains($lowLabel, 'city')) {
                    $nature = 'city';
                } elseif (str_contains($lowLabel, 'entreprise') || str_contains($lowLabel, 'société') || str_contains($lowLabel, 'company')) {
                    $nature = 'company';
                }
            }

            // C. Attribution vers colonnes standards ou custom_fields
            if ($nature && !in_array($nature, ['company'])) {
                if (!isset($mappedData[$nature])) {
                    $mappedData[$nature] = $value;
                }
            } else {
                $customFields[$label] = $value;
            }
        }

        // Finaliser les data
        $submissionData = array_merge($formData, $mappedData);
        $submissionData['custom_fields'] = $customFields;

        \Log::info('📝 [FORM SUBMISSION] Début traitement formulaire', [
            'funnel_id' => $funnel->id,
            'page_id' => $page->id,
            'mapped_data' => $mappedData,
            'custom_fields' => $customFields,
            'session_commercial_ref' => session('commercial_ref'),
        ]);

        \Log::info('🍪 [DEBUG COOKIE] Vérification cookies', [
            'visitor_cookie' => $request->cookie('rlm_visitor') ? 'PRESENT' : 'ABSENT',
            'session_cookie_value' => $request->cookie(config('session.cookie')) ? 'PRESENT' : 'ABSENT',
            'all_cookies' => array_keys($request->cookies->all()),
        ]);

        // 2. Récupérer le commercial (Priorité : Input caché > Session)
        $broughtBy = null;
        $commercialId = $request->input('commercial_ref') ?? session('commercial_ref');

        if ($commercialId) {
            $broughtBy = \App\Models\User::find($commercialId);

            \Log::info('👤 [FORM SUBMISSION] Commercial identifié', [
                'commercial_id' => $commercialId,
                'source' => $request->has('commercial_ref') ? 'INPUT (Hidden Field)' : 'SESSION',
                'commercial_name' => $broughtBy?->name,
            ]);
        } else {
            \Log::warning('⚠️ [FORM SUBMISSION] Aucun commercial trouvé (Input ou Session)', [
                'session_data' => session()->all(),
            ]);
        }

        // 3. Création et Tracking du Lead via TrackingService
        $lead = $this->trackingService->createLeadFromForm($funnel, $submissionData, $broughtBy);

        \Log::info('✅ [FORM SUBMISSION] Lead créé ou mis à jour', [
            'lead_id' => $lead->id,
            'lead_email' => $lead->email,
            'lead_name' => $lead->getFullName(),
            'brought_by' => $lead->brought_by,
            'commercial_name' => $broughtBy?->name,
        ]);

        // 4. Enrichissement (Géolocalisation & Email)
        $this->geolocationService->updateLeadLocation($lead, $request->ip());

        $this->emailService->checkAndSubscribeLeadToSequences(
            $lead,
            'form_submit',
            [
                'page_id' => $page->id,
                'form_data' => $validatedData,
                'funnel_id' => $funnel->id,
            ]
        );

        // 4. Redirection intelligente
        $nextPage = $page->getNextPage();
        $successMessage = $formBlock->content['success_message'] ?? 'Inscription réussie !';

        if ($nextPage) {
            $nextUrl = url('/f/' . $funnel->slug . '/' . $nextPage->slug);
            return redirect($nextUrl)->with('success', $successMessage);
        }

        return back()->with('success', $formBlock->content['success_message'] ?? 'Merci, vos informations ont été reçues.');
    }

    /**
     * Résoudre le funnel depuis le sous-domaine ou le slug
     * Gère aussi les custom_slug des commerciaux
     */
    protected function resolveFunnel(Request $request, $subdomain = null)
    {
        $slugParam = $subdomain;

        $host = $request->getHost();
        $extractedSubdomain = $this->subdomainService->extractSubdomainFromRequest();

        \Log::info('🔍 [FUNNEL RESOLVE] Début résolution funnel', [
            'slug_param' => $slugParam,
            'url' => $request->fullUrl(),
            'host' => $host,
            'extracted_subdomain' => $extractedSubdomain,
            'session_commercial_ref' => session('commercial_ref'),
        ]);

        // 1. Si on a un sous-domaine dans l'URL
        if ($extractedSubdomain) {
            // Est-ce un tunnel qui a directement ce sous-domaine ?
            $funnel = Funnel::where('subdomain', $extractedSubdomain)->first();
            if ($funnel) {
                return $funnel;
            }

            // Est-ce un commercial qui a ce sous-domaine ?
            $commercialUser = \App\Models\User::where('subdomain', $extractedSubdomain)->first();
            if ($commercialUser && $slugParam) {
                // Chercher d'abord parmi les custom_slugs du commercial
                $commercialFunnel = \DB::table('funnel_user')
                    ->where('user_id', $commercialUser->id)
                    ->where('custom_slug', $slugParam)
                    ->where('is_active', true)
                    ->first();

                if ($commercialFunnel) {
                    $funnel = Funnel::find($commercialFunnel->funnel_id);
                    if ($funnel) {
                        $this->trackCommercialAttribution($request, $funnel, $commercialUser->id);
                        return $funnel;
                    }
                }

                // Sinon, chercher avec le slug global
                $funnel = Funnel::where('slug', $slugParam)->first();
                if ($funnel) {
                    // Attribuer si commercial est proprio ou a accès explicite
                    $hasAccess = \DB::table('funnel_user')
                        ->where('user_id', $commercialUser->id)
                        ->where('funnel_id', $funnel->id)
                        ->where('is_active', true)
                        ->exists();

                    if ($hasAccess || $funnel->assigned_to == $commercialUser->id) {
                        $this->trackCommercialAttribution($request, $funnel, $commercialUser->id);
                    }
                    return $funnel;
                }
            }
        }

        // 2. Si on n'a pas trouvé via le sous-domaine, traiter $slugParam comme un slug normal ou custom_slug
        if ($slugParam) {
            // A. Est-ce un sous-domaine de tunnel ? (Au cas où c'est passé en paramètre par erreur)
            $funnel = Funnel::where('subdomain', $slugParam)->first();
            if ($funnel) {
                return $funnel;
            }

            // B. Est-ce un custom_slug de commercial indépendant ?
            $commercialFunnel = \DB::table('funnel_user')
                ->where('custom_slug', $slugParam)
                ->where('is_active', true)
                ->first();

            if ($commercialFunnel) {
                $funnel = Funnel::find($commercialFunnel->funnel_id);
                if ($funnel) {
                    $this->trackCommercialAttribution($request, $funnel, $commercialFunnel->user_id);
                    return $funnel;
                }
            }

            // C. Est-ce un slug de tunnel normal ?
            $funnel = Funnel::where('slug', $slugParam)->first();

            if ($funnel && $request->has('ref')) {
                $this->trackCommercialAttribution($request, $funnel, $request->get('ref'));
            }

            if ($funnel) {
                return $funnel;
            }
        }

        return null;
    }

    /**
     * Tracker l'attribution du commercial pour le visiteur
     */
    protected function trackCommercialAttribution(Request $request, Funnel $funnel, $commercialId)
    {
        \Log::info('💾 [COMMERCIAL ATTRIBUTION] Stockage en session', [
            'commercial_id' => $commercialId,
            'funnel_id' => $funnel->id,
            'funnel_name' => $funnel->name,
            'session_id' => session()->getId(),
        ]);

        // Stocker dans la session pour attribution ultérieure des leads
        session([
            'commercial_ref' => $commercialId,
            'commercial_funnel_id' => $funnel->id,
        ]);

        \Log::info('✅ [COMMERCIAL ATTRIBUTION] Session mise à jour', [
            'commercial_ref' => session('commercial_ref'),
            'commercial_funnel_id' => session('commercial_funnel_id'),
        ]);

        // Incrémenter le compteur de vues pour ce commercial
        $updated = \DB::table('funnel_user')
            ->where('user_id', $commercialId)
            ->where('funnel_id', $funnel->id)
            ->increment('leads_count');

        \Log::info('📊 [COMMERCIAL ATTRIBUTION] Compteur incrémenté', [
            'commercial_id' => $commercialId,
            'funnel_id' => $funnel->id,
            'rows_updated' => $updated,
        ]);
    }
}
