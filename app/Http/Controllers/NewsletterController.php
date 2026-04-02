<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
        ]);

        // Use firstOrCreate to prevent duplicate entries and SQL errors
        Subscriber::firstOrCreate(
            ['email' => $request->email]
        );

        return back()->with('success', 'Thanks for subscribing to the Shelf-e newsletter!');
    }
}
