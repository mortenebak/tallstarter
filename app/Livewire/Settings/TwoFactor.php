<?php

namespace App\Livewire\Settings;

use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Component;
use PragmaRX\Google2FA\Google2FA;

#[Layout('components.layouts.app.frontend')]
class TwoFactor extends Component
{
    public bool $showingQrCode = false;

    public bool $showingRecoveryCodes = false;

    public string $code = '';

    public array $recoveryCodes = [];

    /**
     * Enable two-factor authentication.
     */
    public function enableTwoFactorAuthentication(): void
    {
        $user = auth()->user();
        $google2fa = new Google2FA;

        // Generate a new secret
        $secret = $google2fa->generateSecretKey();

        // Store it temporarily
        $user->two_factor_secret = encrypt($secret);
        $user->save();

        $this->showingQrCode = true;
        $this->showingRecoveryCodes = false;
    }

    /**
     * Confirm two-factor authentication.
     */
    public function confirmTwoFactorAuthentication(): void
    {
        $this->validate([
            'code' => 'required|string',
        ]);

        $user = auth()->user();
        $google2fa = new Google2FA;

        $secret = decrypt($user->two_factor_secret);

        if (! $google2fa->verifyKey($secret, $this->code)) {
            $this->addError('code', 'The provided code was invalid.');

            return;
        }

        // Mark 2FA as confirmed
        $user->two_factor_confirmed_at = now();

        // Generate recovery codes
        $recoveryCodes = Collection::times(8, function () {
            return strtoupper(bin2hex(random_bytes(5)));
        })->all();

        $user->two_factor_recovery_codes = encrypt(json_encode($recoveryCodes));
        $user->two_factor_recovery_codes_viewed_at = now();
        $user->save();

        $this->recoveryCodes = $recoveryCodes;
        $this->showingQrCode = false;
        $this->showingRecoveryCodes = true;
        $this->code = '';

        $this->dispatch('alert', [
            'type' => 'success',
            'message' => 'Two-factor authentication has been enabled.',
        ]);
    }

    /**
     * Show recovery codes.
     */
    public function showRecoveryCodes(): void
    {
        $user = auth()->user();

        // Only allow showing recovery codes if they haven't been viewed yet
        if ($user->two_factor_recovery_codes_viewed_at !== null) {
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'Recovery codes can only be viewed once for security reasons. Please regenerate new codes if needed.',
            ]);

            return;
        }

        $this->recoveryCodes = $user->recoveryCodes();
        $this->showingRecoveryCodes = true;
        
        // Mark codes as viewed
        $user->two_factor_recovery_codes_viewed_at = now();
        $user->save();
    }

    /**
     * Regenerate recovery codes.
     */
    public function regenerateRecoveryCodes(): void
    {
        $user = auth()->user();

        $recoveryCodes = Collection::times(8, function () {
            return strtoupper(bin2hex(random_bytes(5)));
        })->all();

        $user->two_factor_recovery_codes = encrypt(json_encode($recoveryCodes));
        $user->two_factor_recovery_codes_viewed_at = now();
        $user->save();

        $this->recoveryCodes = $recoveryCodes;
        $this->showingRecoveryCodes = true;

        $this->dispatch('alert', [
            'type' => 'success',
            'message' => 'New recovery codes have been generated.',
        ]);
    }

    /**
     * Disable two-factor authentication.
     */
    public function disableTwoFactorAuthentication(): void
    {
        $user = auth()->user();

        $user->two_factor_secret = null;
        $user->two_factor_recovery_codes = null;
        $user->two_factor_confirmed_at = null;
        $user->two_factor_recovery_codes_viewed_at = null;
        $user->save();

        $this->showingQrCode = false;
        $this->showingRecoveryCodes = false;

        $this->dispatch('alert', [
            'type' => 'success',
            'message' => 'Two-factor authentication has been disabled.',
        ]);
    }

    /**
     * Get the QR code URL for the user.
     */
    public function getQrCodeUrlProperty(): ?string
    {
        if (! $this->showingQrCode) {
            return null;
        }

        $user = auth()->user();
        $google2fa = new Google2FA;

        $secret = decrypt($user->two_factor_secret);

        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        return 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data='.urlencode($qrCodeUrl);
    }

    /**
     * Get the manual entry secret.
     */
    public function getManualEntrySecretProperty(): ?string
    {
        if (! $this->showingQrCode) {
            return null;
        }

        $user = auth()->user();

        return decrypt($user->two_factor_secret);
    }

    public function render()
    {
        return view('livewire.settings.two-factor');
    }
}
