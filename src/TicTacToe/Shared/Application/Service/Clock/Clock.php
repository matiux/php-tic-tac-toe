<?php

declare(strict_types=1);

namespace TicTacToe\Shared\Application\Service\Clock;

use DateTimeImmutable;

interface Clock
{
    public function getCurrentTimeImmutable(): DateTimeImmutable;
}
