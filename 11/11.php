<?php declare(strict_types=1);

require __DIR__ . '/functions.php';

$positionsInput = __DIR__ . '/11input.txt';

$source = file_get_contents($positionsInput);

$data = prepareData($source);

// A
dump(countEnergyLevel100($data));

// B
dump(countEnergyLevel0($data));
