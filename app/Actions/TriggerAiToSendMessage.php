<?php

namespace App\Actions;

use App\Enums\MessageRole;
use App\Jobs\SendAiResponseJob;
use App\Models\Message;

class TriggerAiToSendMessage
{
    public function execute(Message $message): void
    {
        $lastAiMessage = Message::where('visitor_id', $message->visitor_id)
            ->where('role', MessageRole::ASSISTANT)
            ->latest()
            ->first();

        $cutoff = now()->subMinutes(10);

        if ($lastAiMessage && $lastAiMessage->created_at->gt($cutoff)) {
            $cutoff = $lastAiMessage->created_at;
        }

        $incomingMessages = Message::where('visitor_id', $message->visitor_id)
            ->where('role', MessageRole::VISITOR)
            ->where('created_at', '>=', $cutoff)
            ->get();

        dispatch(new SendAiResponseJob($incomingMessages));
    }
}
