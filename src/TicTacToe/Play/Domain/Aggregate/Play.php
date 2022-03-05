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
    private null|Player $lastPlayer;
    private bool $winning = false;

    /** @var int[] */
    private array $winningCombination = [];

    private function __construct(
        public readonly PlayId $playId,
        private DateTimeImmutable $startDate
    ) {
        $this->lastPlayer = null;
        $this->initializeBoard();
    }

    public static function newMatch(PlayId $playId, DateTimeImmutable $startDate): self
    {
        return new self($playId, $startDate);
    }

    private function initializeBoard(): void
    {
        $this->board = self::drawEmptyBoard();
    }

    /**
     * @return string[][]
     */
    public static function drawEmptyBoard(): array
    {
        return array_fill(
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
     * @param Player $player
     * @param int    $bordCellNumber
     *
     * @throws InvalidCelPositionException
     * @throws InvalidPlayerException
     * @throws NotEmptyCelException
     *
     * @return MatchStatus
     */
    public function move(Player $player, int $bordCellNumber): MatchStatus
    {
        $this->moveIfNotWinning($player, $bordCellNumber);

        return $this->showStatus();
    }

    /**
     * @param Player $player
     * @param int    $bordCellNumber
     *
     * @throws InvalidCelPositionException
     * @throws InvalidPlayerException
     * @throws NotEmptyCelException
     */
    private function moveIfNotWinning(Player $player, int $bordCellNumber): void
    {
        if ($this->winning) {
            return;
        }
        // TODO - Specification pattern - Introduce Parameter Object - Preserve Whole Object
        $this->isValidCelOrFail($bordCellNumber);
        $this->isCellEmptyOrFail($bordCellNumber);
        $this->setLastPlayerIfValid($player);
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
     * @param int $boardCellNumber
     *
     * @throws InvalidCelPositionException
     */
    private function isValidCelOrFail(int $boardCellNumber): void
    {
        if ($boardCellNumber < 0 || $boardCellNumber >= self::NUM_ROWS * self::NUM_COLS) {
            throw new InvalidCelPositionException();
        }
    }

    /**
     * @param int $boardCellNumber
     *
     * @throws NotEmptyCelException
     */
    private function isCellEmptyOrFail(int $boardCellNumber): void
    {
        $row = self::getRowFromCelNumber($boardCellNumber);
        $col = self::getColFromCelNumber($boardCellNumber);

        if (self::EMPTY_CELL !== $this->board[$row][$col]) {
            throw new NotEmptyCelException();
        }
    }

    /**
     * @param Player $player
     *
     * @throws InvalidPlayerException
     */
    private function setLastPlayerIfValid(Player $player): void
    {
        if ($player === $this->lastPlayer) {
            throw new InvalidPlayerException();
        }

        $this->lastPlayer = $player;
    }

    private function setMoveOnBoard(int $boardCellNumber): void
    {
        $row = self::getRowFromCelNumber($boardCellNumber);
        $col = self::getColFromCelNumber($boardCellNumber);

        $this->board[$row][$col] = (string) $this->lastPlayer?->value;
    }

    public static function getRowFromCelNumber(int $boardCellNumber): int
    {
        return (int) floor($boardCellNumber / 3);
    }

    public static function getColFromCelNumber(int $boardCellNumber): int
    {
        return $boardCellNumber % 3;
    }

    private function setWinningIfWin(): void
    {
        if ($winningCombination = $this->isThereAWinner()) {
            $this->winning = true;
            $this->winningCombination = $winningCombination;
        }
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

    private function isGameFinished(): bool
    {
        return $this->isBoardFull() || $this->winning;
    }

    private function isBoardFull(): bool
    {
        return !in_array(self::EMPTY_CELL, array_merge(...$this->board()));
    }

    /**
     * @return string[][]
     */
    private function board(): array
    {
        return $this->board;
    }
}
