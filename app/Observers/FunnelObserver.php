<?php

namespace App\Observers;

use App\Enums\FunnelStatus;
use App\Models\Funnel;
use App\Services\SubdomainService;

class FunnelObserver
{
    public function __construct(
        protected SubdomainService $subdomainService
    ) {
    }

    /**
     * Handle the Funnel "creating" event.
     * Génère automatiquement un sous-domaine si non défini.
     */
    public function creating(Funnel $funnel): void
    {
        // Générer un sous-domaine unique si non fourni
        if (empty($funnel->subdomain) && !empty($funnel->name)) {
            $funnel->subdomain = $this->subdomainService->generateFromName($funnel->name);
        }
    }

    /**
     * Handle the Funnel "created" event.
     */
    public function created(Funnel $funnel): void
    {
        // Log activity
        activity()
            ->performedOn($funnel)
            ->causedBy(auth()->user())
            ->withProperties([
                'name' => $funnel->name,
                'subdomain' => $funnel->subdomain,
            ])
            ->log('Tunnel créé');
    }

    /**
     * Handle the Funnel "updated" event.
     */
    public function updated(Funnel $funnel): void
    {
        // Log status changes
        if ($funnel->isDirty('status')) {
            $original  = $funnel->getOriginal('status');
            $oldStatus = $original instanceof FunnelStatus ? $original : FunnelStatus::tryFrom($original);
            $newStatus = $funnel->status;

            activity()
                ->performedOn($funnel)
                ->causedBy(auth()->user())
                ->withProperties([
                    'old_status' => $oldStatus?->value,
                    'new_status' => $newStatus->value,
                ])
                ->log('Statut du tunnel modifié');
        }

        // Log when published
        if ($funnel->isDirty('published_at') && $funnel->published_at) {
            activity()
                ->performedOn($funnel)
                ->causedBy(auth()->user())
                ->log('Tunnel publié');
        }
    }

    /**
     * Handle the Funnel "deleted" event.
     */
    public function deleted(Funnel $funnel): void
    {
        // Libère le sous-domaine : l'index unique en base ignore le soft-delete,
        // donc sans ça ce sous-domaine resterait bloqué à vie même une fois le tunnel supprimé.
        if ($funnel->subdomain) {
            $funnel->update(['subdomain' => null]);
        }

        activity()
            ->performedOn($funnel)
            ->causedBy(auth()->user())
            ->log('Tunnel supprimé');
    }
}
