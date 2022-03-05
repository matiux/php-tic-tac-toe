<?php

declare(strict_types=1);

namespace Tests\Unit\TicTacToe\Play\Application\Service;

use Mockery;
use PHPUnit\Framework\TestCase;
use TicTacToe\Play\Application\Service\StartNewGame;
use TicTacToe\Play\Domain\Aggregate\Matches;
use TicTacToe\Play\Domain\Aggregate\Play;
use TicTacToe\Play\Domain\Aggregate\PlayId;
use TicTacToe\Shared\Application\Service\Clock\Clock;

class StartNewGameTest extends TestCase
{
    private StartNewGame $service;
    private Matches $matches;
    private Clock $clock;

    protected function setUp(): void
    {
        $this->matches = Mockery::spy(Matches::class);
        $this->clock = Mockery::spy(Clock::class);

        $this->service = new StartNewGame(
            $this->matches,
            $this->clock,
        );
    }

    /**
     * @psalm-suppress UndefinedInterfaceMethod, MixedMethodCall
     * @test
     */
    public function it_should_create_new_game(): void
    {
        $status = $this->service->execute();

        self::assertTrue($status->isSuccess());

        $body = $status->body();
        self::assertInstanceOf(PlayId::class, $body);

        $this->matches->shouldHaveReceived('nextId')->once();
        $this->matches->shouldHaveReceived('add', [Play::class])->once();
        $this->clock->shouldHaveReceived('getCurrentTimeImmutable')->once();
    }
}
