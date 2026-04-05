<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleSocialiteController extends Controller
{
    /**
     * Redirect the user to the Google OAuth consent screen.
     */
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle the callback from Google.
     * Finds or creates a user, then logs them in.
     */
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Google authentication failed. Please try again.']);
        }

        // 1. Check if a user with this Google ID already exists → just log them in.
        $user = User::where('google_id', $googleUser->getId())->first();

        if ($user) {
            $user->update(['google_token' => $googleUser->token]);
            Auth::login($user);
            return redirect()->intended(route('home'));
        }

        // 2. A user with this email already exists (registered with password).
        //    Link the Google account to it so they can use either method.
        $user = User::where('email', $googleUser->getEmail())->first();

        if ($user) {
            $user->update([
                'google_id'    => $googleUser->getId(),
                'google_token' => $googleUser->token,
                'avatar'       => $googleUser->getAvatar(),
            ]);
            Auth::login($user);
            return redirect()->intended(route('home'));
        }

        // 3. Brand-new user — create their account.
        $user = User::create([
            'name'         => $googleUser->getName(),
            'email'        => $googleUser->getEmail(),
            'google_id'    => $googleUser->getId(),
            'google_token' => $googleUser->token,
            'avatar'       => $googleUser->getAvatar(),
        ]);

        // Google has already verified this email address.
        $user->email_verified_at = now();
        $user->role = 'user';
        $user->save();

        Auth::login($user);

        return redirect()->intended(route('home'));
    }
}
