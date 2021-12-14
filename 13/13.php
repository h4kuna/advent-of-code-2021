<?php declare(strict_types=1);

require __DIR__ . '/functions.php';

$positionsInput = __DIR__ . '/13input.txt';

$source = file_get_contents($positionsInput);

$data = prepareData($source);

// A
[$paper, $fold] = prepareData($source);

//dump(createManual($paper, $fold));

// B
renderManual($paper, $fold);
