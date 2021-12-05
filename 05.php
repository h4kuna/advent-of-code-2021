<?php declare(strict_types=1);

use h4kuna\Iterators;
use Nette\Utils\Strings;

require __DIR__ . '/vendor/autoload.php';

$hydrothermalData = __DIR__ . '/05input.txt';

function prepareData(string $hydrothermalData): BoardMeta
{
	$iterator = new Iterators\TextIterator(file_get_contents($hydrothermalData));
	$iterator->setFlags($iterator::TRIM_LINE);

	$boardMeta = new BoardMeta();
	foreach ($iterator as $row) {
		$match = Strings::match($row, '/(?<x1>\d+),(?<y1>\d+) -> (?<x2>\d+),(?<y2>\d+)/');
		$line = new Line(new Point((int) $match['x1'], (int) $match['y1']), new Point((int) $match['x2'], (int) $match['y2']));
		$boardMeta->addLine($line);
	}

	return $boardMeta;
}

class BoardMeta
{
	/** @var array<Line> */
	public array $lines = [];

	public int $maxY = 0;

	public int $maxX = 0;


	public function addLine(Line $line)
	{
		$this->lines[] = $line;
		$this->maxX = max($line->pointA->x, $line->pointB->x, $this->maxX);
		$this->maxY = max($line->pointA->y, $line->pointB->y, $this->maxY);
	}


	public function horizontalAndVertical(): \Generator
	{
		foreach ($this->lines as $line) {
			if ($line->isHorizontal() || $line->isVertical()) {
				yield $line;
			}
		}
	}


	public function diagonal(): \Generator
	{
		foreach ($this->lines as $line) {
			if ($line->isDiagonal()) {
				yield $line;
			}
		}
	}

}

class Point
{
	public int $x;

	public int $y;


	public function __construct(int $x, int $y)
	{
		$this->x = $x;
		$this->y = $y;
	}

}

class Line
{
	public Point $pointA;

	public Point $pointB;


	public function __construct(Point $pointA, Point $pointB)
	{
		$this->pointA = $pointA;
		$this->pointB = $pointB;
	}


	public function isHorizontal(): bool
	{
		return $this->pointA->x === $this->pointB->x;
	}


	public function xMin(): int
	{
		return min($this->pointA->x, $this->pointB->x);
	}


	public function xMax(): int
	{
		return max($this->pointA->x, $this->pointB->x);
	}


	public function yMin(): int
	{
		return min($this->pointA->y, $this->pointB->y);
	}


	public function yMax(): int
	{
		return max($this->pointA->y, $this->pointB->y);
	}


	public function isVertical(): bool
	{
		return $this->pointA->y === $this->pointB->y;
	}


	public function isDiagonal(): bool
	{
		return abs($this->pointA->x - $this->pointB->x) === abs($this->pointA->y - $this->pointB->y);
	}

}

/**
 * @return array<array<int>>
 */
function createBoard(BoardMeta $boardMeta): array
{
	$row = array_fill(0, $boardMeta->maxY + 1, 0);

	return array_fill(0, $boardMeta->maxX + 1, $row);
}


/**
 * @param array<array<int>> $board
 */
function matchWay(array $board): int
{
	$count = 0;
	foreach ($board as $row) {
		foreach ($row as $value) {
			if ($value > 1) {
				++$count;
			}
		}
	}

	return $count;
}


function avoidVents(\Generator $data, array &$board): int
{
	foreach ($data as $line) {
		assert($line instanceof Line);
		if ($line->isHorizontal()) {
			$max = $line->yMax();
			$x = $line->pointA->x;
			for ($y = $line->yMin(); $y <= $max; ++$y) {
				++$board[$x][$y];
			}
		} elseif ($line->isVertical()) {
			$max = $line->xMax();
			$y = $line->pointA->y;
			for ($x = $line->xMin(); $x <= $max; ++$x) {
				++$board[$x][$y];
			}
		} elseif ($line->isDiagonal()) {
			$moveX = ($line->pointB->x - $line->pointA->x) > 0 ? 1 : -1;
			$moveY = ($line->pointB->y - $line->pointA->y) > 0 ? 1 : -1;

			$max = $line->xMax();
			$x = $line->pointA->x;
			$y = $line->pointA->y;
			for ($i = $line->xMin(); $i <= $max; ++$i) {
				++$board[$x][$y];
				$x += $moveX;
				$y += $moveY;
			}
		} else {
			throw new \Exception('Unknown option');
		}
	}

	return matchWay($board);
}


// run
$boardMeta = prepareData($hydrothermalData);
$board = createBoard($boardMeta);

//A
dump(avoidVents($boardMeta->horizontalAndVertical(), $board));

//B
dump(avoidVents($boardMeta->diagonal(), $board));
// 22088
