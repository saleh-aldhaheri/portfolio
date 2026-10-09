<?php

namespace App\Data;

use App\Enums\MessageRole;

class ContextMessageData
{
    public function __construct(
        public MessageRole $role,
        public string $message,
        public \DateTime $createdAt,
        public ?int $visitorId = null,
        public ?string $visitorName = null,
    ) {}

    public function toArray(): array
    {
        return [
            'role' => $this->role->value,
            'visitorId' => $this->visitorId ?? null,
            'visitorName' => $this->visitorName ?? null,
            'message' => $this->message,
            'createdAt' => $this->createdAt,
        ];
    }
}
