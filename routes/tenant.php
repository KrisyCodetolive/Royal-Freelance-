<?php

use App\Http\Controllers\TenantRegistrationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes Multi-Tenant (Module 1 — Phase 3 SaaS)
|--------------------------------------------------------------------------
|
| Fichier dédié aux routes liées au multi-tenant. La gestion des invitations
| (rejoindre le bon tenant) vit dans le panel Filament (TenantInvitationResource,
| routée automatiquement sous /admin) — voir ROADMAP_SAAS_PHASE3.md.
|
*/

// Inscription self-service — crée un nouveau tenant + son admin (Owner)
// en 2 étapes : identification, puis choix du plan.
Route::middleware('guest')->group(function () {
    Route::get('/demarrer', [TenantRegistrationController::class, 'create'])->name('tenant.register');
    Route::post('/demarrer', [TenantRegistrationController::class, 'storeIdentity'])->name('tenant.register.store');
    Route::get('/demarrer/plan', [TenantRegistrationController::class, 'choosePlan'])->name('tenant.register.plan');
    Route::post('/demarrer/plan', [TenantRegistrationController::class, 'store'])->name('tenant.register.plan.store');
});
