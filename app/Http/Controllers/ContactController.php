<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function submit(Request $request)
    {
        // 1. Validate the incoming request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|max:2000',
        ]);

        // 2. Process the data (Log it for now, later you can use Mail::to())
        // Log::info('New Contact Message:', $validated);
        
        // 3. Return to the form with a success flash message
        return back()->with('success', 'Thank you for reaching out! Your message has been sent successfully.');
    }
}