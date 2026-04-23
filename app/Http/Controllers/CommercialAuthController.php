<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;

class CommercialAuthController extends Controller
{
    /**
     * Show the login view.
     */
    public function login()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'is_active' => true], $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            if ($user->hasRole('commercial')) {
                return redirect()->intended(route('commercial.dashboard'));
            }

            if ($user->hasRole(['super_admin', 'admin', 'manager'])) {
                return redirect()->intended('/admin');
            }

            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'Identifiants incorrects ou compte inactif.',
        ])->onlyInput('email');
    }

    /**
     * Show the registration view.
     */
    public function register()
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'shop_name' => ['required', 'string', 'max:255'],
        ]);

        // Default tenant (assuming single tenant for now or first one)
        // In a multi-tenant app, this might need subdomain resolution or selection.
        // For Royal LeadPro, we likely use a default tenant or resolving from domain.
        $tenant = Tenant::first();

        // Fail-safe if no tenant exists (should not happen in prod with seeder)
        if (!$tenant) {
            return back()->withErrors(['email' => 'Configuration système invalide (Tenant manquant).']);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'shop_name' => $request->shop_name,
            'tenant_id' => $tenant->id,
            'is_active' => true, // Auto-activate or false if approval needed
        ]);

        $user->assignRole('commercial');

        Auth::login($user);

        return redirect(route('commercial.dashboard'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
