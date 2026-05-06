<?php

namespace App\Http\Controllers;

use App\Services\AlertService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Alert;

class CommercialAlertsController extends Controller
{
    public function __construct(
        protected AlertService $alertService
    ) {
    }

    /**
     * Liste toutes les alertes du commercial
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Alert::where('user_id', $user->id)
            ->with(['lead:id,first_name,last_name,email,phone,score,funnel_id', 'lead.funnel:id,name']);

        // Filtre par statut
        if ($request->filled('status')) {
            if ($request->status === 'unread') {
                $query->where('is_read', false);
            } elseif ($request->status === 'read') {
                $query->where('is_read', true);
            }
        }

        // Filtre par type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $alerts = $query->orderByDesc('created_at')
            ->paginate(20);

        // Marquer automatiquement toutes les non-lues comme lues à l'ouverture de la page
        if (!$request->ajax() && !$request->filled('status')) {
            $this->alertService->markAllAsRead($user);
        }

        return view('commercial.alerts.index', [
            'user' => $user,
            'alerts' => $alerts,
            'filters' => $request->only(['status', 'type']),
            'summary' => $this->alertService->getSummary($user),
        ]);
    }

    /**
     * Marquer une alerte comme lue (AJAX)
     */
    public function markAsRead(Alert $alert)
    {
        $user = Auth::user();

        // Vérifier que l'alerte appartient au commercial
        if ($alert->user_id !== $user->id) {
            abort(403);
        }

        $this->alertService->markAsRead($alert);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Alerte marquée comme lue',
            ]);
        }

        return redirect()->back()->with('success', 'Alerte marquée comme lue');
    }

    /**
     * Marquer toutes les alertes comme lues (AJAX)
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        $count = $this->alertService->markAllAsRead($user);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "$count alertes marquées comme lues",
                'count' => $count,
            ]);
        }

        return redirect()->back()->with('success', "$count alertes marquées comme lues");
    }

    /**
     * Supprimer une alerte
     */
    public function destroy(Alert $alert)
    {
        $user = Auth::user();

        // Vérifier que l'alerte appartient au commercial
        if ($alert->user_id !== $user->id) {
            abort(403);
        }

        $alert->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Alerte supprimée',
            ]);
        }

        return redirect()->back()->with('success', 'Alerte supprimée');
    }

    /**
     * Get unread count (AJAX for polling)
     */
    public function getUnreadCount()
    {
        $user = Auth::user();
        
        $count = Alert::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'count' => $count,
        ]);
    }
}
