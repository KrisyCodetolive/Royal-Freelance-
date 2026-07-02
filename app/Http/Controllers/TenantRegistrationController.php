<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Subscription;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

/**
 * Inscription self-service en 2 étapes : identification (étape 1) puis
 * choix du plan (étape 2). Les données de l'étape 1 transitent par la
 * session (mot de passe déjà haché, jamais stocké en clair) le temps que
 * l'utilisateur choisisse son plan.
 */
class TenantRegistrationController extends Controller
{
    private const SESSION_KEY = 'tenant_registration';

    public function __construct(protected TenantService $tenantService)
    {
    }

    /**
     * Étape 1 — formulaire d'identification (entreprise + admin).
     */
    public function create()
    {
        return view('tenant.register');
    }

    /**
     * Étape 1 — valide l'identification et passe à l'étape 2 (choix du plan).
     */
    public function storeIdentity(Request $request)
    {
        $data = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'admin_name' => ['required', 'string', 'max:255'],
            'admin_email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        session([self::SESSION_KEY => [
            'company_name' => $data['company_name'],
            'admin_name' => $data['admin_name'],
            'admin_email' => $data['admin_email'],
            'password_hash' => Hash::make($data['password']),
        ]]);

        return redirect()->route('tenant.register.plan');
    }

    /**
     * Étape 2 — choix du plan.
     */
    public function choosePlan()
    {
        if (!session()->has(self::SESSION_KEY)) {
            return redirect()->route('tenant.register')
                ->withErrors(['company_name' => "Merci de renseigner d'abord vos informations."]);
        }

        return view('tenant.register-plan', [
            'plans' => Plan::where('is_active', true)->orderBy('price_monthly')->get(),
        ]);
    }

    /**
     * Étape 2 — crée le tenant + l'admin (Owner) avec le plan choisi.
     *
     * Pas de passerelle de paiement : l'activation du plan payant est
     * "mockée" (immédiate), mais les quotas/restrictions réels du plan
     * s'appliquent tout de suite (décision 2026-07-02).
     */
    public function store(Request $request)
    {
        $identity = session(self::SESSION_KEY);

        if (!$identity) {
            return redirect()->route('tenant.register');
        }

        $data = $request->validate([
            'plan' => ['required', 'string', Rule::exists('plans', 'slug')->where('is_active', true)],
            'cycle' => ['required', Rule::in(['monthly', 'yearly'])],
        ]);

        $plan = Plan::where('slug', $data['plan'])->firstOrFail();

        $result = $this->tenantService->setupWithAdmin(
            tenantData: ['name' => $identity['company_name']],
            adminData: [
                'name' => $identity['admin_name'],
                'email' => $identity['admin_email'],
                'password' => $identity['password_hash'],
            ],
        );

        // La personne qui crée le workspace en est l'Owner (droits d'Admin
        // + gestion de l'abonnement) — voir royal-leadpro-phase3.html.
        $result['admin']->assignRole('owner');

        $result['tenant']->subscriptions()->create([
            'plan_id' => $plan->id,
            'cycle' => $data['cycle'],
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => Subscription::computeEndsAt($plan, $data['cycle']),
        ]);

        session()->forget(self::SESSION_KEY);

        Auth::login($result['admin']);

        return redirect('/admin');
    }
}
