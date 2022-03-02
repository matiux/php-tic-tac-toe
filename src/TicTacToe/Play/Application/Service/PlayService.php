<?php

declare(strict_types=1);

namespace TicTacToe\Play\Application\Service;

use InvalidArgumentException;
use TicTacToe\Play\Domain\Aggregate\Matches;
use TicTacToe\Play\Domain\Aggregate\Play;
use TicTacToe\Play\Domain\Aggregate\PlayId;

abstract class PlayService
{
    public function __construct(
        protected Matches $matches,
    ) {
    }

    protected function loadPlayOrFail(PlayId $playId): Play
    {
        if (!$play = $this->matches->withId($playId)) {
            throw new InvalidArgumentException(sprintf('Play not found with ID %s', (string) $playId));
        }

        return $play;
    }
}
