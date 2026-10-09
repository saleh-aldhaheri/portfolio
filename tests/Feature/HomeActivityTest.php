<?php

use App\Livewire\Components\UserTerminal;
use App\Livewire\Pages\Home;
use App\Models\AnalyticsLog;
use App\Models\Visitor;
use Livewire\Livewire;

test('page load creates no visitor and issues no visitor cookie', function () {
    $response = $this->get('/');

    $response->assertOk();
    $response->assertCookieMissing('visitor');

    expect(Visitor::count())->toBe(0);
});

test('logActivity resolves a visitor and records the event', function () {
    Livewire::test(Home::class)
        ->call('logActivity', 'open-github');

    expect(Visitor::count())->toBe(1)
        ->and(AnalyticsLog::count())->toBe(1)
        ->and(AnalyticsLog::first()->event)->toBe('open-github');
});

test('logActivity reuses the visitor identified by the cookie', function () {
    $visitor = Visitor::factory()->create();

    $test = Livewire::test(Home::class);

    app('request')->cookies->set('visitor', $visitor->uuid);

    $test->call('logActivity', 'open-resume');

    expect(Visitor::count())->toBe(1)
        ->and(AnalyticsLog::count())->toBe(1)
        ->and(AnalyticsLog::first()->visitor_id)->toBe($visitor->id);
});

test('terminal command dispatches a named log-activity event', function () {
    Livewire::test(UserTerminal::class)
        ->set('input', 'about')
        ->call('runCommand')
        ->assertDispatched('log-activity', 'command-executed: about');
});
