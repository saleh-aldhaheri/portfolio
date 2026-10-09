<?php

use App\Actions\SendAiResponseAction;
use App\Ai\Agents\ChatAgent;
use App\Data\ContextMessageData;
use App\Enums\MessageRole;
use App\Models\Message;
use App\Models\Visitor;
use App\Services\AiChatContextManagerService;
use App\Services\ChatSlotsManagerService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Laravel\Ai\Responses\AgentResponse;
use Laravel\Ai\Responses\Data\Meta;
use Laravel\Ai\Responses\Data\TextUsage;

beforeEach(function () {
    Event::fake();

    Config::set('services.ai_chat_context.ttl', 30);
    Config::set('services.ai_chat_context.words_count', 500);
    Config::set('services.ai_chat_slot.ttl', 30);
    Config::set('services.ai_chat_slot.slot_list_ttl', 60);
    Config::set('services.ai_chat_slot.extended_tt', 5);
    Config::set('services.ai_chat_slot.connection_limit', 10);

    $this->visitor = Visitor::factory()->create(['name' => 'Saleh']);
    $this->context = new AiChatContextManagerService;

    $this->incoming = fn (): Collection => collect([
        Message::create([
            'visitor_id' => $this->visitor->id,
            'role' => MessageRole::VISITOR,
            'message' => 'What projects have you built?',
        ]),
    ]);
});

function fakeAgentResponse(string $text): AgentResponse
{
    return new AgentResponse('fake-invocation-id', $text, new TextUsage, new Meta);
}

describe('execute', function () {
    it('creates nothing when no messages were received', function () {
        app(SendAiResponseAction::class)->execute(collect());

        expect(Message::count())->toBe(0);
    });

    it('creates nothing when the visitor cannot be resolved', function () {
        $message = Message::create([
            'visitor_id' => $this->visitor->id,
            'role' => MessageRole::VISITOR,
            'message' => 'hello',
        ]);

        DB::statement('PRAGMA foreign_keys = OFF');
        Visitor::whereKey($this->visitor->id)->delete();
        DB::statement('PRAGMA foreign_keys = ON');

        app(SendAiResponseAction::class)->execute(collect([$message]));

        expect(Message::where('role', MessageRole::ASSISTANT->value)->count())->toBe(0);
    });

    it('replies with a fallback and releases the slot when the context is over the word limit', function () {
        Config::set('services.ai_chat_context.words_count', 0);

        $this->context->setContext($this->visitor, [
            new ContextMessageData(
                role: MessageRole::VISITOR,
                message: 'a message that already filled the window',
                createdAt: Carbon::now(),
                visitorId: $this->visitor->id,
                visitorName: 'Saleh',
            ),
        ]);

        app(ChatSlotsManagerService::class)->addSlot($this->visitor);

        app(SendAiResponseAction::class)->execute(($this->incoming)());

        expect(Message::where('role', MessageRole::ASSISTANT->value)->first()->message)
            ->toBe('Thanks for reaching out, Saleh! We\'ve received your messages and will reply as soon as possible.')
            ->and(Cache::get('chat_slots_manager'))->not->toHaveKey($this->visitor->id);
    });

    it('stores the agent reply and appends it to the context', function () {
        $agent = $this->mock(ChatAgent::class);
        $agent->shouldReceive('setVisitor')->once()->with(Mockery::type(Visitor::class));
        $agent->shouldReceive('prompt')->once()->andReturn(fakeAgentResponse('I am Saleh\'s assistant.'));

        app(SendAiResponseAction::class)->execute(($this->incoming)());

        expect(Message::where('role', MessageRole::ASSISTANT->value)->first()->message)
            ->toBe('I am Saleh\'s assistant.');

        $stored = $this->context->getContext($this->visitor);

        expect($stored->messages)->toHaveCount(2)
            ->and($stored->messages[0]->role)->toBe(MessageRole::VISITOR)
            ->and($stored->messages[1]->role)->toBe(MessageRole::ASSISTANT)
            ->and($stored->messages[1]->message)->toBe('I am Saleh\'s assistant.');
    });

    it('does not respond again while the visitor is inside the cooldown window', function () {
        $agent = $this->mock(ChatAgent::class);
        $agent->shouldReceive('setVisitor')->once();
        $agent->shouldReceive('prompt')->once()->andReturn(fakeAgentResponse('first reply'));

        $incoming = ($this->incoming)();

        app(SendAiResponseAction::class)->execute($incoming);
        app(SendAiResponseAction::class)->execute($incoming);

        expect(Message::where('role', MessageRole::ASSISTANT->value)->count())->toBe(1);
    });

    it('keeps the conversation when the agent throws', function () {
        $agent = $this->mock(ChatAgent::class);
        $agent->shouldReceive('setVisitor')->once();
        $agent->shouldReceive('prompt')->once()->andThrow(new RuntimeException('provider down'));

        app(SendAiResponseAction::class)->execute(($this->incoming)());

        expect(Message::where('role', MessageRole::ASSISTANT->value)->first()->message)
            ->toContain('Saleh has received your message');
    });
});
