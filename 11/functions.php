<?php declare(strict_types=1);

use h4kuna\Iterators;

require __DIR__ . '/../vendor/autoload.php';

class Octopus
{
	public static int $count = 0;

	public int $value;

	public int $x;

	public int $y;

	public ?Octopus $next = null;


	public function __construct(int $value, int $x, int $y)
	{
		$this->value = $value;
		$this->x = $x;
		$this->y = $y;
	}


	public function isFlash(): bool
	{
		if ($this->value < 10) {
			++$this->value;
		}

		return $this->value === 10;
	}


	public function isFlashed(): bool
	{
		return $this->value === 0;
	}


	public function reset(): void
	{
		if ($this->value === 10) {
			++self::$count;
		}
		$this->value = 0;
	}


	public function isEmpty(): bool
	{
		if ($this->value === 0) {
			if ($this->next === null) {
				return true;
			}

			return $this->next->isEmpty();
		}

		return false;
	}

}

/**
 * @param array<Octopus> $stack
 * @param array<array<Octopus>> $data
 */
function runFlashing(array $stack, array $data): void
{
	foreach ($stack as $octopus) {
		//renderOctopus($data);
		octopusFlashing($octopus, $data);
	}
}


/**
 * @param array<array<Octopus>> $data
 */
function renderOctopus(array $data)
{
	foreach ($data as $rows) {
		foreach ($rows as $octopus) {
			echo $octopus->value;
		}
		echo PHP_EOL;
	}
	echo PHP_EOL;
}


/**
 * @param array<array<Octopus>> $data
 */
function countEnergyLevel100(array $data): int
{
	for ($i = 0; $i < 100; ++$i) {
		renderOctopus($data);
		$stack = [];
		foreach ($data as $rows) {
			foreach ($rows as $octopus) {
				assert($octopus instanceof Octopus);
				if ($octopus->isFlash()) {
					$stack[] = $octopus;
				}
			}
		}

		runFlashing($stack, $data);
	}

	renderOctopus($data);

	return Octopus::$count;
}


/**
 * @param array<array<Octopus>> $data
 */
function countEnergyLevel0(array $data): int
{
	$first = $data[0][0];
	$i = 1;
	do {
		$stack = [];
		foreach ($data as $rows) {
			foreach ($rows as $octopus) {
				assert($octopus instanceof Octopus);
				if ($octopus->isFlash()) {
					$stack[] = $octopus;
				}
			}
		}

		runFlashing($stack, $data);
		if ($first->isEmpty()) {
			renderOctopus($data);
			return $i + 100;
		}
	} while (++$i > 0);
}


/**
 * @param array<Octopus> $inStack
 * @param array<array<Octopus>> $data
 */
function octopusFlashing(Octopus $octopus, array $data): void
{
	if ($octopus->isFlashed()) {
		return;
	}
	$octopus->reset();
	$y = $octopus->y - 1;

	$maxY = $y + 3;
	for ($y = max($y, 0); $y < $maxY; ++$y) {
		if (!isset($data[$y])) {
			break;
		}
		$x = $octopus->x - 1;
		$maxX = $x + 3;
		for ($x = max(0, $x); $x < $maxX; ++$x) {
			if (!isset($data[$y][$x])) {
				break;
			}
			if (!$data[$y][$x]->isFlashed() && $data[$y][$x]->isFlash()) {
				octopusFlashing($data[$y][$x], $data);
			}
		}
	}
}


/**
 * @return array<array<Octopus>>
 */
function prepareData(string $data): array
{
	$matrix = [];
	$iterator = new Iterators\TextIterator($data);
	$iterator->setFlags($iterator::TRIM_LINE);
	$prev = null;
	foreach ($iterator as $y => $line) {
		foreach (toInt($line, '') as $x => $number) {
			$matrix[$y][$x] = new Octopus($number, $x, $y);
			if ($prev !== null) {
				$prev->next = $matrix[$y][$x];
			}
			$prev = $matrix[$y][$x];
		}
	}

	return $matrix;
}
