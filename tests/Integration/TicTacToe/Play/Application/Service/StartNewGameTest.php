<?php

declare(strict_types=1);

namespace Tests\Integration\TicTacToe\Play\Application\Service;

use PHPUnit\Framework\TestCase;
use Tests\Support\TicTacToe\Play\Repository\InMemoryMatches;
use TicTacToe\Play\Application\Service\StartNewGame;
use TicTacToe\Play\Domain\Aggregate\PlayId;
use TicTacToe\Shared\Application\Service\Clock\SystemClock;

class StartNewGameTest extends TestCase
{
    private StartNewGame $service;

    protected function setUp(): void
    {
        $this->service = new StartNewGame(
            new InMemoryMatches(),
            new SystemClock()
        );
    }

    /**
     * @test
     */
    public function it_should_create_new_game(): void
    {
        $status = $this->service->execute();

        self::assertTrue($status->isSuccess());

        $body = $status->body();
        self::assertInstanceOf(PlayId::class, $body);
    }
}
