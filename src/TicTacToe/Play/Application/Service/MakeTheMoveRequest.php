<?php

declare(strict_types=1);

namespace TicTacToe\Play\Application\Service;

class MakeTheMoveRequest
{
    private function __construct(
        public readonly string $playId,
        public readonly string $player,
        public readonly int $bordCellNumber
    ) {
    }

    public static function create(string $playId, string $player, int $bordCellNumber): self
    {
        return new self($playId, $player, $bordCellNumber);
    }
}
