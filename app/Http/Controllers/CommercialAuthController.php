<?php

namespace App\Http\Controllers;

use App\Models\TenantInvitation;
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

            if ($user->hasRole(['super_admin', 'admin', 'owner', 'manager', 'editor', 'viewer'])) {
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
    public function register(Request $request)
    {
        return view('auth.register', [
            'invitationToken' => $request->query('invitation'),
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * L'inscription d'un commercial nécessite un lien d'invitation valide
     * (généré par un admin depuis son tenant) : il n'y a pas de rattachement
     * implicite au "premier tenant de la base".
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'shop_name' => ['required', 'string', 'max:255'],
            'invitation' => ['required', 'string'],
        ]);

        $invitation = TenantInvitation::where('token', $request->input('invitation'))->first();

        if (!$invitation || !$invitation->isValid()) {
            return back()
                ->withErrors(['invitation' => "Ce lien d'invitation est invalide ou a expiré."])
                ->onlyInput('name', 'email', 'shop_name');
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'shop_name' => $request->shop_name,
            'tenant_id' => $invitation->tenant_id,
            'is_active' => true,
        ]);

        $user->assignRole($invitation->role);

        $invitation->markUsedBy($user);

        Auth::login($user);

        // Un invité peut recevoir le rôle admin/editor/viewer (panel Filament)
        // ou commercial (espace dédié) : la redirection dépend du rôle assigné.
        if ($user->isAdmin() || $user->isEditor() || $user->isViewer()) {
            return redirect('/admin');
        }

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
