<?php

declare(strict_types=1);

namespace TicTacToe\Play\Domain\Exception;

use DDDStarterPack\Exception\Domain\DomainException;

class NotEmptyCelException extends DomainException
{
    public const MESSAGE = 'Cella non libera';
}
