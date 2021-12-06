<?php declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

$lanternfishData = __DIR__ . '/06input.txt';

/**
 * @return array<int, Lanternfish>
 */
function prepareData(string $lanternfishData): array
{
	/** @var array<int, Lanternfish> $lanternfishs */
	$lanternfishs = [];
	for ($i = 8; $i >= 0; --$i) {
		$lanternfishs[$i] = new Lanternfish();
	}

	$data = toInt(trim(file_get_contents($lanternfishData)));
	foreach ($data as $pointer) {
		$lanternfishs[$pointer]->addFish();
	}

	return $lanternfishs;
}

final class Lanternfish
{
	private int $count = 0;


	public function addFish(int $count = 1): void
	{
		$this->count += $count;
	}


	public function count(): int
	{
		return $this->count;
	}

}

/**
 * @param array<Lanternfish> $lanternfishs
 */
function countBornedNDays(array &$lanternfishs, int $days): int
{
	for ($i = 0; $i < $days; ++$i) {
		$old = $lanternfishs[0];
		$prev = clone $lanternfishs[0];
		foreach ($lanternfishs as $pointer => $lanternfish) {
			$lanternfishs[$pointer] = $prev;
			if ($pointer === 6) {
				$prev->addFish($old->count());
			}
			$prev = $lanternfish;
		}
	}

	$sum = 0;
	foreach ($lanternfishs as $lanternfish) {
		$sum += $lanternfish->count();
	}

	return $sum;
}


// run
$lanternfishs = prepareData($lanternfishData);

// A
dump(countBornedNDays($lanternfishs, 80));
// 5934 ok
// my: 388739

// B
dump(countBornedNDays($lanternfishs, 256 - 80));
// my: 1741362314973
