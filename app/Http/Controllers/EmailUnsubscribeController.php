<?php

namespace App\Http\Controllers;

use App\Models\EmailSequenceEmailSend;
use Illuminate\Http\Request;

class EmailUnsubscribeController extends Controller
{
    public function unsubscribe(Request $request, EmailSequenceEmailSend $send)
    {
        $token = $request->query('token');

        if ($token !== md5($send->id . config('app.key'))) {
            abort(403);
        }

        $send->unsubscribe();

        return view('emails.unsubscribed');
    }

    public function trackOpen(Request $request, EmailSequenceEmailSend $send)
    {
        $token = $request->query('token');

        if ($token === md5($send->id . config('app.key'))) {
            $send->markAsOpened();
        }

        // Return a 1x1 transparent pixel
        return response(base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'), 200)
            ->header('Content-Type', 'image/gif');
    }
}
