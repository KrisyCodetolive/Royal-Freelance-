<?php

namespace App\Http\Controllers;

use App\Models\PushSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PushSubscriptionController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'endpoint'   => 'required|string',
            'public_key' => 'required|string',
            'auth_token' => 'required|string',
        ]);

        PushSubscription::updateOrCreate(
            [
                'user_id'  => Auth::id(),
                'endpoint' => $validated['endpoint'],
            ],
            [
                'public_key' => $validated['public_key'],
                'auth_token' => $validated['auth_token'],
            ]
        );

        return response()->json(['success' => true]);
    }

    public function destroy(Request $request)
    {
        $validated = $request->validate([
            'endpoint' => 'required|string',
        ]);

        PushSubscription::where('user_id', Auth::id())
            ->where('endpoint', $validated['endpoint'])
            ->delete();

        return response()->json(['success' => true]);
    }

    public function vapidPublicKey()
    {
        return response()->json([
            'public_key' => config('services.vapid.public_key'),
        ]);
    }
}
