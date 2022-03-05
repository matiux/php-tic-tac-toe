<?php

declare(strict_types=1);

namespace Tests\Support\TicTacToe\Play\Repository;

use TicTacToe\Play\Domain\Aggregate\Matches;
use TicTacToe\Play\Domain\Aggregate\Play;
use TicTacToe\Play\Domain\Aggregate\PlayId;

class InMemoryMatches implements Matches
{
    /** @var Play[] */
    private array $plays = [];

    public function nextId(): PlayId
    {
        return PlayId::create();
    }

    public function add(Play $aPlay): void
    {
        $this->plays[(string) $aPlay->playId] = $aPlay;
    }

    public function update(Play $aPlay): void
    {
        $this->plays[(string) $aPlay->playId] = $aPlay;
    }

    public function withId(PlayId $playId): null|Play
    {
        if (array_key_exists((string) $playId, $this->plays)) {
            return $this->plays[(string) $playId];
        }

        return null;
    }
}
