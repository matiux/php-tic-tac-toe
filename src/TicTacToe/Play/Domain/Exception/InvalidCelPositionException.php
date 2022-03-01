<?php

declare(strict_types=1);

namespace TicTacToe\Play\Domain\Exception;

use DDDStarterPack\Exception\Domain\DomainException;

class InvalidCelPositionException extends DomainException
{
    public const MESSAGE = 'Posizione non valida';
}
