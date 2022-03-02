<?php

declare(strict_types=1);

namespace TicTacToe\Play\Application\DataTransformer;

use DDDStarterPack\DataTransformer\Application\BasicItemDataTransformer;
use TicTacToe\Play\Domain\Aggregate\MatchStatus;

/**
 * @property MatchStatus $item
 * @psalm-suppress MissingConstructor
 * @extends BasicItemDataTransformer<MatchStatus, array>
 */
class MatchStatusToArrayDataTransformer extends BasicItemDataTransformer
{
    public function read(): array
    {
        return [
            'board' => $this->item->board,
            'winning' => $this->item->winning,
            'play_finished' => $this->item->playFinished,
            'winning_combination' => $this->item->winningCombination,
        ];
    }
}
