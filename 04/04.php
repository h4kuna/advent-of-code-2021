<?php declare(strict_types=1);

use h4kuna\Iterators;
use Nette\Utils\Strings;

require __DIR__ . '/../vendor/autoload.php';

$bingoInput = __DIR__ . '/04input.txt';

final class BingoNumber
{
	public int $value;

	public bool $isMarked = false;


	public function __construct(int $value)
	{
		$this->value = $value;
	}


	public function mark(): void
	{
		$this->isMarked = true;
	}

}

final class BingoBoard
{
	/**
	 * @var array<array<BingoNumber>>
	 */
	private array $board = [];


	public function addRow(string $row): void
	{
		$this->board[] = self::prepareRow($row);
	}


	public function isFull(): bool
	{
		return self::checkNumbers($this->board);
	}


	public function mark(int $number, bool $checkWin): bool
	{
		foreach ($this->board as $x => $row) {
			foreach ($row as $y => $bingoNumber) {
				if ($bingoNumber->value === $number) {
					$bingoNumber->mark();
					if ($checkWin === true) {
						return $this->checkWin($x, $y);
					}

					return false;
				}
			}
		}

		return false;
	}


	public function sumUnmarked(): int
	{
		$sum = 0;
		foreach ($this->board as $row) {
			foreach ($row as $bingoNumber) {
				if (!$bingoNumber->isMarked) {
					$sum += $bingoNumber->value;
				}
			}
		}

		return $sum;
	}


	/**
	 * @param array<BingoNumber|array<BingoNumber>>
	 */
	private static function checkNumbers(array $numbers): bool
	{
		return count($numbers) === 5;
	}


	private static function prepareRow(string $row)
	{
		$numbers = [];
		foreach (Strings::split($row, '/\s+/') as $number) {
			$numbers[] = new BingoNumber((int) $number);
		}

		if (self::checkNumbers($numbers) === false) {
			throw new \Exception('Invalid input');
		}

		return $numbers;
	}


	private function checkWin(int $x, int $y): bool
	{
		return $this->checkWinX($x) || $this->checkWinY($y);
	}


	private function checkWinX(int $x): bool
	{
		foreach ($this->board[$x] as $number) {
			if ($number->isMarked === false) {
				return false;
			}
		}

		return true;
	}


	private function checkWinY(int $y): bool
	{
		foreach ($this->board as $numbers) {
			if ($numbers[$y]->isMarked === false) {
				return false;
			}
		}

		return true;
	}

}

class BingoBoards
{
	/**
	 * @var array<BingoBoard>
	 */
	private array $boards;


	public function __construct(array $boards)
	{
		$this->boards = $boards;
	}


	public function mark(int $number, bool $checkWin): ?int
	{
		foreach ($this->boards as $board) {
			$isWin = $board->mark($number, $checkWin);
			if ($isWin === true) {
				return $board->sumUnmarked() * $number;
			}
		}

		return null;
	}


	public function markAsLast(int $number, bool $checkWin): ?int
	{
		$lastWinBoard = null;
		foreach ($this->boards as $pos => $board) {
			$isWin = $board->mark($number, $checkWin);
			if ($isWin) {
				$lastWinBoard = $board;
				unset($this->boards[$pos]);
			}
		}

		if ($this->boards !== []) {
			return null;
		} elseif ($lastWinBoard === null) {
			throw new \Exception('$lastWinBoard could not be empty');
		}

		return $lastWinBoard->sumUnmarked() * $number;
	}

}

class Bingo
{
	public BingoBoards $bingoBoards;

	/** @var array<int> */
	public array $numbers;


	public function __construct(BingoBoards $bingoBoard, array $numbers)
	{
		$this->bingoBoards = $bingoBoard;
		$this->numbers = $numbers;
	}

}

function prepareData(string $bingoInput): Bingo
{
	$iterator = new Iterators\TextIterator(file_get_contents($bingoInput));
	$iterator->setFlags($iterator::TRIM_LINE);

	$boards = $numbers = [];
	$board = null;
	foreach ($iterator as $line => $row) {
		if ($row === '') {
			if ($board !== null && $board->isFull()) {
				$boards[] = $board;
			}
			$board = new BingoBoard();
		} elseif ($line === 0) {
			$numbers = toInt($row);
		} else {
			$board->addRow($row);
		}
	}
	if ($board !== null && $board->isFull()) {
		$boards[] = $board;
	}

	return new Bingo(new BingoBoards($boards), $numbers);
}


function playGame(Bingo $bingo): int
{
	foreach ($bingo->numbers as $i => $number) {
		$value = $bingo->bingoBoards->mark($number, $i > 4);
		if ($value !== null) {
			return $value;
		}
	}

	throw new \Exception('Now win!');
}


function playGameToFail(Bingo $bingo)
{
	foreach ($bingo->numbers as $i => $number) {
		$value = $bingo->bingoBoards->markAsLast($number, $i > 4);
		if ($value !== null) {
			return $value;
		}
	}

	throw new \Exception('Now win!');
}


// run
$bingo = prepareData($bingoInput);

// A
dump(playGame($bingo));

// B
dump(playGameToFail($bingo));
