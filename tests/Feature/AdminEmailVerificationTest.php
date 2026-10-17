<?php

use App\Filament\Auth\Pages\EmailVerification\EmailVerificationPrompt;
use App\Filament\Auth\Pages\Register;
use App\Mail\OtpMail;
use App\Models\User;
use App\Services\EmailVerificationCodeService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

// ---------------------------------------------------------------------------
// Registration page is accessible and sends an OTP on registration
// ---------------------------------------------------------------------------

test('registration page renders', function () {
    $response = $this->get(filament()->getRegistrationUrl());
    $response->assertSuccessful();
});

test('registration sends an OTP email instead of a signed verification link', function () {
    Mail::fake();

    Livewire::test(Register::class)
        ->fillForm([
            'name' => 'Test Admin',
            'email' => 'admin@example.com',
            'password' => 'Password1!',
            'passwordConfirmation' => 'Password1!',
        ])
        ->call('register');

    Mail::assertQueued(OtpMail::class, function (OtpMail $mail) {
        return $mail->purpose === 'admin-email-verification';
    });
});

// ---------------------------------------------------------------------------
// Email verification prompt
// ---------------------------------------------------------------------------

test('verification prompt is accessible to authenticated unverified user', function () {
    $user = User::factory()->unverified()->create();

    // Test via Livewire to bypass HTTP middleware stack; the component itself
    // must render without error for an unverified user.
    Livewire::actingAs($user)
        ->test(EmailVerificationPrompt::class)
        ->assertSuccessful();
});

test('correct OTP verifies the user email', function () {
    Mail::fake();

    $user = User::factory()->unverified()->create();
    $this->actingAs($user);

    $service = app(EmailVerificationCodeService::class);
    $service->issue($user);

    // Pull the stored code directly from cache for the test
    $payload = Cache::get("email-verify-otp:{$user->id}");
    $code = $payload['code'];

    Livewire::actingAs($user)
        ->test(EmailVerificationPrompt::class)
        ->fillForm(['code' => $code])
        ->call('verify');

    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
});

test('wrong OTP does not verify the user and shows an error notification', function () {
    Mail::fake();

    $user = User::factory()->unverified()->create();
    $this->actingAs($user);

    app(EmailVerificationCodeService::class)->issue($user);

    Livewire::actingAs($user)
        ->test(EmailVerificationPrompt::class)
        ->fillForm(['code' => '000000'])
        ->call('verify')
        ->assertNotified();

    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});

test('OTP expires after TTL and verification fails', function () {
    Mail::fake();

    $user = User::factory()->unverified()->create();
    $this->actingAs($user);

    app(EmailVerificationCodeService::class)->issue($user);

    // Manually expire the cache entry
    Cache::forget("email-verify-otp:{$user->id}");

    Livewire::actingAs($user)
        ->test(EmailVerificationPrompt::class)
        ->fillForm(['code' => '123456'])
        ->call('verify')
        ->assertNotified();

    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});

test('exceeding max attempts locks the user out', function () {
    Mail::fake();

    $user = User::factory()->unverified()->create();
    $this->actingAs($user);

    app(EmailVerificationCodeService::class)->issue($user);

    $component = Livewire::actingAs($user)->test(EmailVerificationPrompt::class);

    // Submit wrong codes up to the max-attempt threshold
    for ($i = 0; $i < EmailVerificationCodeService::MAX_ATTEMPTS; $i++) {
        $component->fillForm(['code' => '000000'])->call('verify');
    }

    // One more attempt after lockout — service should detect locked out state
    $component->fillForm(['code' => '000000'])->call('verify')->assertNotified();

    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});

test('resend action sends a new OTP email', function () {
    Mail::fake();

    $user = User::factory()->unverified()->create();
    $this->actingAs($user);

    app(EmailVerificationCodeService::class)->issue($user);

    Livewire::actingAs($user)
        ->test(EmailVerificationPrompt::class)
        ->callAction('resendNotification');

    Mail::assertQueued(OtpMail::class, 2);
});

test('resend is throttled to one call per 60 seconds per user', function () {
    Mail::fake();

    $user = User::factory()->unverified()->create();
    $this->actingAs($user);

    $service = app(EmailVerificationCodeService::class);
    $service->issue($user);

    // First resend should succeed
    $first = $service->resend($user);
    // Second immediate resend should be throttled
    $second = $service->resend($user);

    expect($first)->toBeTrue()
        ->and($second)->toBeFalse();
});

// ---------------------------------------------------------------------------
// canAccessPanel — only verified users can access the panel
// ---------------------------------------------------------------------------

test('verified user satisfies canAccessPanel', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $panel = filament()->getDefaultPanel();
    expect($user->canAccessPanel($panel))->toBeTrue();
});

test('unverified user does not satisfy canAccessPanel', function () {
    $user = User::factory()->unverified()->create();
    $panel = filament()->getDefaultPanel();
    expect($user->canAccessPanel($panel))->toBeFalse();
});

// ---------------------------------------------------------------------------
// Password reset flow
// ---------------------------------------------------------------------------

test('password reset request page is accessible', function () {
    $response = $this->get(filament()->getRequestPasswordResetUrl());
    $response->assertSuccessful();
});

test('password reset for unknown email returns neutral response without revealing user existence', function () {
    $response = Livewire::test(\Filament\Auth\Pages\PasswordReset\RequestPasswordReset::class)
        ->fillForm(['email' => 'nonexistent@example.com'])
        ->call('request');

    // Should not throw or reveal user existence — neutral notification
    $response->assertNotified();
});
