<?php

declare(strict_types=1);

namespace TicTacToe\Play\Application\Service;

use DDDStarterPack\Service\Application\NoRequestApplicationService;
use TicTacToe\Play\Domain\Aggregate\Matches;
use TicTacToe\Play\Domain\Aggregate\Play;
use TicTacToe\Shared\Application\Service\Clock\SystemClock;

/**
 * @implements NoRequestApplicationService<StartNewGameResponse>
 */
class StartNewGame implements NoRequestApplicationService
{
    public function __construct(
        private Matches $matches,
        private SystemClock $systemClock
    ) {
    }

    /**
     * @param null $request
     *
     * @return StartNewGameResponse
     */
    public function execute($request = null): StartNewGameResponse
    {
        $play = Play::newMatch(
            $this->matches->nextId(),
            $this->systemClock->getCurrentTimeImmutable()
        );

        $this->matches->add($play);

        return StartNewGameResponse::success($play->playId);
    }
}
