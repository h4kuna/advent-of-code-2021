<?php declare(strict_types=1);

require __DIR__ . '/functions.php';

$positionsInput = __DIR__ . '/10input.txt';

$source = file_get_contents($positionsInput);
$data = prepareData($source);

['corrupted' => $corrupted, 'autocomplete' => $autocomplete] = analyzeCorruptedLines($data);

// A
dump($corrupted);

// B
dump($autocomplete);
