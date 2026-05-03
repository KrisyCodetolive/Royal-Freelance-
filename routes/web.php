<?php

use App\Http\Controllers\CommercialAuthController;
use App\Http\Controllers\CommercialAlertsController;
use App\Http\Controllers\CommercialDashboardController;
use App\Http\Controllers\FunnelController;
use App\Livewire\PageBuilder\PageBuilder;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Middleware\RoleMiddleware;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
// Landing page — fallback sans contrainte de domaine (local dev + prod)
Route::get('/', fn() => view('landing'))->name('home');
Route::get('/presentation-client', fn() => view('presentation.client'))->name('presentation.client.fallback');
Route::get('/presentation-client/sequences-email', fn() => view('presentation.sequences-email'))->name('presentation.sequences-email');
Route::get('/presentation-client/email-builder', fn() => view('presentation.email-builder'))->name('presentation.email-builder');

// Guest Routes (CGU, Privacy, Support)
Route::view('/cgu', 'guest.terms')->name('guest.terms');
Route::view('/confidentialite', 'guest.privacy')->name('guest.privacy');
Route::view('/support', 'guest.support')->name('guest.support');

Route::middleware('guest')->group(function () {
    Route::get('/login', [CommercialAuthController::class, 'login'])->name('login');
    Route::post('/login', [CommercialAuthController::class, 'authenticate'])->name('login.post');
    Route::get('/register', [CommercialAuthController::class, 'register'])->name('register');
    Route::post('/register', [CommercialAuthController::class, 'store'])->name('register.post');
});

Route::post('/logout', [CommercialAuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Commercial Dashboard Routes
|--------------------------------------------------------------------------
*/
Route::prefix('commercial')
    ->middleware(['auth', RoleMiddleware::class . ':commercial'])
    ->group(function () {
        Route::get('/', [CommercialDashboardController::class, 'index'])
            ->name('commercial.dashboard');

        Route::get('/tunnels', [CommercialDashboardController::class, 'funnels'])
            ->name('commercial.funnels');

        Route::get('/tunnels/{funnel}/configure', [CommercialDashboardController::class, 'configureFunnel'])
            ->name('commercial.funnel.configure');

        Route::post('/tunnels/{funnel}/configure', [CommercialDashboardController::class, 'saveFunnelConfig'])
            ->name('commercial.funnel.configure.save');

        Route::get('/leads', [CommercialDashboardController::class, 'leads'])
            ->name('commercial.leads');

        Route::get('/leads/kanban', [CommercialDashboardController::class, 'kanban'])
            ->name('commercial.leads.kanban');

        Route::post('/leads/update-status', [CommercialDashboardController::class, 'updateLeadStatus'])
            ->name('commercial.leads.update-status');

        Route::get('/profile', [CommercialDashboardController::class, 'profile'])
            ->name('commercial.profile');

        Route::post('/profile', [CommercialDashboardController::class, 'updateProfile'])
            ->name('commercial.profile.update');

        // Alerts routes
        Route::get('/alerts', [CommercialAlertsController::class, 'index'])
            ->name('commercial.alerts');

        Route::post('/alerts/{alert}/read', [CommercialAlertsController::class, 'markAsRead'])
            ->name('commercial.alerts.read');

        Route::post('/alerts/mark-all-read', [CommercialAlertsController::class, 'markAllAsRead'])
            ->name('commercial.alerts.mark-all-read');

        Route::delete('/alerts/{alert}', [CommercialAlertsController::class, 'destroy'])
            ->name('commercial.alerts.destroy');

        Route::get('/alerts/unread-count', [CommercialAlertsController::class, 'getUnreadCount'])
            ->name('commercial.alerts.unread-count');
    });

// Page Builder Routes (protected by Filament auth)
Route::group(['middleware' => ['auth']], function () {
    Route::get('/page-builder/{model}/{modelId}', PageBuilder::class)->name('page-builder.edit');
    Route::get('/page-builder/preview/{model}/{modelId}', [PageBuilder::class, 'preview'])->name('page-builder.preview');
});

// Email Builder Routes
Route::group(['middleware' => ['auth']], function () {
    Route::get('/email-builder/{email}', \App\Livewire\EmailBuilder\EmailBuilder::class)->name('email-builder.edit');
});

/*
|--------------------------------------------------------------------------
| Public Funnel Routes (Domain & Subdomain Handling)
|--------------------------------------------------------------------------
*/

// Route pour les sous-domaines wildcard (*.royalleadpro.com)
// Cette route DOIT être avant la route générique
$baseDomain = config('app.subdomain_base', 'localhost');
Route::domain('{subdomain}.' . $baseDomain)->group(function () {
    Route::get('/', [FunnelController::class, 'showRoot']);
    Route::get('/{pageSlug}', [FunnelController::class, 'showPage']);
    Route::post('/{pageSlug}/submit', [FunnelController::class, 'submit']);

    // Support aussi le pattern /f/{slug} sur sous-domaine
    // Utilise des paramètres simples pour permettre à resolveFunnel() de gérer les custom_slug
    Route::prefix('f')->group(function () {
        Route::get('/{funnelSlug}', [FunnelController::class, 'showRootWithSlug']);
        Route::get('/{funnelSlug}/{pageSlug}', [FunnelController::class, 'showPageWithSlug']);
        Route::post('/{funnelSlug}/{pageSlug}/submit', [FunnelController::class, 'submitWithSlug']);
    });
});

// Route pour le domaine principal uniquement (landing page)
Route::domain($baseDomain)->group(function () {
    Route::get('/presentation-client', function () {
        return view('presentation.client');
    })->name('presentation.client');

    Route::get('/', function () {
        return view('landing');
    });
});

// Route principale pour les URLs avec domaine personnalisé
Route::domain('{domain}')->group(function () {
    Route::get('/', [FunnelController::class, 'showRoot']);
    Route::get('/{pageSlug}', [FunnelController::class, 'showPage']);
    Route::post('/{pageSlug}/submit', [FunnelController::class, 'submit']);
});

// Route de repli pour les accès via sous-dossier (si pas de domaine)
// ex: royal-lead.com/f/mon-tunnel/page-vente  
// Utilise des paramètres simples pour supporter les custom_slug
Route::prefix('f')->group(function () {

    // Racine du funnel
    Route::get('/{funnelSlug}', [FunnelController::class, 'showRootWithSlug'])
        ->name('funnel.root');

    // Page spécifique du funnel
    Route::get('/{funnelSlug}/{pageSlug}', [FunnelController::class, 'showPageWithSlug'])
        ->name('funnel.page');

    // Soumission de formulaire
    Route::post('/{funnelSlug}/{pageSlug}/submit', [FunnelController::class, 'submitWithSlug'])
        ->name('funnel.submit');
});

/*
|--------------------------------------------------------------------------
| API Routes pour le suivi (Tracking)
|--------------------------------------------------------------------------
*/
Route::prefix('api/tracking')->group(function () {
    Route::post('/page-engagement', [\App\Http\Controllers\TrackingApiController::class, 'trackPageEngagement']);
    Route::post('/update-lead-data', [\App\Http\Controllers\TrackingApiController::class, 'updateLeadData']);
    Route::post('/cta-click', [\App\Http\Controllers\TrackingApiController::class, 'trackCtaClick']);
});

Route::get('/email/unsubscribe/{send}', [\App\Http\Controllers\EmailUnsubscribeController::class, 'unsubscribe'])
    ->name('email.unsubscribe');

Route::get('/email/track/open/{send}', [\App\Http\Controllers\EmailUnsubscribeController::class, 'trackOpen'])
    ->name('email.tracking.open');
