<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Vérifier les leads inactifs quotidiennement à 9h00
Schedule::command('leads:check-inactive')->dailyAt('09:00');

// Traiter les séquences email actives (toutes les minutes en dev, hourly en prod)
Schedule::command('email:process-sequences')->everyMinute()->withoutOverlapping();
