<?php

declare(strict_types=1);

namespace Tests\Unit\TicTacToe\Play\Domain\Aggregate;

use PHPUnit\Framework\TestCase;
use TicTacToe\Play\Domain\Aggregate\Player;
use ValueError;

class PlayerTest extends TestCase
{
    /**
     * @test
     */
    public function it_should_represent_valid_player(): void
    {
        $xPlayer = Player::from('X');
        self::assertSame('X', $xPlayer->value);
    }

    /**
     * @test
     */
    public function it_should_throw_exception_if_player_is_invalid(): void
    {
        self::expectException(ValueError::class);
        self::expectExceptionMessage(sprintf('"o" is not a valid backing value for enum "%s"', Player::class));

        Player::from('o');
    }
}
