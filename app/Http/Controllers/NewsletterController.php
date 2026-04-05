<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        // Route is protected by auth middleware, so auth()->user() is always present.
        $email = auth()->user()->email;

        // If the account email is already in the subscribers table, tell the
        // client — but treat it as a soft state, not an error (200, not 422).
        $existing = Subscriber::where('email', $email)->first();

        if ($existing) {
            if ($request->expectsJson()) {
                return response()->json([
                    'already_subscribed' => true,
                    'message'            => 'You are already subscribed!',
                ]);
            }

            return back()->with('newsletter_already_subscribed', true);
        }

        Subscriber::create(['email' => $email]);

        if ($request->expectsJson()) {
            return response()->json([
                'message'       => 'You have successfully subscribed!',
                'discount_code' => 'FIRST15',
            ]);
        }

        return back()
            ->with('newsletter_success', true)
            ->with('newsletter_discount', 'FIRST15');
    }
}
