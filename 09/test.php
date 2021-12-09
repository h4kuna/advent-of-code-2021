<?php declare(strict_types=1);

require __DIR__ . '/functions.php';

$source = '2199943210
3987894921
9856789892
8767896789
9899965678' . PHP_EOL;

$data = prepareData($source);

[$sum, $lowest] = findLowPoints($data);
// A
dump($sum);




render($data);

// B
dump(basinsCount($data, $lowest));

