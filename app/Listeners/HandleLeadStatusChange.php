<?php

namespace App\Listeners;

use App\Enums\AlertType;
use App\Events\LeadStatusChanged;
use App\Services\AlertService;

class HandleLeadStatusChange
{
    protected AlertService $alertService;

    public function __construct(AlertService $alertService)
    {
        $this->alertService = $alertService;
    }

    /**
     * Handle the event.
     */
    public function handle(LeadStatusChanged $event): void
    {
        $lead = $event->lead;
        $oldStatus = $event->oldStatus;
        $newStatus = $event->newStatus;

        // Check and create status change alerts
        $this->alertService->checkStatusChangeAlerts($lead, $oldStatus, $newStatus);

        // Log the status change
        activity()
            ->performedOn($lead)
            ->withProperties([
                'old_status' => $oldStatus->value,
                'new_status' => $newStatus->value,
            ])
            ->log('Statut du lead modifié');
    }
}
