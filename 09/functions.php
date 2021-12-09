<?php declare(strict_types=1);

use h4kuna\Iterators;

require __DIR__ . '/../vendor/autoload.php';

class Number
{
	public int $value;

	public int $x;

	public int $y;

	public ?Number $parent = null;

	public string $id;


	public function __construct(int $value, int $x, int $y)
	{
		$this->value = $value;
		$this->x = $x;
		$this->y = $y;
	}


	public function setParent(Number $number): void
	{
		$this->parent = $number;
	}

}

/**
 * @param array<array<Number>> $matrix
 */
function findLowPoints(array $matrix): array
{
	foreach ($matrix as $y => $row) {
		foreach ($row as $x => $number) {
			markParent($x, $y, $matrix);
		}
	}

	$lowest = [];
	$sum = 0;
	foreach ($matrix as $row) {
		foreach ($row as $number) {
			if ($number->parent === null) {
				$lowest[] = $number;
				$sum += $number->value + 1;
			}
		}
	}

	return [$sum, $lowest];
}


/**
 * @param array<array<Number>> $matrix
 */
function compareCell(int $x1, int $y1, int $x2, int $y2, array $matrix): void
{
	if (!isset($matrix[$y2][$x2])) {
		return;
	}

	if ($matrix[$y1][$x1]->value <= $matrix[$y2][$x2]->value) {
		$matrix[$y2][$x2]->setParent($matrix[$y1][$x1]);
	}
}


/**
 * @param array<array<Number>> $matrix
 */
function markParent(int $x, int $y, array $matrix): void
{
	compareCell($x, $y, $x - 1, $y, $matrix);
	compareCell($x, $y, $x + 1, $y, $matrix);
	compareCell($x, $y, $x, $y - 1, $matrix);
	compareCell($x, $y, $x, $y + 1, $matrix);
}


/**
 * @return array<array<Number>>
 */
function prepareData(string $data): array
{
	$matrix = [];
	$input = new Iterators\TextIterator($data);
	$input->setFlags($input::TRIM_LINE);
	foreach ($input as $y => $line) {
		foreach (str_split($line) as $x => $number) {
			$matrix[$y][$x] = new Number((int) $number, $x, $y);
		}
	}

	return $matrix;
}


/**
 * @param array<array<Number>> $matrix
 * @param array<Number> $lowest
 */
function basinsCount(array $matrix, array $lowest): int
{
	$counts = [];
	foreach ($lowest as $number) {
		$counts[] = startSearch($number, $matrix);
	}

	sort($counts);

	return array_product(array_slice($counts, -3));
}


/**
 * @param array<array<Number>> $matrix
 */
function render(array $matrix): void
{
	echo "\033[37m";
	foreach ($matrix as $line) {
		foreach ($line as $number) {
			if ($number->parent === null) {
				echo "\033[32m";
			}
			echo $number->value;
			if ($number->parent === null) {
				echo "\033[37m";
			}
		}
		echo PHP_EOL;
	}
}


function startSearch(?Number $number, array &$matrix)
{
	if ($number === null || $number->value === 9) {
		return 0;
	}
	$matrix[$number->y][$number->x] = null;

	return startSearch($matrix[$number->y + 1][$number->x] ?? null, $matrix) +
		startSearch($matrix[$number->y - 1][$number->x] ?? null, $matrix) +
		startSearch($matrix[$number->y][$number->x + 1] ?? null, $matrix) +
		startSearch($matrix[$number->y][$number->x - 1] ?? null, $matrix) + 1;
}









