<?php declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

/**
 * @return array<Crab>
 */
function prepareData(string $data): array
{
	$crabs = [];
	foreach (toInt($data) as $position) {
		if (!isset($crabs[$position])) {
			$crabs[$position] = new Crab();
		}
		$crabs[$position]->addCrab();
	}

	ksort($crabs);

	return $crabs;
}

class Crab
{

	public int $count = 0;


	public function addCrab(): void
	{
		++$this->count;
	}

}

/**
 * @param array<Crab> $crabs
 * @return array<int, int>
 */
function fuelCountContant(array $crabs): array
{
	$positions = array_keys($crabs);
	$min = min($positions);
	$max = max($positions);

	$lastCounted = null;
	$data = [];
	for ($i = $min; $i < $max; ++$i) {
		$cpCrabs = $crabs;
		$count = 0;
		foreach ($cpCrabs as $position => $crab) {
			$count += abs($i - $position) * $crab->count;
			if ($lastCounted !== null && $count > $lastCounted) {
				break;
			}
		}
		if ($lastCounted === null || $count < $lastCounted) {
			$lastCounted = $count;
		}
		$data[$i] = $count;
	}

	return $data;
}


function countFuelForMove(int $from, int $to) :int {
	if ($from === $to) {
		return 0;
	} elseif ($from > $to) {
		$x = $from;
		$from = $to;
		$to = $x;
	}


	$move = $to - $from;
	$sum = 0;
	for ($i = 1; $i <= $move; ++$i) {
		$sum += $i;
	}
	return $sum;
}

/**
 * @param array<Crab> $crabs
 * @return array<int, int>
 */
function fuelCount(array $crabs): array
{
	$positions = array_keys($crabs);
	$min = min($positions);
	$max = max($positions);

	$lastCounted = null;
	$data = [];
	for ($i = $min; $i < $max; ++$i) {
		$cpCrabs = $crabs;
		$count = 0;
		foreach ($cpCrabs as $position => $crab) {
			$count += countFuelForMove($i, $position) * $crab->count;
			if ($lastCounted !== null && $count > $lastCounted) {
				break;
			}
		}
		if ($lastCounted === null || $count < $lastCounted) {
			$lastCounted = $count;
		}
		$data[$i] = $count;
	}

	return $data;
}

