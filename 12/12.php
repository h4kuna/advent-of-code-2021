<?php declare(strict_types=1);

require __DIR__ . '/functions.php';

$positionsInput = __DIR__ . '/12input.txt';

$source = file_get_contents($positionsInput);

$data = prepareData($source);

// A
dump(findPaths($data));

// B
//dump(countEnergyLevel0($data));
