<?php

declare(strict_types=1);

namespace TicTacToe\Shared\Application\Service\Clock;

use DateTimeImmutable;

class SystemClock implements Clock
{
    public function getCurrentTimeImmutable(): DateTimeImmutable
    {
        return new DateTimeImmutable();
    }
}
