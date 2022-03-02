<?php

declare(strict_types=1);

namespace TicTacToe\Play\Domain\Aggregate;

class MatchStatus
{
    public function __construct(
        public readonly array $board,
        public readonly bool $winning,
        public readonly bool $playFinished,
        public readonly array $winningCombination,
    ) {
    }

    public static function create(array $board, bool $winning, bool $playFinished, array $winningCombination): self
    {
        return new self($board, $winning, $playFinished, $winningCombination);
    }
}
