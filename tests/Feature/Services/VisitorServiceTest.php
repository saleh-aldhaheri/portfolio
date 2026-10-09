<?php

use App\Models\Visitor;
use App\Services\VisitorService;

beforeEach(function () {
    $this->service = new VisitorService;
});

describe('resolve', function () {
    it('creates a visitor and queues the visitor cookie when no cookie is present', function () {
        $visitor = $this->service->resolve(Request::create('/', 'GET'));

        expect($visitor->exists)->toBeTrue()
            ->and($visitor->uuid)->not->toBeNull()
            ->and($visitor->city)->toBe('Unknown')
            ->and($visitor->country)->toBe('Unknown')
            ->and($visitor->os)->toBe('Unknown');

        $cookie = Cookie::queued('visitor');

        expect($cookie)->not->toBeNull()
            ->and($cookie->getValue())->toBe($visitor->uuid)
            ->and($cookie->getExpiresTime())->toBeGreaterThan(now()->addMonths(11)->getTimestamp());
    });

    it('reuses the visitor behind a known cookie and refreshes its ip', function () {
        $existing = Visitor::factory()->create(['uuid' => 'known-uuid']);

        $request = Request::create('/', 'GET');
        $request->cookies->set('visitor', 'known-uuid');
        $request->headers->set('CF-Connecting-IP', '203.0.113.9');

        $visitor = $this->service->resolve($request);

        expect($visitor->id)->toBe($existing->id)
            ->and($visitor->fresh()->ip_address)->toBe('203.0.113.9')
            ->and(Visitor::count())->toBe(1);
    });

    it('does not queue a cookie when the visitor already exists', function () {
        Visitor::factory()->create(['uuid' => 'known-uuid']);

        $request = Request::create('/', 'GET');
        $request->cookies->set('visitor', 'known-uuid');

        $this->service->resolve($request);

        expect(Cookie::queued('visitor'))->toBeNull();
    });

    it('reads the edge headers when they are present', function () {
        $request = Request::create('/', 'GET');
        $request->headers->set('CF-Connecting-IP', '198.51.100.7');
        $request->headers->set('CF-IPCity', 'Muscat');
        $request->headers->set('CF-IPCountry', 'Oman');
        $request->headers->set('Sec-CH-UA-Platform', '"Linux"');
        $request->headers->set('Sec-CH-UA', 'Chromium');

        $visitor = $this->service->resolve($request);

        expect($visitor->ip_address)->toBe('198.51.100.7')
            ->and($visitor->city)->toBe('Muscat')
            ->and($visitor->country)->toBe('Oman')
            ->and($visitor->os)->toBe('Linux')
            ->and($visitor->browser)->toBe('Chromium');
    });

    it('resolves a single visitor when called more than once for the same request', function () {
        $request = Request::create('/', 'GET');

        $first = $this->service->resolve($request);
        $second = $this->service->resolve($request);

        expect($second->id)->toBe($first->id)
            ->and(Visitor::count())->toBe(1);
    });
});
