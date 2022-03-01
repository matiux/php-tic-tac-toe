<?php

declare(strict_types=1);

namespace TicTacToe\Play\Domain\Aggregate;

use DateTimeImmutable;
use TicTacToe\Play\Domain\Exception\InvalidCelPositionException;
use TicTacToe\Play\Domain\Exception\InvalidPlayerException;
use TicTacToe\Play\Domain\Exception\NotEmptyCelException;

class Play
{
    private const NUM_ROWS = 3;
    private const NUM_COLS = 3;

    private array $board = [];
    private string $lastPlayer; // TODO - Value Object con validazione intrinseca

    private function __construct(
        public readonly PlayId $playId,
        private DateTimeImmutable $startDate
    ) {
        $this->initializeBoard();
        $this->lastPlayer = '';
    }

    public static function newMatch(PlayId $playId, DateTimeImmutable $startDate): self
    {
        return new self($playId, $startDate);
    }

    private function initializeBoard(): void
    {
        $this->board = array_fill(
            start_index: 0,
            count: self::NUM_ROWS,
            value: array_fill(
                start_index: 0,
                count: self::NUM_COLS,
                value: null
            )
        );
    }

    /**
     * @param string $player
     * @param int    $bordCellNumber
     *
     * @throws InvalidCelPositionException|InvalidPlayerException|NotEmptyCelException
     */
    public function move(string $player, int $bordCellNumber): void
    {
        // TODO - Specification pattern - Introduce Parameter Object - Preserve Whole Object
        $this->isValidCelOrFail($bordCellNumber);

        $row = $this->getRowFromCelNumber($bordCellNumber);
        $col = $this->getColFromCelNumber($bordCellNumber);

        // TODO - Specification pattern
        $this->isCellEmptyOrFail($row, $col);
        $this->isValidPlayerOrFail($player);

        $this->board[$row][$col] = $player;
        $this->lastPlayer = $player;

        $this->isGameFinished();
    }

    /**
     * @param int $bordCellNumber
     *
     * @throws InvalidCelPositionException
     */
    private function isValidCelOrFail(int $bordCellNumber): void
    {
        if ($bordCellNumber < 0 || $bordCellNumber >= self::NUM_ROWS * self::NUM_COLS) {
            throw new InvalidCelPositionException();
        }
    }

    /**
     * @param int $row
     * @param int $col
     *
     * @throws NotEmptyCelException
     */
    private function isCellEmptyOrFail(int $row, int $col): void
    {
        if (null !== $this->board[$row][$col]) {
            throw new NotEmptyCelException();
        }
    }

    /**
     * @param string $player
     *
     * @throws InvalidPlayerException
     */
    private function isValidPlayerOrFail(string $player): void
    {
        if ($player === $this->lastPlayer) {
            throw new InvalidPlayerException();
        }
    }

    private function getRowFromCelNumber(int $bordCellNumber): int
    {
        return (int) floor($bordCellNumber / 3);
    }

    private function getColFromCelNumber(int $bordCellNumber): int
    {
        return (int) $bordCellNumber % 3;
    }

    public function board(): array
    {
        return $this->board;
    }

    private function isGameFinished(): bool
    {
    }
}
