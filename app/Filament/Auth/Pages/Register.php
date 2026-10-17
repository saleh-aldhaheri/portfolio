<?php

namespace App\Filament\Auth\Pages;

use App\Models\User;
use App\Services\EmailVerificationCodeService;
use Filament\Auth\Pages\Register as BaseRegister;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;

class Register extends BaseRegister
{
    /**
     * Override to send an OTP instead of a signed URL verification link.
     */
    protected function sendEmailVerificationNotification(Model $user): void
    {
        if (! $user instanceof MustVerifyEmail) {
            return;
        }

        if ($user->hasVerifiedEmail()) {
            return;
        }

        /** @var User $user */
        app(EmailVerificationCodeService::class)->issue($user);
    }
}
