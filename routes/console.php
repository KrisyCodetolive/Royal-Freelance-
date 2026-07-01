<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Vérifier les leads inactifs quotidiennement à 9h00
Schedule::command('leads:check-inactive')->dailyAt('09:00');

// Traiter les séquences email actives (toutes les minutes en prod via cron)
Schedule::command('email:process-sequences')->everyMinute()->before(function () {
    \Illuminate\Support\Facades\Log::info('[CRON] email:process-sequences démarré', ['at' => now()->toDateTimeString()]);
})->after(function () {
    \Illuminate\Support\Facades\Log::info('[CRON] email:process-sequences terminé', ['at' => now()->toDateTimeString()]);
});
