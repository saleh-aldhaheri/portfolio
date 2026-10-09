<?php

namespace App\Services;

use App\Mail\OtpMail;
use App\Models\ContactMessage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Random\RandomException;

class ContactService
{
    public const int TTL_MINUTES = 10;

    /**
     * Issue a fresh verification code for the given payload and email it.
     *
     * @param  array{name: string, email: string, phone: string|null, company: string|null, message: string, visitor_id: int, verified_email: string}  $payload
     * @return array{token: string, code: string}
     */
    public function issue(array $payload): array
    {
        $token = (string) Str::uuid();
        $code = $this->randomCode();

        Cache::put($this->key($token), [...$payload, 'code' => $code], now()->addMinutes(self::TTL_MINUTES));

        Mail::to($payload['verified_email'])->send(new OtpMail($code));

        return ['token' => $token, 'code' => $code];
    }

    /**
     * Re-issue a code for an existing pending token, or return null when expired.
     */
    public function resend(string $token): ?string
    {
        $payload = Cache::get($this->key($token));

        if ($payload === null) {
            return null;
        }

        $code = $this->randomCode();
        $payload['code'] = $code;

        Cache::put($this->key($token), $payload, now()->addMinutes(self::TTL_MINUTES));

        Mail::to($payload['verified_email'])->send(new OtpMail($code));

        return $code;
    }

    /**
     * Verify a code against the pending token, returning the payload when valid.
     *
     * @return array{name: string, email: string, phone: string|null, company: string|null, message: string, visitor_id: int, verified_email: string}|null
     */
    public function verify(string $token, string $code): ?array
    {
        $payload = Cache::get($this->key($token));

        if ($payload === null || ! hash_equals((string) $payload['code'], $code)) {
            return null;
        }

        Cache::forget($this->key($token));

        return $payload;
    }

    /**
     * @param  array{visitor_id: int, message: string}  $payload
     */
    public function store(array $payload): ContactMessage
    {
        return ContactMessage::create([
            'visitor_id' => $payload['visitor_id'],
            'message' => $payload['message'],
        ]);
    }

    private function key(string $token): string
    {
        return "contact-otp:{$token}";
    }

    /**
     * @throws RandomException
     */
    private function randomCode(): string
    {
        return (string) random_int(100000, 999999);
    }
}
