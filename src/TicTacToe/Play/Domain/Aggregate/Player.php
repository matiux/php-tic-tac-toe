<?php

declare(strict_types=1);

namespace TicTacToe\Play\Domain\Aggregate;

enum Player: string
{
    case X = 'X';
    case O = 'O';
}
