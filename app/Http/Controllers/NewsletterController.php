<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $request->validate(
            [
                'email' => [
                    'required',
                    'email',
                    'max:255',
                    Rule::unique('subscribers', 'email'),
                ],
            ],
            [
                'email.unique' => 'This email address is already subscribed to our newsletter.',
            ]
        );

        Subscriber::create(['email' => $request->email]);

        return response()->json([
            'message'       => 'You have successfully subscribed!',
            'discount_code' => 'FIRST15',
        ]);
    }
}
