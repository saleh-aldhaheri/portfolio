<?php

namespace App\Services;

use App\Data\ContextData;
use App\Data\ContextMessageData;
use App\Data\ContextSummaryData;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;

class AiChatContextManagerService
{
    /**
     * @param  array<ContextMessageData>  $context
     */
    public function setContext($visitor, array $content): void
    {
        if (empty($content)) {
            throw new InvalidArgumentException('Messages array cannot be empty.');
        }

        foreach ($content as $message) {
            if (! $message instanceof ContextMessageData) {
                throw new InvalidArgumentException('All messages must be an instance of ContextMessageData.');
            }
        }

        $key = $this->cacheKey($visitor);
        $existingContext = $this->readContext($key);

        $existingMessages = $existingContext?->messages ?? [];

        $mergedMessages = array_merge($existingMessages, $content);

        $now = Carbon::now();

        $ttlInMinutes = (int) config('services.ai_chat_context.ttl');
        $expiredAt = $now->copy()->addMinutes($ttlInMinutes);

        $contextData = new ContextData(
            messages: $mergedMessages,
            expiredAt: $expiredAt
        );

        Cache::put($key, $contextData, $expiredAt);
    }

    public function cacheKey($visitor): string
    {
        return "context_window_visitor_{$visitor->id}";
    }

    public function getContext($visitor): ?ContextData
    {
        return $this->readContext($this->cacheKey($visitor));
    }

    public function getContextSummary($visitor): ?ContextSummaryData
    {
        $key = $this->cacheKey($visitor);

        $context = $this->readContext($key);

        if (! $context) {
            return null;
        }

        return collect($context->messages)->pipe(function ($messages) use ($context) {
            $wordsCount = $messages->sum(
                fn (ContextMessageData $message) => str_word_count($message->message)
            );

            $charactersCount = $messages->sum(
                fn (ContextMessageData $message) => mb_strlen($message->message)
            );

            return new ContextSummaryData(
                wordsCount: (int) $wordsCount,
                charactersCount: (int) $charactersCount,
                expiredAt: $context->expiredAt
            );
        });
    }

    public function IsContextFull($visitor): bool
    {
        $context = $this->getContextSummary($visitor);

        if ($context && $context->wordsCount > config('services.ai_chat_context.words_count')) {
            return true;
        }

        return false;
    }

    public function IsContextExpired($visitor): bool
    {
        $context = $this->getContextSummary($visitor);

        if ($context && $context->expiredAt < now()) {
            return true;
        }

        return false;
    }

    public function IsContextValid($visitor): bool
    {
        $context = $this->getContextSummary($visitor);

        if ($context && ($context->expiredAt < now() || $context->wordsCount > config('services.ai_chat_context.words_count'))) {
            return false;
        }

        return true;
    }

    private function readContext(string $key): ?ContextData
    {
        $context = Cache::get($key);

        if (! $context instanceof ContextData) {
            Cache::forget($key);

            return null;
        }

        if (Carbon::now()->greaterThanOrEqualTo($context->expiredAt)) {
            Cache::forget($key);

            return null;
        }

        return $context;
    }
}
