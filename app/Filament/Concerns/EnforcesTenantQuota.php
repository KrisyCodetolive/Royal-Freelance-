<?php

namespace App\Filament\Concerns;

use App\Services\QuotaService;
use Filament\Notifications\Notification;
use Filament\Support\Exceptions\Halt;

/**
 * À utiliser dans une page Filament CreateRecord pour bloquer la création
 * quand le tenant courant a atteint la limite de son plan (Module 3 — Quotas).
 */
trait EnforcesTenantQuota
{
    protected function haltIfQuotaReached(string $quotaKey, string $label): void
    {
        $tenant = auth()->user()?->tenant;

        if (!$tenant) {
            return;
        }

        if (!app(QuotaService::class)->hasReachedLimit($tenant, $quotaKey)) {
            return;
        }

        Notification::make()
            ->title("Limite de {$label} atteinte")
            ->body("Votre plan actuel ne permet pas d'en créer davantage. Passez à un plan supérieur pour continuer.")
            ->danger()
            ->send();

        throw new Halt();
    }
}
