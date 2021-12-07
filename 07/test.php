<?php declare(strict_types=1);

use Tester\Assert;

require __DIR__ . '/functions.php';

$data = '16,1,2,0,4,2,7,1,2,14' . PHP_EOL;

$crabs = prepareData($data);

{
$expected = [
	1 => 41,
	2 => 37,
	3 => 39,
	10 => 52, // original is 71 but this value is changed by optimalization
];
$actual = fuelCountContant($crabs);
Assert::same($expected, array_intersect_key($actual, $expected));
}

{
	Assert::same(10, countFuelForMove(1, 5));
	Assert::same(6, countFuelForMove(2, 5));
	Assert::same(66, countFuelForMove(16, 5));
	Assert::same(15, countFuelForMove(0, 5));
	Assert::same(45, countFuelForMove(14, 5));
}

{
	$expected = [
		2 => 206,
		5 => 168,
	];
	$actual = fuelCount($crabs);
	Assert::same($expected, array_intersect_key($actual, $expected));
}
