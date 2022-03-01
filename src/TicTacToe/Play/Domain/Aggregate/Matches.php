<?php

declare(strict_types=1);

namespace TicTacToe\Play\Domain\Aggregate;

interface Matches
{
    public function nextId(): PlayId;

    public function add(Play $aPlay): void;

    public function update(Play $aPlay): void;

    public function withId(PlayId $playId): null|Play;
}
