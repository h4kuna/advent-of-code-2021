<?php declare(strict_types=1);

require __DIR__ . '/functions.php';

$positionsInput = __DIR__ . '/09input.txt';

$source = file_get_contents($positionsInput);


$data = prepareData($source);

[$sum, $lowest] = findLowPoints($data);
// A
dump($sum);

//render($data);
// B
dump(basinsCount($data, $lowest));
