<?php

use App\Actions\SendMessageAction;
use App\Enums\MessageRole;
use App\Jobs\SendAiResponseJob;
use App\Models\Message;
use App\Models\Visitor;
use App\Services\ChatSlotsManagerService;

beforeEach(function () {
    Bus::fake();

    Config::set('services.ai_chat_slot.ttl', 30);
    Config::set('services.ai_chat_slot.slot_list_ttl', 60);
    Config::set('services.ai_chat_slot.extended_tt', 5);
    Config::set('services.ai_chat_slot.connection_limit', 10);

    $this->visitor = Visitor::factory()->create();
    $this->action = new SendMessageAction;
});

describe('execute', function () {
    it('stores the visitor message and hands it to the ai', function () {
        $message = $this->action->execute($this->visitor, 'Hello Saleh');

        expect($message)->toBeInstanceOf(Message::class)
            ->and($message->message)->toBe('Hello Saleh')
            ->and($message->role)->toBe(MessageRole::VISITOR)
            ->and($message->visitor_id)->toBe($this->visitor->id)
            ->and(Message::count())->toBe(1);

        Bus::assertDispatched(SendAiResponseJob::class);
    });

    it('replies with a full slots notice and skips the ai when the chat is full', function () {
        Config::set('services.ai_chat_slot.connection_limit', 1);
        app(ChatSlotsManagerService::class)->addSlot(Visitor::factory()->create());

        $message = $this->action->execute($this->visitor, 'Hello Saleh');

        expect($message->role)->toBe(MessageRole::ASSISTANT)
            ->and($message->message)->toBe('All chat slots are currently full. Please try again in a few minutes.')
            ->and(Message::count())->toBe(1);

        Bus::assertNotDispatched(SendAiResponseJob::class);
    });
});
