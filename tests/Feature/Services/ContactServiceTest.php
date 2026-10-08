<?php

use App\Mail\OtpMail;
use App\Models\ContactMessage;
use App\Models\Visitor;
use App\Services\ContactService;

beforeEach(function () {
    Mail::fake();

    $this->service = new ContactService;
    $this->visitor = Visitor::factory()->create();
    $this->payload = [
        'name' => 'Saleh',
        'email' => 'visitor@example.test',
        'phone' => null,
        'company' => null,
        'message' => 'I would like to work with you',
        'visitor_id' => $this->visitor->id,
        'verified_email' => 'confirmed@example.test',
    ];
});

describe('issue', function () {
    it('returns a uuid token and a six digit code', function () {
        $result = $this->service->issue($this->payload);

        expect(strlen($result['token']))->toBe(36)
            ->and($result['code'])->toMatch('/^\d{6}$/');
    });

    it('stores the pending payload under the token', function () {
        $result = $this->service->issue($this->payload);

        $pending = Cache::get("contact-otp:{$result['token']}");

        expect($pending['name'])->toBe('Saleh')
            ->and($pending['message'])->toBe('I would like to work with you')
            ->and($pending['verified_email'])->toBe('confirmed@example.test')
            ->and($pending['code'])->toBe($result['code']);
    });

    it('emails the code to the verified address only', function () {
        $this->service->issue($this->payload);

        Mail::assertQueued(OtpMail::class, fn (OtpMail $mail): bool => $mail->hasTo('confirmed@example.test'));
        Mail::assertNotQueued(OtpMail::class, fn (OtpMail $mail): bool => $mail->hasTo('visitor@example.test'));
    });

    it('keeps the pending payload until the ttl passes', function () {
        $result = $this->service->issue($this->payload);

        $this->travel(ContactService::TTL_MINUTES - 1)->minutes();

        expect(Cache::get("contact-otp:{$result['token']}"))->not->toBeNull();
    });

    it('drops the pending payload once the ttl has passed', function () {
        $result = $this->service->issue($this->payload);

        $this->travel(ContactService::TTL_MINUTES + 1)->minutes();

        expect($this->service->verify($result['token'], $result['code']))->toBeNull();
    });
});

describe('verify', function () {
    it('returns the payload and consumes the token when the code matches', function () {
        $result = $this->service->issue($this->payload);

        $payload = $this->service->verify($result['token'], $result['code']);

        expect($payload['message'])->toBe('I would like to work with you')
            ->and($payload['visitor_id'])->toBe($this->visitor->id)
            ->and(Cache::get("contact-otp:{$result['token']}"))->toBeNull()
            ->and($this->service->verify($result['token'], $result['code']))->toBeNull();
    });

    it('keeps the token alive when the code does not match', function () {
        $result = $this->service->issue($this->payload);
        $wrongCode = $result['code'] === '000000' ? '000001' : '000000';

        expect($this->service->verify($result['token'], $wrongCode))->toBeNull()
            ->and($this->service->verify($result['token'], $result['code']))->not->toBeNull();
    });

    it('returns null for a token that was never issued', function () {
        expect($this->service->verify('missing-token', '123456'))->toBeNull();
    });
});

describe('resend', function () {
    it('returns null for a token that was never issued', function () {
        expect($this->service->resend('missing-token'))->toBeNull();
    });

    it('issues and emails a replacement code for a pending token', function () {
        $result = $this->service->issue($this->payload);

        $code = $this->service->resend($result['token']);

        expect($code)->toMatch('/^\d{6}$/')
            ->and(Cache::get("contact-otp:{$result['token']}")['code'])->toBe($code);

        Mail::assertQueued(OtpMail::class, fn (OtpMail $mail): bool => $mail->hasTo('confirmed@example.test'));
    });

    it('does not resend once the token has been verified', function () {
        $result = $this->service->issue($this->payload);
        $this->service->verify($result['token'], $result['code']);

        expect($this->service->resend($result['token']))->toBeNull();
    });
});

describe('store', function () {
    it('persists the verified payload as a contact message', function () {
        $result = $this->service->issue($this->payload);
        $payload = $this->service->verify($result['token'], $result['code']);

        $message = $this->service->store($payload);

        expect($message)->toBeInstanceOf(ContactMessage::class)
            ->and($message->name)->toBe('Saleh')
            ->and($message->email)->toBe('confirmed@example.test')
            ->and($message->message)->toBe('I would like to work with you')
            ->and($message->visitor_id)->toBe($this->visitor->id)
            ->and(ContactMessage::count())->toBe(1);
    });
});
