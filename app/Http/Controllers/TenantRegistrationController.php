<?php

namespace App\Http\Controllers;

use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;

class TenantRegistrationController extends Controller
{
    public function __construct(protected TenantService $tenantService)
    {
    }

    /**
     * Show the self-service tenant registration form.
     */
    public function create()
    {
        return view('tenant.register');
    }

    /**
     * Create a new tenant with its admin, then log the admin in.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'admin_name' => ['required', 'string', 'max:255'],
            'admin_email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $result = $this->tenantService->setupWithAdmin(
            tenantData: ['name' => $data['company_name']],
            adminData: [
                'name' => $data['admin_name'],
                'email' => $data['admin_email'],
                'password' => $data['password'],
            ],
        );

        // Plan Gratuit par défaut à la création (stub en attendant le Module 2 — Billing).
        $result['tenant']->update(['plan_slug' => 'free']);

        Auth::login($result['admin']);

        return redirect('/admin');
    }
}
