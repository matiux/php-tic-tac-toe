<?php

declare(strict_types=1);

namespace TicTacToe\Play\Application\Service;

use DDDStarterPack\DataTransformer\Application\Type\ItemDataTransformer;
use DDDStarterPack\Service\Application\ApplicationService;
use TicTacToe\Play\Domain\Aggregate\Matches;
use TicTacToe\Play\Domain\Aggregate\PlayId;
use TicTacToe\Play\Domain\Exception\InvalidCelPositionException;
use TicTacToe\Play\Domain\Exception\InvalidPlayerException;
use TicTacToe\Play\Domain\Exception\NotEmptyCelException;

/**
 * @implements ApplicationService<MakeTheMoveRequest, PlayResponse>
 */
class MakeTheMove extends PlayService implements ApplicationService
{
    public function __construct(
        Matches $matches,
        private ItemDataTransformer $dataTransformer,
    ) {
        parent::__construct($matches);
    }

    /**
     * @param MakeTheMoveRequest $makeTheMoveRequest
     *
     * @throws InvalidCelPositionException|InvalidPlayerException|NotEmptyCelException
     *
     * @return PlayResponse
     */
    public function execute($makeTheMoveRequest): PlayResponse
    {
        $play = $this->loadPlayOrFail(PlayId::createFrom($makeTheMoveRequest->playId));

        $matchStatus = $play->move($makeTheMoveRequest->player, $makeTheMoveRequest->bordCellNumber);

        $this->matches->update($play);
        $r = $this->dataTransformer->write($matchStatus)->read();

        return PlayResponse::success(
            $r
        );
    }
}
