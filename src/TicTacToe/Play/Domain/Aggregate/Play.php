<?php

declare(strict_types=1);

namespace TicTacToe\Play\Domain\Aggregate;

use DateTimeImmutable;
use TicTacToe\Play\Domain\Exception\InvalidCelPositionException;
use TicTacToe\Play\Domain\Exception\InvalidPlayerException;
use TicTacToe\Play\Domain\Exception\NotEmptyCelException;

class Play
{
    private const EMPTY_CELL = '-';
    private const NUM_ROWS = 3;
    private const NUM_COLS = 3;

    /** @var string[][] */
    private array $board = [];
    private string $lastPlayer; // TODO - Value Object con validazione intrinseca
    private bool $winning = false;

    /** @var int[] */
    private array $winningCombination = [];

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
                value: self::EMPTY_CELL
            )
        );
    }

    /**
     * @param string $player
     * @param int    $bordCellNumber
     *
     * @throws InvalidCelPositionException
     * @throws InvalidPlayerException
     * @throws NotEmptyCelException
     *
     * @return MatchStatus
     */
    public function move(string $player, int $bordCellNumber): MatchStatus
    {
        $this->moveIfNotWinning($player, $bordCellNumber);

        return $this->showStatus();
    }

    /**
     * @param string $player
     * @param int    $bordCellNumber
     *
     * @throws InvalidCelPositionException
     * @throws InvalidPlayerException
     * @throws NotEmptyCelException
     */
    private function moveIfNotWinning(string $player, int $bordCellNumber): void
    {
        if ($this->winning) {
            return;
        }
        // TODO - Specification pattern - Introduce Parameter Object - Preserve Whole Object
        $this->isValidCelOrFail($bordCellNumber);

        // TODO - Specification pattern
        $this->isCellEmptyOrFail(
            $this->getRowFromCelNumber($bordCellNumber),
            $this->getColFromCelNumber($bordCellNumber)
        );

        $this->setLastPlayerOrFail($player);
        $this->setMoveOnBoard($bordCellNumber);
        $this->setWinningIfWin();
    }

    public function showStatus(): MatchStatus
    {
        return MatchStatus::create(
            board: $this->board(),
            winning: $this->winning,
            playFinished: $this->isGameFinished(),
            winningCombination: $this->winningCombination,
        );
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
        if (self::EMPTY_CELL !== $this->board[$row][$col]) {
            throw new NotEmptyCelException();
        }
    }

    private function getRowFromCelNumber(int $bordCellNumber): int
    {
        return (int) floor($bordCellNumber / 3);
    }

    private function getColFromCelNumber(int $bordCellNumber): int
    {
        return $bordCellNumber % 3;
    }

    /**
     * @param string $player
     *
     * @throws InvalidPlayerException
     */
    private function setLastPlayerOrFail(string $player): void
    {
        if ($player === $this->lastPlayer) {
            throw new InvalidPlayerException();
        }

        $this->lastPlayer = $player;
    }

    private function setMoveOnBoard(int $bordCellNumber): void
    {
        $this->board[$this->getRowFromCelNumber($bordCellNumber)][$this->getColFromCelNumber($bordCellNumber)] = $this->lastPlayer;
    }

    private function setWinningIfWin(): void
    {
        if ($winningCombination = $this->isThereAWinner()) {
            $this->winning = true;
            $this->winningCombination = $winningCombination;
        }
    }

    private function isGameFinished(): bool
    {
        return $this->isBoardFull() || $this->winning;
    }

    private function isBoardFull(): bool
    {
        return !in_array(self::EMPTY_CELL, array_merge(...$this->board()));
    }

    /**
     * @return null|int[]
     */
    private function isThereAWinner(): null|array
    {
        if ($winnerLine = $this->isThereHorizontalWinner()) {
            return $winnerLine;
        }

        if ($winnerLine = $this->isThereVerticalWinner()) {
            return $winnerLine;
        }

        if ($winnerLine = $this->isThereDiagonalWinner()) {
            return $winnerLine;
        }

        return null;
    }

    /**
     * @return null|int[]
     */
    private function isThereHorizontalWinner(): null|array
    {
        if (0 > ($pos = $this->areThereEqualLines($this->board()))) {
            return null;
        }

        $row = $pos * 3;

        return range($row, $row + 2);
    }

    /**
     * @return null|int[]
     */
    private function isThereVerticalWinner(): null|array
    {
        $cols = [];

        for ($col = 0; $col < self::NUM_COLS; ++$col) {
            for ($row = 0; $row < self::NUM_ROWS; ++$row) {
                $cols[$col][] = $this->board[$row][$col];
            }
        }

        if (0 > ($pos = $this->areThereEqualLines($cols))) {
            return null;
        }

        $col = $pos;

        return [$col, $col + 3, $col + 6];
    }

    /**
     * TODO Refactor: extract method.
     *
     * @return null|int[]
     */
    private function isThereDiagonalWinner(): null|array
    {
        $diagonals = [];

        /**
         * Left to right: \.
         */
        for ($col = 0; $col < self::NUM_COLS; ++$col) {
            for ($row = 0; $row < self::NUM_ROWS; ++$row, ++$col) {
                $diagonals[0][] = $this->board[$row][$col];
            }
        }

        /**
         * Right to left: /.
         */
        for ($col = 2; $col >= 0; --$col) {
            for ($row = 0; $row < self::NUM_ROWS; ++$row, --$col) {
                $diagonals[1][] = $this->board[$row][$col];
            }
        }

        if (0 > ($pos = $this->areThereEqualLines($diagonals))) {
            return null;
        }

        return match ($pos) {
            0 => [0, 4, 8],
            1 => [2, 4, 6],
        };
    }

    /**
     * @param string[][] $lines
     *
     * @return int
     */
    private function areThereEqualLines(array $lines): int
    {
        foreach ($lines as $index => $line) {
            if ($this->isLineEqual($line)) {
                return (int) $index;
            }
        }

        return -1;
    }

    private function isLineEqual(array $line): bool
    {
        if (1 === count(array_unique($line)) && self::EMPTY_CELL !== current(array_unique($line))) {
            return true;
        }

        return false;
    }

    /**
     * @return string[][]
     */
    public function board(): array
    {
        return $this->board;
    }
}
