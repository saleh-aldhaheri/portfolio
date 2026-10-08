<?php

use App\Data\ContextData;
use App\Data\ContextMessageData;
use App\Data\ContextSummaryData;
use App\Enums\MessageRole;
use App\Models\Visitor;
use App\Services\AiChatContextManagerService;
use Carbon\Carbon;

beforeEach(function () {
    $this->visitor = Visitor::factory()->create();
    $this->aiChatContextManagerService = new AiChatContextManagerService;
});

describe('set context', function () {
    it('should create or update the context into the cache', function () {
        $now = new DateTime('2026-10-08 10:00:00');

        $incomingMessages = [
            new ContextMessageData(
                role: MessageRole::VISITOR,
                message: 'Hello, I need help',
                createdAt: $now,
                visitorId: 101,
                visitorName: 'John Doe'
            ),
        ];

        $this->aiChatContextManagerService->setContext($this->visitor, $incomingMessages);

        $cachedData = Cache::get($this->aiChatContextManagerService->cacheKey($this->visitor));

        expect($cachedData)->toBeInstanceOf(ContextData::class)
            ->and($cachedData->messages)->toEqual($incomingMessages)
            ->and($cachedData->messages[0]->message)->toBe('Hello, I need help')
            ->and($cachedData->messages[0]->visitorId)->toBe(101);
    });

    it('should throw an error if an array element is not a ContextMessageData instance', function () {
        $invalidMessages = [
            'invalid_message_string',
            ['role' => 'user', 'message' => 'Hello'],
        ];

        expect(fn () => $this->aiChatContextManagerService->setContext($this->visitor, $invalidMessages))
            ->toThrow(InvalidArgumentException::class, 'All messages must be an instance of ContextMessageData.');
    });

    it('should throw an error if the incoming messages array is empty', function () {

        $emptyMessages = [];

        expect(fn () => $this->aiChatContextManagerService->setContext($this->visitor, $emptyMessages))
            ->toThrow(InvalidArgumentException::class, 'Messages array cannot be empty.');
    });

    it('should clear the context window based on configured context ttl', function () {

        $ttlInMinutes = 30;

        Config::set('services.ai_chat_context.ttl', $ttlInMinutes);

        $incomingMessages = [
            new ContextMessageData(
                role: MessageRole::VISITOR,
                message: 'Hello, world!',
                createdAt: new DateTime,
                visitorId: 101,
                visitorName: 'John Doe'
            ),
        ];

        $this->aiChatContextManagerService->setContext($this->visitor, $incomingMessages);

        $cacheKey = $this->aiChatContextManagerService->cacheKey($this->visitor);

        expect(Cache::get($cacheKey))->not()->toBeNull();

        $this->travel($ttlInMinutes + 1)->minutes();

        expect(Cache::get($cacheKey))->toBeNull();
    });

    it('should add new context if the context is valid', function () {

        $incomingMessages1 = [
            new ContextMessageData(
                role: MessageRole::VISITOR,
                message: 'Hello, world!',
                createdAt: new DateTime,
                visitorId: 101,
                visitorName: 'John Doe'
            ),
            new ContextMessageData(
                role: MessageRole::VISITOR,
                message: 'Hello, world!',
                createdAt: new DateTime,
                visitorId: 101,
                visitorName: 'John Doe'
            ),
        ];

        $incomingMessages2 = [
            new ContextMessageData(
                role: MessageRole::VISITOR,
                message: 'Hello, world!',
                createdAt: new DateTime,
            ),
        ];

        $this->aiChatContextManagerService->setContext($this->visitor, $incomingMessages1);

        $cacheKey = $this->aiChatContextManagerService->cacheKey($this->visitor);

        $data = Cache::get($cacheKey);
        expect($data)->not()->toBeNull()
            ->and(count($data->messages))->toBe(2);

        $this->aiChatContextManagerService->setContext($this->visitor, $incomingMessages2);

        $data = Cache::get($cacheKey);
        expect($data)->not()->toBeNull()
            ->and(count($data->messages))->toBe(3);
    });
});

describe('get context', function () {
    it('should return a ContextData instance', function () {
        $incomingMessages = [
            new ContextMessageData(
                role: MessageRole::VISITOR,
                message: 'Hello, world!',
                createdAt: new DateTime,
                visitorId: 101,
                visitorName: 'John Doe'
            ),
            new ContextMessageData(
                role: MessageRole::VISITOR,
                message: 'Hello, world!',
                createdAt: new DateTime,
                visitorId: 101,
                visitorName: 'John Doe'
            ),
        ];

        $this->aiChatContextManagerService->setContext($this->visitor, $incomingMessages);

        $data = $this->aiChatContextManagerService->getContext($this->visitor);

        expect($data)->toBeInstanceOf(ContextData::class)
            ->and(count($data->messages))
            ->toBe(2)
            ->and($data->messages[0])
            ->toBeInstanceOf(ContextMessageData::class);
    });

    it('should return null if context is empty', function () {
        $data = $this->aiChatContextManagerService->getContext($this->visitor);
        expect($data)->toBeNull();
    });
});

describe('get context summary', function () {
    it('should return context summary with correct word count, character count, and expiration date', function () {

        $expiredAt = Carbon::now()->addMinutes(60);

        $messages = [
            new ContextMessageData(
                role: MessageRole::VISITOR,
                message: 'Hello world',
                createdAt: new DateTime('2026-10-08 10:00:00'),
                visitorId: 101,
                visitorName: 'John Doe'
            ),
            new ContextMessageData(
                role: MessageRole::VISITOR,
                message: 'I need assistance',
                createdAt: new DateTime('2026-10-08 10:01:00'),
                visitorId: 101,
                visitorName: 'John Doe'
            ),
        ];

        $cacheKey = $this->aiChatContextManagerService->cacheKey($this->visitor);
        Cache::put($cacheKey, new ContextData(messages: $messages, expiredAt: $expiredAt));

        $summary = $this->aiChatContextManagerService->getContextSummary($this->visitor);

        expect($summary)->toBeInstanceOf(ContextSummaryData::class)
            ->and($summary->wordsCount)->toBe(5)
            ->and($summary->charactersCount)->toBe(28)
            ->and($summary->expiredAt)->toEqual($expiredAt);
    });

    it('should return null when no context exists in cache', function () {

        $summary = $this->aiChatContextManagerService->getContextSummary($this->visitor);

        expect($summary)->toBeNull();
    });
});

describe('context status checks', function () {

    beforeEach(function () {
        Config::set('services.ai_chat_context.words_count', 10);
    });

    describe('IsContextFull', function () {
        it('returns false when no context exists in cache', function () {
            expect($this->aiChatContextManagerService->IsContextFull($this->visitor))->toBeFalse();
        });

        it('returns true when total word count exceeds configured threshold', function () {
            $messages = [
                new ContextMessageData(
                    role: MessageRole::VISITOR,
                    message: 'One two three four five six seven eight nine ten eleven twelve',
                    createdAt: Carbon::now(),
                    visitorId: 101,
                    visitorName: 'John'
                ),
            ];

            $key = $this->aiChatContextManagerService->cacheKey($this->visitor);
            Cache::put($key, new ContextData(messages: $messages, expiredAt: Carbon::now()->addMinutes(30)));

            expect($this->aiChatContextManagerService->IsContextFull($this->visitor))->toBeTrue();
        });

        it('returns false when total word count is within threshold', function () {
            $messages = [
                new ContextMessageData(
                    role: MessageRole::VISITOR,
                    message: 'One two three four five',
                    createdAt: Carbon::now(),
                    visitorId: 101,
                    visitorName: 'John'
                ),
            ];

            $key = $this->aiChatContextManagerService->cacheKey($this->visitor);
            Cache::put($key, new ContextData(messages: $messages, expiredAt: Carbon::now()->addMinutes(30)));

            expect($this->aiChatContextManagerService->IsContextFull($this->visitor))->toBeFalse();
        });
    });

    describe('IsContextExpired', function () {
        it('returns false when no context exists in cache', function () {
            expect($this->aiChatContextManagerService->IsContextExpired($this->visitor))->toBeFalse();
        });

        it('returns true when context expiredAt timestamp is in the past', function () {
            $messages = [
                new ContextMessageData(
                    role: MessageRole::VISITOR,
                    message: 'Hello world',
                    createdAt: Carbon::now(),
                    visitorId: 101,
                    visitorName: 'John'
                ),
            ];

            $key = $this->aiChatContextManagerService->cacheKey($this->visitor);
            Cache::put($key, new ContextData(messages: $messages, expiredAt: Carbon::now()->addMinutes(10)), 600);

            $this->travel(15)->minutes();

            expect($this->aiChatContextManagerService->IsContextExpired($this->visitor))->toBeFalse();
        });

        it('returns false when context is still active', function () {
            $messages = [
                new ContextMessageData(
                    role: MessageRole::VISITOR,
                    message: 'Hello world',
                    createdAt: Carbon::now(),
                    visitorId: 101,
                    visitorName: 'John'
                ),
            ];

            $key = $this->aiChatContextManagerService->cacheKey($this->visitor);
            Cache::put($key, new ContextData(messages: $messages, expiredAt: Carbon::now()->addMinutes(30)));

            expect($this->aiChatContextManagerService->IsContextExpired($this->visitor))->toBeFalse();
        });
    });

    describe('IsContextValid', function () {
        it('returns true when no context exists in cache', function () {
            expect($this->aiChatContextManagerService->IsContextValid($this->visitor))->toBeTrue();
        });

        it('returns false when context is expired', function () {
            $messages = [
                new ContextMessageData(
                    role: MessageRole::VISITOR,
                    message: 'Hello world',
                    createdAt: Carbon::now(),
                    visitorId: 101,
                    visitorName: 'John'
                ),
            ];

            $key = $this->aiChatContextManagerService->cacheKey($this->visitor);
            Cache::put($key, new ContextData(messages: $messages, expiredAt: Carbon::now()->addMinutes(10)), 600);

            $this->travel(15)->minutes();

            expect($this->aiChatContextManagerService->IsContextValid($this->visitor))->toBeTrue();
        });

        it('returns false when context exceeds maximum allowed words', function () {
            $messages = [
                new ContextMessageData(
                    role: MessageRole::VISITOR,
                    message: 'One two three four five six seven eight nine ten eleven twelve thirteen fourteen fifteen',
                    createdAt: Carbon::now(),
                    visitorId: 101,
                    visitorName: 'John'
                ),
            ];

            $key = $this->aiChatContextManagerService->cacheKey($this->visitor);
            Cache::put($key, new ContextData(messages: $messages, expiredAt: Carbon::now()->addMinutes(30)));

            expect($this->aiChatContextManagerService->IsContextValid($this->visitor))->toBeFalse();
        });

        it('returns true when context is active and within word limit', function () {
            $messages = [
                new ContextMessageData(
                    role: MessageRole::VISITOR,
                    message: 'Hello world',
                    createdAt: Carbon::now(),
                    visitorId: 101,
                    visitorName: 'John'
                ),
            ];

            $key = $this->aiChatContextManagerService->cacheKey($this->visitor);
            Cache::put($key, new ContextData(messages: $messages, expiredAt: Carbon::now()->addMinutes(30)));

            expect($this->aiChatContextManagerService->IsContextValid($this->visitor))->toBeTrue();
        });
    });
});
