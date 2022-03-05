<?php

declare(strict_types=1);

namespace Tests\Unit\TicTacToe\Play\Application\DataTransformer;

use PHPUnit\Framework\TestCase;
use TicTacToe\Play\Application\DataTransformer\MatchStatusToArrayDataTransformer;
use TicTacToe\Play\Domain\Aggregate\MatchStatus;
use TicTacToe\Play\Domain\Aggregate\Play;

class MatchStatusToArrayDataTransformerTest extends TestCase
{
    /**
     * @test
     */
    public function it_should_transform__match_status_in_array(): void
    {
        $status = MatchStatus::create(
            board: Play::drawEmptyBoard(),
            winning: false,
            playFinished: false,
            winningCombination: []
        );

        $expectedArrayStatus = [
            'board' => [
                ['-', '-', '-'],
                ['-', '-', '-'],
                ['-', '-', '-'],
            ],
            'winning' => false,
            'play_finished' => false,
            'winning_combination' => [],
        ];

        $statusTransformed = (new MatchStatusToArrayDataTransformer())->write($status)->read();

        self::assertSame($expectedArrayStatus, $statusTransformed);
    }
}
