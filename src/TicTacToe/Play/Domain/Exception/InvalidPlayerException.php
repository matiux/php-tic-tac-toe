<?php

declare(strict_types=1);

namespace TicTacToe\Play\Domain\Exception;

use DDDStarterPack\Exception\Domain\DomainException;

class InvalidPlayerException extends DomainException
{
    public const MESSAGE = 'Player non valido';
}
