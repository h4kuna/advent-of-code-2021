<?php declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

$radarInput = __DIR__ . '/01input.txt';

/** @return array<int> */
function prepareData(string $radarInput): array
{
	$measurements = new \SplFileObject($radarInput);
	$data = [];
	foreach ($measurements as $measurement) {
		$value = (int) trim($measurement);
		if ($value !== 0) {
			$data[] = $value;
		}
	}

	return $data;
}


/**
 * @param array<int> $measurements
 */
function comparePreviousMeasurement(array $measurements): int
{
	$prev = 0;
	$increased = 0;
	foreach ($measurements as $value) {
		if ($prev !== 0 && $value > $prev) {
			++$increased;
		}

		$prev = $value;
	}

	return $increased;
}


/**
 * @param array<int> $measurements
 */
function compareMeasurementGroups(array $measurements): int
{
	$max = count($measurements);

	$data = [];
	for ($i = 2; $i < $max; ++$i) {
		$prepareData = array_slice($measurements, $i - 2, 3); // use for??
		$data[] = array_sum($prepareData);
	}

	return comparePreviousMeasurement($data);
}


// run

$measurements = prepareData($radarInput);

// A
dump(comparePreviousMeasurement($measurements));

// B
dump(compareMeasurementGroups($measurements));

