<?php declare(strict_types=1);

require __DIR__ . '/functions.php';

$crabsPositions = __DIR__ . '/08input.txt';

$source = prepareData(file_get_contents($crabsPositions));


// A
dump(decodeUnique($source));

// B
dump(decodeNumbers($source));
