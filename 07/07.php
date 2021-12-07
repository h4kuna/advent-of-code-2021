<?php declare(strict_types=1);

require __DIR__ . '/functions.php';

$crabsPositions = __DIR__ . '/07input.txt';

$crabs = prepareData(file_get_contents($crabsPositions));


// A
dump(min(fuelCountContant($crabs)));

// B
dump(min(fuelCount($crabs)));
