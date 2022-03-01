<?php

declare(strict_types=1);

namespace TicTacToe\Play\Application\Service;

use DDDStarterPack\Service\Application\ApplicationService;
use InvalidArgumentException;
use TicTacToe\Play\Domain\Aggregate\Matches;
use TicTacToe\Play\Domain\Aggregate\Play;
use TicTacToe\Play\Domain\Aggregate\PlayId;
use TicTacToe\Play\Domain\Exception\InvalidPlayerException;
use TicTacToe\Play\Domain\Exception\NotEmptyCelException;

/**
 * @implements ApplicationService<MakeTheMoveRequest, MakeTheMoveResponse>
 */
class MakeTheMove implements ApplicationService
{
    public function __construct(
        private Matches $matches
    ) {
    }

    /**
     * @param MakeTheMoveRequest $makeTheMoveRequest
     *
     * @throws InvalidPlayerException
     * @throws NotEmptyCelException
     *
     * @return MakeTheMoveResponse
     */
    public function execute($makeTheMoveRequest): MakeTheMoveResponse
    {
        $play = $this->loadPlayOrFail(PlayId::createFrom($makeTheMoveRequest->playId));

        $play->move($makeTheMoveRequest->player, $makeTheMoveRequest->bordCellNumber);

        $this->matches->update($play);

        return MakeTheMoveResponse::success($play->board());
    }

    private function loadPlayOrFail(PlayId $playId): Play
    {
        if (!$play = $this->matches->withId($playId)) {
            throw new InvalidArgumentException();
        }

        return $play;
    }
}
