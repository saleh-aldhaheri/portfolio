<?php

namespace App\Data;

class ContextSummaryData
{
    public function __construct(
        public int $wordsCount,
        public int $charactersCount,
        public \DateTime $expiredAt,
    ) {}

    public function toArray(): array
    {
        return [
            'wordsCount' => $this->wordsCount,
            'charactersCount' => $this->charactersCount,
            'expiredAt' => $this->expiredAt,
        ];
    }
}
