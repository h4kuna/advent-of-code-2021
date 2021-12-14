<?php declare(strict_types=1);

require __DIR__ . '/functions.php';

$positionsInput = __DIR__ . '/input.txt';

$source = file_get_contents($positionsInput);

[$polymer, $rules] = prepareData($source);

// A
dump(buildByStep($polymer, $rules, 10));

// B
dump(buildByStep($polymer, $rules, 40));

