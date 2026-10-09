<?php

use App\Actions\TriggerAiToSendMessage;
use App\Enums\MessageRole;
use App\Jobs\SendAiResponseJob;
use App\Models\Message;
use App\Models\Visitor;
use Illuminate\Database\Eloquent\Collection;

beforeEach(function () {
    Bus::fake();

    $this->visitor = Visitor::factory()->create();

    $this->makeMessage = function (MessageRole $role, string $text, ?DateTimeInterface $createdAt = null): Message {
        $message = Message::create([
            'visitor_id' => $this->visitor->id,
            'role' => $role,
            'message' => $text,
        ]);

        if ($createdAt) {
            $message->created_at = $createdAt;
            $message->save();
        }

        return $message;
    };
});

function dispatchedMessages(): Collection
{
    $messages = null;

    Bus::assertDispatched(SendAiResponseJob::class, function (SendAiResponseJob $job) use (&$messages): bool {
        $property = new ReflectionProperty($job, 'incomingMessages');
        $property->setAccessible(true);
        $messages = $property->getValue($job);

        return true;
    });

    return $messages;
}

describe('execute', function () {
    it('dispatches the visitor messages collected since the last assistant reply', function () {
        ($this->makeMessage)(MessageRole::VISITOR, 'stale', now()->subMinutes(5));
        ($this->makeMessage)(MessageRole::ASSISTANT, 'reply', now()->subMinutes(2));
        ($this->makeMessage)(MessageRole::VISITOR, 'fresh');

        (new TriggerAiToSendMessage)->execute(
            Message::where('message', 'fresh')->firstOrFail()
        );

        expect(dispatchedMessages()->pluck('message')->all())->toBe(['fresh']);
    });

    it('falls back to the ten minute window when the visitor has no assistant reply', function () {
        ($this->makeMessage)(MessageRole::VISITOR, 'inside', now()->subMinutes(5));
        ($this->makeMessage)(MessageRole::VISITOR, 'outside', now()->subMinutes(15));

        (new TriggerAiToSendMessage)->execute(
            Message::where('message', 'inside')->firstOrFail()
        );

        expect(dispatchedMessages()->pluck('message')->all())->toBe(['inside']);
    });

    it('only collects messages belonging to the same visitor', function () {
        $other = Visitor::factory()->create();
        Message::create(['visitor_id' => $other->id, 'role' => MessageRole::VISITOR, 'message' => 'other']);

        ($this->makeMessage)(MessageRole::VISITOR, 'mine');

        (new TriggerAiToSendMessage)->execute(
            Message::where('message', 'mine')->firstOrFail()
        );

        expect(dispatchedMessages()->pluck('message')->all())->toBe(['mine']);
    });
});
