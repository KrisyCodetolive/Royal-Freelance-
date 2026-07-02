<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use Illuminate\Console\Command;

class ExpireSubscriptions extends Command
{
    protected $signature = 'subscriptions:expire';

    protected $description = 'Repasse en statut "expired" les abonnements actifs dont la date de fin est dépassée';

    public function handle(): int
    {
        $count = Subscription::query()
            ->where('status', 'active')
            ->whereNotNull('ends_at')
            ->where('ends_at', '<', now())
            ->update(['status' => 'expired']);

        $this->info("{$count} abonnement(s) expiré(s).");

        return self::SUCCESS;
    }
}
