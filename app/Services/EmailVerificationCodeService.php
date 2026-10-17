<?php

namespace App\Services;

use App\Mail\OtpMail;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Random\RandomException;

class EmailVerificationCodeService
{
    public const int TTL_MINUTES = 10;

    public const int MAX_ATTEMPTS = 5;

    /**
     * Issue a fresh 6-digit OTP for the given user and mail it.
     *
     * @throws RandomException
     */
    public function issue(User $user): void
    {
        $code = $this->randomCode();

        Cache::put($this->key($user), ['code' => $code, 'attempts' => 0], now()->addMinutes(self::TTL_MINUTES));

        Mail::to($user->email)->send(new OtpMail(code: $code, purpose: 'admin-email-verification'));
    }

    /**
     * Resend a fresh code, subject to rate limiting.
     * Returns true when the mail was sent, false when throttled.
     *
     * @throws RandomException
     */
    public function resend(User $user): bool
    {
        $limiterKey = 'email-verify-resend:'.$user->id;

        // 1 per 60s
        if (RateLimiter::tooManyAttempts($limiterKey.':60', maxAttempts: 1)) {
            return false;
        }

        // 3 per 10min
        if (RateLimiter::tooManyAttempts($limiterKey.':600', maxAttempts: 3)) {
            return false;
        }

        RateLimiter::hit($limiterKey.':60', decaySeconds: 60);
        RateLimiter::hit($limiterKey.':600', decaySeconds: 600);

        $this->issue($user);

        return true;
    }

    /**
     * Verify the submitted code for the given user.
     * Returns true when valid, false when invalid or expired.
     * Increments attempt counter and forgets the entry on success.
     */
    public function verify(User $user, string $code): bool
    {
        $payload = Cache::get($this->key($user));

        if ($payload === null) {
            return false;
        }

        if ($payload['attempts'] >= self::MAX_ATTEMPTS) {
            Cache::forget($this->key($user));

            return false;
        }

        if (! hash_equals((string) $payload['code'], $code)) {
            $payload['attempts']++;
            Cache::put($this->key($user), $payload, now()->addMinutes(self::TTL_MINUTES));

            return false;
        }

        Cache::forget($this->key($user));

        return true;
    }

    /**
     * Return true when the user has exceeded the attempt limit.
     */
    public function isLockedOut(User $user): bool
    {
        $payload = Cache::get($this->key($user));

        return $payload !== null && $payload['attempts'] >= self::MAX_ATTEMPTS;
    }

    private function key(User $user): string
    {
        return "email-verify-otp:{$user->id}";
    }

    /**
     * @throws RandomException
     */
    private function randomCode(): string
    {
        return (string) random_int(100000, 999999);
    }
}
