<?php

namespace App\Services;

use App\Enums\FunnelStatus;
use App\Enums\PageType;
use App\Models\Block;
use App\Models\CommercialGroup;
use App\Models\Funnel;
use App\Models\Page;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Str;

class FunnelService
{
    public function __construct(
        protected SubdomainService $subdomainService
    ) {
    }

    /**
     * Create a new funnel (Onboarding: Create Tunnel)
     */
    public function create(array $data, ?User $creator = null): Funnel
    {
        $tenant = $creator?->tenant ?? Tenant::find($data['tenant_id']);

        // Générer automatiquement un sous-domaine unique si non fourni
        $subdomain = $data['subdomain'] ?? null;
        if (empty($subdomain)) {
            $subdomain = $this->subdomainService->generateFromName($data['name']);
        } else {
            // Valider et nettoyer le sous-domaine fourni
            $subdomain = strtolower(trim($subdomain));
            if (!$this->subdomainService->isAvailable($subdomain)) {
                $subdomain = $this->subdomainService->generateFromName($data['name']);
            }
        }

        $funnel = Funnel::create([
            'uuid' => Str::uuid(),
            'tenant_id' => $tenant->id,
            'offer_id' => $data['offer_id'] ?? null,
            'assigned_to' => $data['assigned_to'] ?? $creator?->id,
            'name' => $data['name'],
            'slug' => $data['slug'] ?? Str::slug($data['name']),
            'subdomain' => $subdomain,
            'description' => $data['description'] ?? null,
            'status' => FunnelStatus::DRAFT,
            'template' => $data['template'] ?? null,
            'whatsapp_url' => $data['whatsapp_url'] ?? null,
            'whatsapp_message' => $data['whatsapp_message'] ?? null,
            'payment_url' => $data['payment_url'] ?? null,
            'branding' => $data['branding'] ?? null,
            'settings' => $data['settings'] ?? null,
        ]);

        // Create default pages if template specified
        if (!empty($data['template'])) {
            $this->createPagesFromTemplate($funnel, $data['template']);
        }

        return $funnel;
    }

    /**
     * Create pages from template
     */
    public function createPagesFromTemplate(Funnel $funnel, string $template): void
    {
        $templates = [
            'formation' => [
                ['type' => PageType::CAPTURE, 'title' => 'Inscription'],
                ['type' => PageType::THANK_YOU, 'title' => 'Bienvenue'],
                ['type' => PageType::PRESENTATION, 'title' => 'Présentation'],
                ['type' => PageType::MODALITIES, 'title' => 'Modalités'],
            ],
            'livre' => [
                ['type' => PageType::CAPTURE, 'title' => 'Télécharger le livre'],
                ['type' => PageType::THANK_YOU, 'title' => 'Merci'],
                ['type' => PageType::PRESENTATION, 'title' => 'À propos du livre'],
            ],
            'mlm' => [
                ['type' => PageType::CAPTURE, 'title' => 'Rejoindre l\'opportunité'],
                ['type' => PageType::THANK_YOU, 'title' => 'Bienvenue dans l\'équipe'],
                ['type' => PageType::PRESENTATION, 'title' => 'Présentation du business'],
                ['type' => PageType::TESTIMONIALS, 'title' => 'Témoignages'],
                ['type' => PageType::MODALITIES, 'title' => 'Comment démarrer'],
            ],
        ];

        $pages = $templates[$template] ?? $templates['formation'];

        foreach ($pages as $order => $pageData) {
            $this->createPage($funnel, [
                'type' => $pageData['type'],
                'title' => $pageData['title'],
                'sort_order' => $order,
            ]);
        }
    }

    /**
     * Create a page for a funnel
     */
    public function createPage(Funnel $funnel, array $data): Page
    {
        return Page::create([
            'uuid' => Str::uuid(),
            'funnel_id' => $funnel->id,
            'type' => $data['type'],
            'title' => $data['title'],
            'slug' => $data['slug'] ?? Str::slug($data['title']),
            'description' => $data['description'] ?? null,
            'sort_order' => $data['sort_order'] ?? $funnel->pages()->count(),
            'is_active' => $data['is_active'] ?? true,
            'is_required' => $data['is_required'] ?? ($data['type'] === PageType::CAPTURE),
        ]);
    }

    /**
     * Create a block for a page
     */
    public function createBlock(Page $page, array $data): Block
    {
        return Block::create([
            'uuid' => Str::uuid(),
            'page_id' => $page->id,
            'type' => $data['type'],
            'content' => $data['content'] ?? [],
            'settings' => $data['settings'] ?? [],
            'styles' => $data['styles'] ?? [],
            'sort_order' => $data['sort_order'] ?? $page->blocks()->count(),
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    /**
     * Duplicate a funnel with all its pages and blocks
     */
    public function duplicate(Funnel $funnel, ?string $newName = null): Funnel
    {
        return $funnel->duplicate($newName);
    }

    /**
     * Publish a funnel (make it active)
     */
    public function publish(Funnel $funnel): Funnel
    {
        // Validate funnel has required pages
        $this->validateForPublishing($funnel);

        $funnel->update([
            'status' => FunnelStatus::ACTIVE,
            'published_at' => now(),
        ]);

        return $funnel->fresh();
    }

    /**
     * Pause a funnel
     */
    public function pause(Funnel $funnel): Funnel
    {
        $funnel->update(['status' => FunnelStatus::PAUSED]);
        return $funnel->fresh();
    }

    /**
     * Archive a funnel
     */
    public function archive(Funnel $funnel): Funnel
    {
        $funnel->update(['status' => FunnelStatus::ARCHIVED]);
        return $funnel->fresh();
    }

    /**
     * Validate funnel is ready for publishing
     */
    public function validateForPublishing(Funnel $funnel): array
    {
        $errors = [];

        // Must have at least one page
        if ($funnel->pages()->count() === 0) {
            $errors[] = 'Le tunnel doit avoir au moins une page.';
        }

        // Must have a capture page
        if (!$funnel->pages()->where('type', PageType::CAPTURE)->exists()) {
            $errors[] = 'Le tunnel doit avoir une page de capture.';
        }

        // Capture page must have a form block
        $capturePage = $funnel->pages()->where('type', PageType::CAPTURE)->first();
        if ($capturePage && !$capturePage->blocks()->where('type', 'form')->exists()) {
            $errors[] = 'La page de capture doit avoir un formulaire.';
        }

        if (!empty($errors)) {
            throw new \InvalidArgumentException(implode(' ', $errors));
        }

        return $errors;
    }

    /**
     * Assign funnel to a commercial group
     */
    public function assignToGroup(Funnel $funnel, CommercialGroup $group, bool $canCustomize = false): void
    {
        $funnel->assignToGroup($group, $canCustomize);
    }

    /**
     * Assign funnel to multiple groups
     */
    public function assignToGroups(Funnel $funnel, array $groupIds, bool $canCustomize = false): void
    {
        $syncData = [];
        foreach ($groupIds as $groupId) {
            $syncData[$groupId] = ['can_customize' => $canCustomize];
        }

        $funnel->commercialGroups()->sync($syncData);
    }

    /**
     * Get funnel analytics
     */
    public function getAnalytics(Funnel $funnel, ?string $period = 'month'): array
    {
        $startDate = match ($period) {
            'day' => now()->startOfDay(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'year' => now()->startOfYear(),
            default => now()->startOfMonth(),
        };

        $leads = $funnel->leads()->where('created_at', '>=', $startDate);

        return [
            'total_leads' => $leads->count(),
            'hot_leads' => (clone $leads)->hot()->count(),
            'conversions' => (clone $leads)->converted()->count(),
            'conversion_rate' => $leads->count() > 0
                ? round((clone $leads)->converted()->count() / $leads->count() * 100, 2)
                : 0,
            'page_views' => $funnel->events()->where('type', 'page_view')->where('created_at', '>=', $startDate)->count(),
            'form_submissions' => $funnel->events()->where('type', 'form_submit')->where('created_at', '>=', $startDate)->count(),
            'whatsapp_clicks' => $funnel->events()->where('type', 'whatsapp_click')->where('created_at', '>=', $startDate)->count(),
        ];
    }
}
