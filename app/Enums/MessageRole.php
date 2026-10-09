<?php

namespace App\Enums;

enum MessageRole: string
{
    case VISITOR = 'visitor';
    case ASSISTANT = 'assistant';

    public function label(): string
    {
        return match ($this) {
            self::VISITOR => 'Visitor',
            self::ASSISTANT => 'Assistant',
        };
    }
}
