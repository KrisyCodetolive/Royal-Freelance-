<?php

namespace App\Services;

use App\Enums\FunnelStatus;
use App\Enums\OfferType;
use App\Models\CommercialGroup;
use App\Models\Funnel;
use App\Models\Offer;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Collection;

class OnboardingService
{
    protected TenantService $tenantService;
    protected FunnelService $funnelService;
    protected CommercialService $commercialService;

    public function __construct(
        TenantService $tenantService,
        FunnelService $funnelService,
        CommercialService $commercialService
    ) {
        $this->tenantService = $tenantService;
        $this->funnelService = $funnelService;
        $this->commercialService = $commercialService;
    }

    /**
     * Get onboarding status for a tenant
     */
    public function getStatus(Tenant $tenant): array
    {
        return [
            'steps' => $this->getSteps($tenant),
            'current_step' => $this->getCurrentStep($tenant),
            'completion_percentage' => $this->getCompletionPercentage($tenant),
            'is_complete' => $this->isComplete($tenant),
        ];
    }

    /**
     * Get all onboarding steps with their status
     */
    public function getSteps(Tenant $tenant): array
    {
        return [
            [
                'id' => 'branding',
                'title' => 'Personnaliser votre espace',
                'description' => 'Configurez votre logo, couleurs et informations',
                'icon' => 'heroicon-o-paint-brush',
                'completed' => $this->isStepComplete($tenant, 'branding'),
                'route' => 'filament.admin.pages.settings',
            ],
            [
                'id' => 'offer',
                'title' => 'Créer votre première offre',
                'description' => 'Définissez le produit ou service à promouvoir',
                'icon' => 'heroicon-o-gift',
                'completed' => $this->isStepComplete($tenant, 'offer'),
                'route' => 'filament.admin.resources.offers.create',
            ],
            [
                'id' => 'funnel',
                'title' => 'Créer un tunnel de vente',
                'description' => 'Construisez votre premier tunnel de capture',
                'icon' => 'heroicon-o-funnel',
                'completed' => $this->isStepComplete($tenant, 'funnel'),
                'route' => 'filament.admin.resources.funnels.create',
            ],
            [
                'id' => 'pages',
                'title' => 'Configurer les pages',
                'description' => 'Ajoutez du contenu à vos pages de tunnel',
                'icon' => 'heroicon-o-document-text',
                'completed' => $this->isStepComplete($tenant, 'pages'),
                'route' => null, // Dynamic based on funnel
            ],
            [
                'id' => 'publish',
                'title' => 'Publier le tunnel',
                'description' => 'Rendez votre tunnel accessible au public',
                'icon' => 'heroicon-o-rocket-launch',
                'completed' => $this->isStepComplete($tenant, 'publish'),
                'route' => null,
            ],
            [
                'id' => 'team',
                'title' => 'Inviter votre équipe',
                'description' => 'Ajoutez des commerciaux et créez des groupes',
                'icon' => 'heroicon-o-user-group',
                'completed' => $this->isStepComplete($tenant, 'team'),
                'optional' => true,
                'route' => 'filament.admin.resources.users.create',
            ],
            [
                'id' => 'assign',
                'title' => 'Assigner les tunnels',
                'description' => 'Attribuez les tunnels aux groupes commerciaux',
                'icon' => 'heroicon-o-link',
                'completed' => $this->isStepComplete($tenant, 'assign'),
                'optional' => true,
                'route' => 'filament.admin.resources.commercial-groups.index',
            ],
        ];
    }

    /**
     * Check if a specific step is complete
     */
    public function isStepComplete(Tenant $tenant, string $step): bool
    {
        return match ($step) {
            'branding' => $this->isBrandingComplete($tenant),
            'offer' => $tenant->offers()->exists(),
            'funnel' => $tenant->funnels()->exists(),
            'pages' => $tenant->funnels()->whereHas('pages.blocks')->exists(),
            'publish' => $tenant->funnels()->where('status', FunnelStatus::ACTIVE)->exists(),
            'team' => $tenant->commercials()->exists(),
            'assign' => $tenant->commercialGroups()->whereHas('funnels')->exists(),
            default => false,
        };
    }

    /**
     * Check if branding is configured
     */
    protected function isBrandingComplete(Tenant $tenant): bool
    {
        return !empty($tenant->logo) ||
            !empty($tenant->branding['primary_color']) ||
            !empty($tenant->email);
    }

    /**
     * Get current step to complete
     */
    public function getCurrentStep(Tenant $tenant): ?array
    {
        $steps = $this->getSteps($tenant);

        foreach ($steps as $step) {
            if (!$step['completed'] && !($step['optional'] ?? false)) {
                return $step;
            }
        }

        // Check optional steps
        foreach ($steps as $step) {
            if (!$step['completed'] && ($step['optional'] ?? false)) {
                return $step;
            }
        }

        return null;
    }

    /**
     * Get completion percentage
     */
    public function getCompletionPercentage(Tenant $tenant): int
    {
        $steps = $this->getSteps($tenant);
        $requiredSteps = array_filter($steps, fn($s) => !($s['optional'] ?? false));
        $completedRequired = array_filter($requiredSteps, fn($s) => $s['completed']);

        return count($requiredSteps) > 0
            ? (int) round((count($completedRequired) / count($requiredSteps)) * 100)
            : 100;
    }

    /**
     * Check if onboarding is complete
     */
    public function isComplete(Tenant $tenant): bool
    {
        return $this->getCompletionPercentage($tenant) === 100;
    }

    /**
     * Quick setup: Create offer, funnel, and pages in one step
     */
    public function quickSetup(Tenant $tenant, array $data): array
    {
        // 1. Create Offer
        $offer = Offer::create([
            'tenant_id' => $tenant->id,
            'name' => $data['offer_name'],
            'type' => OfferType::tryFrom($data['offer_type']) ?? OfferType::FORMATION,
            'description' => $data['offer_description'] ?? null,
            'price' => $data['offer_price'] ?? 0,
            'currency' => $data['currency'] ?? 'XOF',
            'is_active' => true,
        ]);

        // 2. Create Funnel with template
        $funnel = $this->funnelService->create([
            'tenant_id' => $tenant->id,
            'offer_id' => $offer->id,
            'name' => $data['funnel_name'] ?? $offer->name,
            'template' => $data['template'] ?? 'formation',
            'whatsapp_url' => $data['whatsapp_url'] ?? null,
            'whatsapp_message' => $data['whatsapp_message'] ?? 'Bonjour, je suis intéressé par ' . $offer->name,
        ]);

        return [
            'offer' => $offer,
            'funnel' => $funnel,
            'pages' => $funnel->pages,
        ];
    }

    /**
     * Setup commercial team
     */
    public function setupTeam(Tenant $tenant, array $data): array
    {
        // 1. Create Commercial Group
        $group = $this->commercialService->createGroup($tenant, [
            'name' => $data['group_name'] ?? 'Équipe commerciale',
            'description' => $data['group_description'] ?? null,
        ]);

        // 2. Create Commercials
        $commercials = [];
        foreach ($data['commercials'] ?? [] as $commercialData) {
            $commercial = $this->commercialService->createCommercial($tenant, [
                'name' => $commercialData['name'],
                'email' => $commercialData['email'],
                'shop_name' => $commercialData['shop_name'] ?? null,
                'phone' => $commercialData['phone'] ?? null,
                'whatsapp_number' => $commercialData['whatsapp_number'] ?? $commercialData['phone'] ?? null,
            ]);

            // Add to group
            $this->commercialService->addToGroup($commercial, $group);

            $commercials[] = $commercial;
        }

        // 3. Assign funnels to group
        if (!empty($data['funnel_ids'])) {
            $this->commercialService->assignFunnelsToGroup(
                $group,
                $data['funnel_ids'],
                $data['can_customize'] ?? false
            );
        }

        return [
            'group' => $group,
            'commercials' => $commercials,
        ];
    }

    /**
     * Get setup wizard data for frontend
     */
    public function getWizardData(Tenant $tenant): array
    {
        return [
            'status' => $this->getStatus($tenant),
            'tenant' => [
                'id' => $tenant->id,
                'name' => $tenant->name,
                'branding' => $tenant->branding,
            ],
            'offer_types' => collect(OfferType::cases())->map(fn($t) => [
                'value' => $t->value,
                'label' => $t->label(),
                'icon' => $t->icon(),
            ]),
            'templates' => [
                ['value' => 'formation', 'label' => 'Formation', 'description' => 'Tunnel pour vendre une formation'],
                ['value' => 'livre', 'label' => 'Livre/Ebook', 'description' => 'Tunnel pour vendre un livre'],
                ['value' => 'mlm', 'label' => 'MLM/Opportunité', 'description' => 'Tunnel pour présenter une opportunité'],
            ],
            'suggestions' => $this->getSuggestions($tenant),
        ];
    }

    /**
     * Get suggestions based on current state
     */
    public function getSuggestions(Tenant $tenant): array
    {
        $suggestions = [];

        // Suggest completing branding
        if (!$this->isStepComplete($tenant, 'branding')) {
            $suggestions[] = [
                'type' => 'action',
                'title' => 'Personnalisez votre espace',
                'description' => 'Ajoutez votre logo et vos couleurs pour une expérience cohérente.',
                'action' => 'complete_branding',
            ];
        }

        // Suggest creating first funnel
        if (!$this->isStepComplete($tenant, 'funnel')) {
            $suggestions[] = [
                'type' => 'tip',
                'title' => 'Prêt à capturer des leads ?',
                'description' => 'Créez votre premier tunnel en moins de 5 minutes avec nos modèles prédéfinis.',
                'action' => 'create_funnel',
            ];
        }

        // Suggest publishing
        $draftFunnels = $tenant->funnels()->where('status', FunnelStatus::DRAFT)->count();
        if ($draftFunnels > 0) {
            $suggestions[] = [
                'type' => 'reminder',
                'title' => "{$draftFunnels} tunnel(s) en brouillon",
                'description' => 'N\'oubliez pas de publier vos tunnels pour commencer à capturer des leads.',
                'action' => 'publish_funnels',
            ];
        }

        // Suggest adding team
        if ($this->isStepComplete($tenant, 'publish') && !$this->isStepComplete($tenant, 'team')) {
            $suggestions[] = [
                'type' => 'growth',
                'title' => 'Multipliez vos leads',
                'description' => 'Ajoutez des commerciaux pour qu\'ils partagent vos tunnels avec leur propre lien.',
                'action' => 'add_team',
            ];
        }

        return $suggestions;
    }

    /**
     * Mark onboarding as dismissed
     */
    public function dismiss(Tenant $tenant): void
    {
        $settings = $tenant->settings ?? [];
        $settings['onboarding_dismissed'] = true;
        $settings['onboarding_dismissed_at'] = now()->toISOString();

        $tenant->update(['settings' => $settings]);
    }

    /**
     * Check if onboarding was dismissed
     */
    public function isDismissed(Tenant $tenant): bool
    {
        return $tenant->settings['onboarding_dismissed'] ?? false;
    }
}
