<?php declare(strict_types=1);

require __DIR__ . '/functions.php';

$source = '5483143223
2745854711
5264556173
6141336146
6357385478
4167524645
2176841721
6882881134
4846848554
5283751526' . PHP_EOL;

//$source = '11111
//19991
//19191
//19991
//11111' . PHP_EOL;

$data = prepareData($source);

// A
dump(countEnergyLevel100($data));

// B
dump(countEnergyLevel0($data));
