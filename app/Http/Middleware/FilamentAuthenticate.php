<?php

namespace App\Http\Middleware;

use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate as BaseAuthenticate;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Model;

/**
 * Extends Filament's Authenticate middleware to allow authenticated-but-unverified
 * users through to the email-verification prompt (so they can enter their OTP).
 * All other panel routes still require canAccessPanel() === true.
 */
class FilamentAuthenticate extends BaseAuthenticate
{
    protected function authenticate($request, array $guards): void
    {
        $guard = Filament::auth();

        if (! $guard->check()) {
            $this->unauthenticated($request, $guards);
        }

        $this->auth->shouldUse(Filament::getAuthGuard());

        /** @var Model $user */
        $user = $guard->user();

        $panel = Filament::getCurrentOrDefaultPanel();

        // Allow unverified users to reach the email-verification prompt
        // so they can submit their OTP without being blocked.
        if ($panel->hasEmailVerification()) {
            $promptRouteName = $panel->getEmailVerificationPromptRouteName();

            if ($request->routeIs($promptRouteName)) {
                return;
            }
        }

        abort_if(
            $user instanceof FilamentUser
                ? (! $user->canAccessPanel($panel))
                : (config('app.env') !== 'local'),
            403,
        );
    }
}
