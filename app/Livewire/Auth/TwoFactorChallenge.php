<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use PragmaRX\Google2FA\Google2FA;

#[Layout('components.layouts.auth')]
class TwoFactorChallenge extends Component
{
    #[Validate('required|string')]
    public string $code = '';

    public bool $recovery = false;

    /**
     * Challenge the user for their two-factor authentication code.
     */
    public function challenge(): void
    {
        $this->validate();

        $this->ensureIsNotRateLimited();

        $user = Auth::getProvider()->retrieveById(
            session('login.id')
        );

        if ($this->recovery) {
            $this->challengeUsingRecoveryCode($user);
        } else {
            $this->challengeUsingCode($user);
        }

        RateLimiter::clear($this->throttleKey());

        Auth::login($user, session('login.remember', false));

        session()->forget(['login.id', 'login.remember']);
        session()->regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }

    /**
     * Challenge using the authenticator app code.
     */
    protected function challengeUsingCode($user): void
    {
        $google2fa = new Google2FA();
        $secret = decrypt($user->two_factor_secret);

        if (! $google2fa->verifyKey($secret, $this->code)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'code' => __('The provided two-factor authentication code was invalid.'),
            ]);
        }
    }

    /**
     * Challenge using a recovery code.
     */
    protected function challengeUsingRecoveryCode($user): void
    {
        $recoveryCodes = $user->recoveryCodes();

        if (! in_array($this->code, $recoveryCodes)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'code' => __('The provided recovery code was invalid.'),
            ]);
        }

        $user->replaceRecoveryCode($this->code);
    }

    /**
     * Toggle between code and recovery mode.
     */
    public function toggleRecovery(): void
    {
        $this->recovery = ! $this->recovery;
        $this->code = '';
        $this->resetErrorBag();
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'code' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate('two-factor|'.request()->ip());
    }

    public function render()
    {
        return view('livewire.auth.two-factor-challenge');
    }
}
