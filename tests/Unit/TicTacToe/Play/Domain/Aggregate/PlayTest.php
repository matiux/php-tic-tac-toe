<?php

declare(strict_types=1);

namespace Tests\Unit\TicTacToe\Play\Domain\Aggregate;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use TicTacToe\Play\Domain\Aggregate\MatchStatus;
use TicTacToe\Play\Domain\Aggregate\Play;
use TicTacToe\Play\Domain\Aggregate\Player;
use TicTacToe\Play\Domain\Aggregate\PlayId;
use TicTacToe\Play\Domain\Exception\InvalidCelPositionException;
use TicTacToe\Play\Domain\Exception\InvalidPlayerException;
use TicTacToe\Play\Domain\Exception\NotEmptyCelException;

class PlayTest extends TestCase
{
    /**
     * @test
     */
    public function it_should_create_new_match(): void
    {
        $play = Play::newMatch(PlayId::create(), new DateTimeImmutable());

        $expectedStatus = MatchStatus::create(
            board: Play::drawEmptyBoard(),
            winning: false,
            playFinished: false,
            winningCombination: []
        );

        self::assertEquals($expectedStatus, $play->showStatus());
    }

    /**
     * @test
     */
    public function it_should_move(): void
    {
        $play = Play::newMatch(PlayId::create(), new DateTimeImmutable());
        $play->move(Player::X, 5);

        $board = Play::drawEmptyBoard();
        $board[1][2] = Player::X->value;

        $expectedStatus = MatchStatus::create(
            board: $board,
            winning: false,
            playFinished: false,
            winningCombination: []
        );

        self::assertEquals($expectedStatus, $play->showStatus());
    }

    /**
     * @test
     */
    public function it_should_throw_exception_if_not_empty_cell(): void
    {
        self::expectException(NotEmptyCelException::class);

        $play = Play::newMatch(PlayId::create(), new DateTimeImmutable());
        $play->move(Player::X, 5);
        $play->move(Player::X, 5);
    }

    /**
     * @test
     */
    public function it_should_throw_exception_if_not_valid_cell(): void
    {
        self::expectException(InvalidCelPositionException::class);

        $play = Play::newMatch(PlayId::create(), new DateTimeImmutable());
        $play->move(Player::X, 9);
    }

    /**
     * @test
     */
    public function it_should_throw_exception_if_invalid_player(): void
    {
        self::expectException(InvalidPlayerException::class);

        $play = Play::newMatch(PlayId::create(), new DateTimeImmutable());
        $play->move(Player::X, 5);
        $play->move(Player::X, 1);
    }

    /**
     * @return list<array{0: list<array<array-key, int>>, 1: list<int>}>
     */
    private function winnings(): array
    {
        return [
            [[['X' => 0], ['O' => 6], ['X' => 1], ['O' => 5], ['X' => 2]], [0, 1, 2]], // Horizontal winning X - 0 1 2
            [[['X' => 3], ['O' => 6], ['X' => 4], ['O' => 1], ['X' => 5]], [3, 4, 5]], // Horizontal winning X - 3 4 5
            [[['X' => 6], ['O' => 1], ['X' => 7], ['O' => 2], ['X' => 8]], [6, 7, 8]], // Horizontal winning X - 6 7 8
            [[['X' => 0], ['O' => 2], ['X' => 3], ['O' => 5], ['X' => 6]], [0, 3, 6]], // Vertical winning X - 0 3 6
            [[['X' => 1], ['O' => 6], ['X' => 4], ['O' => 2], ['X' => 7]], [1, 4, 7]], // Vertical winning X - 1 4 7
            [[['X' => 2], ['O' => 1], ['X' => 5], ['O' => 4], ['X' => 8]], [2, 5, 8]], // Vertical winning X - 2 5 8
            [[['X' => 0], ['O' => 1], ['X' => 4], ['O' => 5], ['X' => 8]], [0, 4, 8]], // Left to right winning X - 0 4 8
            [[['X' => 2], ['O' => 1], ['X' => 4], ['O' => 5], ['X' => 6]], [2, 4, 6]], // Right to left winning X - 2 4 6
        ];
    }

    /**
     * @dataProvider winnings
     * @test
     *
     * @param list<array<array-key, int>> $moves
     * @param int[]                       $winningCombination
     *
     * @throws InvalidCelPositionException
     * @throws InvalidPlayerException
     * @throws NotEmptyCelException
     */
    public function it_should_finish_with_winning(array $moves, array $winningCombination): void
    {
        $play = Play::newMatch(PlayId::create(), new DateTimeImmutable());
        $board = Play::drawEmptyBoard();

        foreach ($moves as $move) {
            $boardCellNumber = reset($move);
            $player = Player::from((string) array_key_first($move));

            $play->move($player, $boardCellNumber);

            $row = Play::getRowFromCelNumber($boardCellNumber);
            $col = Play::getColFromCelNumber($boardCellNumber);

            $board[$row][$col] = $player->value;
        }

        $expectedStatus = MatchStatus::create(
            board: $board,
            winning: true,
            playFinished: true,
            winningCombination: $winningCombination
        );

        self::assertEquals($expectedStatus, $play->showStatus());
    }

    /**
     * @test
     */
    public function it_should_not_move_if_winning(): void
    {
        $play = Play::newMatch(PlayId::create(), new DateTimeImmutable());

        $play->move(Player::X, 2);
        $play->move(Player::O, 1);
        $play->move(Player::X, 4);
        $play->move(Player::O, 5);
        $play->move(Player::X, 6);

        $board = Play::drawEmptyBoard();

        $board[0][2] = Player::X->value;
        $board[1][1] = Player::X->value;
        $board[2][0] = Player::X->value;
        $board[0][1] = Player::O->value;
        $board[1][2] = Player::O->value;

        $expectedStatus = MatchStatus::create(
            board: $board,
            winning: true,
            playFinished: true,
            winningCombination: [2, 4, 6]
        );

        self::assertEquals($expectedStatus, $play->showStatus());

        $play->move(Player::O, 0);

        self::assertEquals($expectedStatus, $play->showStatus());
    }

    /**
     * @test
     */
    public function it_should_finish_with_no_winner(): void
    {
        $play = Play::newMatch(PlayId::create(), new DateTimeImmutable());

        $play->move(Player::O, 0);
        $play->move(Player::X, 2);
        $play->move(Player::O, 1);
        $play->move(Player::X, 3);
        $play->move(Player::O, 5);
        $play->move(Player::X, 4);
        $play->move(Player::O, 6);
        $play->move(Player::X, 7);
        $play->move(Player::O, 8);

        $board = Play::drawEmptyBoard();

        $board[0][0] = Player::O->value;
        $board[0][1] = Player::O->value;
        $board[0][2] = Player::X->value;
        $board[1][0] = Player::X->value;
        $board[1][1] = Player::X->value;
        $board[1][2] = Player::O->value;
        $board[2][0] = Player::O->value;
        $board[2][1] = Player::X->value;
        $board[2][2] = Player::O->value;

        $expectedStatus = MatchStatus::create(
            board: $board,
            winning: false,
            playFinished: true,
            winningCombination: []
        );

        self::assertEquals($expectedStatus, $play->showStatus());
    }
}
