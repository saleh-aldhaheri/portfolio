<?php

namespace App\Actions;

use App\Ai\Agents\ChatAgent;
use App\Enums\MessageRole;
use App\Events\MessageSentEvent;
use App\Models\Message;
use App\Models\Visitor;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Laravel\Ai\Exceptions\ProviderConnectionException;
use Laravel\Ai\Exceptions\ProviderOverloadedException;
use Laravel\Ai\Exceptions\RateLimitedException;

class SendAiResponseAction
{
    /**
     * @param  Collection<int, Message>  $incomingMessage
     */
    public function execute(Collection $incomingMessage): void
    {
        $firstMessage = $incomingMessage->first();

        if ($firstMessage && $firstMessage->visitor_id) {
            $visitorObj = Visitor::find($firstMessage->visitor_id);
            if ($visitorObj && $this->isVisitorInCooldown($visitorObj, 5)) {
                return;
            }
        }

        $visitor = null;
        $response = null;

        if ($incomingMessage->isEmpty()) {
            $response = 'No messages were received to process. Please try sending your message again.';
        } else {
            $visitor = Visitor::find($incomingMessage->first()->visitor_id);

            if (! $visitor) {
                $response = 'Something went wrong while resolving your profile. Please try again later.';
            }
        }

        if (! $response && $visitor) {
            $agent = new ChatAgent($visitor);

            if (! $this->checkContextWindow($agent)) {
                $name = $visitor->name ?? 'there';
                $response = "Thanks for reaching out, {$name}! We've received your messages and will reply as soon as possible.";
            } else {

                $messages = json_encode($incomingMessage->pluck('message'));

                $agent->rememberLatestMessages($incomingMessage->pluck('message')->all());

                $promptPayload = <<<PROMPT
                INCOMING MESSAGE LATEST MESSAGES:
                - Sender: {$visitor->name}
                - Incoming Messages/latest Messages: "{$messages}"
                Compose the auto-reply response according to your system instructions now.
            PROMPT;

                try {
                    $response = $this->promptWithRetry($agent, $promptPayload, $visitor->id);
                } catch (\Throwable $exception) {
                    Log::error('AI chat prompt failed', [
                        'visitor_id' => $visitor->id,
                        'exception' => get_class($exception),
                        'message' => $exception->getMessage(),
                        'trace' => $exception->getTraceAsString(),
                    ]);

                    $response = "I'm having a quick hiccup on my side, so this reply isn't quite ready - but Saleh has received your message and will get back to you soon. In the meantime, feel free to keep asking anything about him or this site.";
                }
            }
        }

        if (empty($response) || ! $visitor) {
            return;
        }

        $aiMessage = Message::create([
            'visitor_id' => $visitor->id,
            'role' => MessageRole::ASSISTANT->value,
            'message' => $response,
        ]);

        broadcast(new MessageSentEvent($aiMessage));

        $this->updateContext($agent ?? new ChatAgent($visitor), $incomingMessage, $visitor, $response);
    }

    /**
     * Prompt the agent, retrying transient provider errors (rate limits, overloaded, connection) with a short backoff.
     *
     * Tool turns make back-to-back provider calls, which can trip rate limits, so a few retries are warranted.
     */
    private function promptWithRetry(ChatAgent $agent, string $promptPayload, int $visitorId, int $maxAttempts = 3): string
    {
        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            try {
                return $agent->prompt($promptPayload)->text;
            } catch (RateLimitedException|ProviderOverloadedException|ProviderConnectionException $exception) {
                if ($attempt === $maxAttempts) {
                    throw $exception;
                }

                Log::warning('AI chat provider transient error, retrying', [
                    'visitor_id' => $visitorId,
                    'exception' => get_class($exception),
                    'message' => $exception->getMessage(),
                    'attempt' => $attempt,
                ]);

                sleep($attempt * 2);
            }
        }

        throw new LogicException('Unreachable.');
    }

    private function updateContext(
        ChatAgent $agent,
        Collection $receivedMessages,
        Visitor $visitor,
        string $responseMessage,
    ): void {
        $messages = [];

        foreach ($receivedMessages as $message) {
            $messages[] = [
                'role' => MessageRole::VISITOR->value,
                'visitor_id' => $message->visitor_id,
                'name' => $visitor->name ?? 'unknown',
                'text' => $message->content ?? $message->message,
                'type' => 'received',
                'created_at' => $message->created_at?->toISOString() ?? now()->toISOString(),
            ];
        }

        $messages[] = [
            'role' => MessageRole::ASSISTANT->value,
            'visitor_id' => null,
            'name' => null,
            'text' => $responseMessage,
            'type' => 'sent',
            'created_at' => now()->toISOString(),
        ];

        $agent->setContext($messages);
    }

    private function isVisitorInCooldown(Visitor $visitor, int $cooldownSec = 5): bool
    {
        $key = "cooldown-window-chat:{$visitor->id}";

        $wasSet = Cache::add($key, true, $cooldownSec);

        return ! $wasSet;
    }

    private function checkContextWindow(ChatAgent $agent, int $contextLimitWords = 1000): bool
    {
        $context = $agent->getContextSummary();

        if (empty($context)) {
            return true;
        }

        return ! ($context['total_words'] > $contextLimitWords && $context['expires_at'] > Carbon::now()->timestamp);
    }
}
