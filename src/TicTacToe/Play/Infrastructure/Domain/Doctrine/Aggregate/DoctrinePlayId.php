<?php

declare(strict_types=1);

namespace TicTacToe\Play\Infrastructure\Domain\Doctrine\Aggregate;

use DDDStarterPack\Aggregate\Infrastructure\Doctrine\DoctrineEntityId;
use TicTacToe\Play\Domain\Aggregate\PlayId;

class DoctrinePlayId extends DoctrineEntityId
{
    protected function getFQCN(): string
    {
        return PlayId::class;
    }
}
