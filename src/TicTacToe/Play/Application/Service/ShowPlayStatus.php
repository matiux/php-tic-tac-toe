<?php

declare(strict_types=1);

namespace TicTacToe\Play\Application\Service;

use DDDStarterPack\DataTransformer\Application\Type\ItemDataTransformer;
use DDDStarterPack\Service\Application\ApplicationService;
use TicTacToe\Play\Domain\Aggregate\Matches;
use TicTacToe\Play\Domain\Aggregate\PlayId;

/**
 * @implements ApplicationService<string, PlayResponse>
 */
class ShowPlayStatus extends PlayService implements ApplicationService
{
    public function __construct(
        Matches $matches,
        private ItemDataTransformer $dataTransformer,
    ) {
        parent::__construct($matches);
    }

    /**
     * @param string $playId
     * @psalm-assert string $playId
     *
     * @return PlayResponse
     */
    public function execute($playId): PlayResponse
    {
        $play = $this->loadPlayOrFail(PlayId::createFrom($playId));

        return PlayResponse::success(
            $this->dataTransformer->write($play->showStatus())->read()
        );
    }
}
