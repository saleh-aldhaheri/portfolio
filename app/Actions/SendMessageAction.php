<?php

namespace App\Actions;

use App\Enums\MessageRole;
use App\Models\Message;
use App\Models\Visitor;
use App\Services\ChatSlotsManager;

class SendMessageAction
{
    public function execute(Visitor $visitor, string $message): ?Message
    {
        if (! app(ChatSlotsManager::class)->addSlot($visitor)) {
            return Message::create([
                'visitor_id' => $visitor->id,
                'role' => MessageRole::ASSISTANT,
                'message' => 'All chat slots are currently full. Please try again in a few minutes.',
            ]);
        }

        $message = Message::create([
            'visitor_id' => $visitor->id,
            'role' => MessageRole::VISITOR,
            'message' => $message,
        ]);

        (new TriggerAiToSendMessage)->execute($message);

        return $message;
    }
}
