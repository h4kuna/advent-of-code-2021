<?php declare(strict_types=1);

use Nette\Utils;

require __DIR__ . '/../vendor/autoload.php';

$diveInput = __DIR__ . '/02input.txt';

class Movement
{
	public string $direction;

	public int $step;


	public function __construct(string $direction, int $step)
	{
		$this->direction = $direction;
		$this->step = $step;
	}

}

/**
 * @return array<Movement>
 */
function prepareData(string $diveInput): array
{
	$movements = new \SplFileObject($diveInput);
	$data = [];
	foreach ($movements as $measurement) {
		$match = Utils\Strings::match($measurement, '/(?<direction>\w+) (?<step>\d+)/');
		if ($match !== null) {
			$data[] = new Movement($match['direction'], (int) $match['step']);
		}
	}

	return $data;
}


/**
 * @param array<Movement> $diveData
 */
function dive(array $diveData): int
{
	$position = 0;
	$depth = 0;
	foreach ($diveData as $movement) {
		switch ($movement->direction) {
			case 'forward':
				$position += $movement->step;
				break;
			case 'down':
				$depth += $movement->step;
				break;
			case 'up':
				$depth -= $movement->step;
				break;
			default:
				throw new \Exception(sprintf('Invalid movement "%s"', $movement->direction));
		}
	}

	return $depth * $position;
}


/**
 * @param array<Movement> $diveData
 */
function diveAim(array $diveData): int
{
	$position = 0;
	$depth = 0;
	$aim = 0;
	foreach ($diveData as $movement) {
		switch ($movement->direction) {
			case 'forward':
				$position += $movement->step;
				$depth += $movement->step * $aim;
				break;
			case 'down':
				$aim += $movement->step;
				break;
			case 'up':
				$aim -= $movement->step;
				break;
			default:
				throw new \Exception(sprintf('Invalid movement "%s"', $movement->direction));
		}
	}

	return $depth * $position;
}


// run

$movements = prepareData($diveInput);

// A
dump(dive($movements));

// B
dump(diveAim($movements));

