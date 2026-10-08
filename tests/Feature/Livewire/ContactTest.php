<?php

use App\Livewire\Components\Contact;
use App\Models\ContactMessage;
use App\Models\Visitor;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;

beforeEach(function (): void {
    Mail::fake();
});

function submitContactForm(array $overrides = [], array $cookies = []): Testable
{
    $component = Livewire::test(Contact::class)->set(array_merge([
        'name' => 'Sara Al Hinai',
        'email' => 'sara@example.test',
        'phone' => '+968 9123 4567',
        'company' => 'Acme LLC',
        'message' => 'I would like to discuss a project.',
    ], $overrides));

    foreach ($cookies as $name => $value) {
        app('request')->cookies->set($name, $value);
    }

    return $component->call('submit');
}

function confirmContactOtp(Testable $component): Testable
{
    $token = $component->otpToken;
    $code = Cache::get("contact-otp:{$token}")['code'];

    return $component->set('otp', $code)->call('confirmOtp');
}

it('stores every detail the visitor typed onto the visitor record', function () {
    $component = confirmContactOtp(submitContactForm());

    $component->assertSet('requireOtp', false);

    $visitor = Visitor::sole();

    expect($visitor->name)->toBe('Sara Al Hinai')
        ->and($visitor->email)->toBe('sara@example.test')
        ->and($visitor->phone)->toBe('+968 9123 4567')
        ->and($visitor->company)->toBe('Acme LLC');
});

it('stores only the message and the visitor id on the contact message', function () {
    confirmContactOtp(submitContactForm());

    $message = ContactMessage::sole();

    expect($message->message)->toBe('I would like to discuss a project.')
        ->and($message->visitor_id)->toBe(Visitor::sole()->id)
        ->and(array_intersect(['name', 'email'], array_keys($message->getAttributes())))->toBe([]);
});

it('updates an already identified visitor without overwriting their verified email', function () {
    $existing = Visitor::create([
        'uuid' => (string) fake()->uuid(),
        'name' => 'Old Name',
        'email' => 'known@example.test',
        'phone' => '+968 0000 0000',
        'company' => 'Old Co',
    ]);

    confirmContactOtp(submitContactForm(
        ['email' => 'typed@example.test'],
        ['visitor' => $existing->uuid],
    ));

    $existing->refresh();

    expect($existing->name)->toBe('Sara Al Hinai')
        ->and($existing->email)->toBe('known@example.test')
        ->and($existing->phone)->toBe('+968 9123 4567')
        ->and($existing->company)->toBe('Acme LLC');
});

it('leaves optional fields untouched when they were left blank', function () {
    confirmContactOtp(submitContactForm(['phone' => '', 'company' => '']));

    $visitor = Visitor::sole();

    expect($visitor->name)->toBe('Sara Al Hinai')
        ->and($visitor->phone)->toBeNull()
        ->and($visitor->company)->toBeNull();
});
