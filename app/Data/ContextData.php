<?php

namespace App\Data;

class ContextData
{
    /**
     * @param  array<ContextMessageData>  $messages
     */
    public function __construct(
        public array $messages,
        public \DateTime $expiredAt
    ) {}

    public function toArray(): array
    {
        $messages = [];

        foreach ($this->messages as $message) {
            $messages[] = $message->toArray();
        }

        return [
            'messages' => $messages,
            'expiredAt' => $this->expiredAt,
        ];
    }
}
