<?php

namespace App\Actions;

use App\Ai\Agents\ChatAgent;
use App\Data\ContextMessageData;
use App\Enums\MessageRole;
use App\Events\MessageSentEvent;
use App\Models\Message;
use App\Models\Visitor;
use App\Services\AiChatContextManagerService;
use App\Services\ChatSlotsManagerService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SendAiResponseAction
{
    public function __construct(
        private readonly ChatAgent $agent,
        private readonly AiChatContextManagerService $aiChatContextManagerService,
        private readonly ChatSlotsManagerService $chatSlotsManagerService
    ) {}

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

            $this->agent->setVisitor($visitor);

            if (! $this->aiChatContextManagerService->IsContextValid($visitor)) {
                $name = $visitor->name ?? 'there';
                $response = "Thanks for reaching out, {$name}! We've received your messages and will reply as soon as possible.";
                $this->chatSlotsManagerService->removeSlot($visitor);
            } else {

                $messages = json_encode($incomingMessage->pluck('message'));

                $promptPayload = <<<PROMPT
                INCOMING MESSAGE LATEST MESSAGES:
                - Sender: {$visitor->name}
                - Incoming Messages/latest Messages: "{$messages}"
                Compose the auto-reply response according to your system instructions now.
            PROMPT;

                try {
                    $response = $this->agent->prompt($promptPayload)->text;
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

        $this->aiChatContextManagerService->setContext(
            $visitor, $this->prepareContextData(
                $incomingMessage,
                $visitor,
                $response
            )
        );
    }

    public function prepareContextData(
        Collection $receivedMessages,
        Visitor $visitor,
        string $responseMessage,
    ): array {
        /** @var array<ContextMessageData> $messages */
        $messages = [];

        foreach ($receivedMessages as $message) {
            $messages[] = new ContextMessageData(
                role: MessageRole::VISITOR,
                message: $message->content ?? $message->message ?? '',
                createdAt: $message->created_at ? Carbon::parse($message->created_at) : Carbon::now(),
                visitorId: $message->visitor_id ?? $visitor->id,
                visitorName: $visitor->name ?? 'unknown'
            );
        }

        $messages[] = new ContextMessageData(
            role: MessageRole::ASSISTANT,
            message: $responseMessage,
            createdAt: Carbon::now(),
            visitorId: null,
            visitorName: null
        );

        return $messages;
    }

    private function isVisitorInCooldown(Visitor $visitor, int $cooldownSec = 5): bool
    {
        $key = "cooldown-window-chat:{$visitor->id}";

        $wasSet = Cache::add($key, true, $cooldownSec);

        return ! $wasSet;
    }
}
