<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;

class SocialLoginController extends Controller
{
    /**
     * Redirect the user to the provider authentication page.
     */
    public function redirect(string $provider): SymfonyRedirectResponse
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Obtain the user information from the provider.
     */
    public function callback(string $provider): RedirectResponse
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Authentication failed.');
        }

        $user = User::firstOrNew([
            'email' => $socialUser->getEmail(),
        ]);

        $isNewUser = ! $user->exists;

        $user->fill([
            'name' => $socialUser->getName() ?? $socialUser->getNickname(),
            'provider_id' => $socialUser->getId(),
            'provider_name' => $provider,
            'provider_token' => $socialUser->token,
            'email_verified_at' => now(),
        ]);

        if ($isNewUser) {
            $user->password = Str::random(32);
        }

        $user->save();

        Auth::login($user);

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
