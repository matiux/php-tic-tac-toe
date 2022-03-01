<?php

declare(strict_types=1);

namespace TicTacToe\Play\Application\Service;

use DDDStarterPack\Service\Application\Response\BasicApplicationServiceResponse;

class MakeTheMoveResponse extends BasicApplicationServiceResponse
{
    protected function errorCode(): int
    {
        return self::ERROR_CODE;
    }

    protected function successCode(): int
    {
        return self::SUCCESS_CODE;
    }
}
