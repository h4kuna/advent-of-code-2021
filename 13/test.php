<?php declare(strict_types=1);

use Tester\Assert;

require __DIR__ . '/functions.php';
{
	$source = '6,10
0,14
9,10
0,3
10,4
4,11
6,0
6,12
4,1
0,13
10,12
3,4
3,0
8,4
1,10
2,14
8,10
9,0

fold along y=7
fold along x=5' . PHP_EOL;
	[$paper, $fold] = prepareData($source);

	Assert::same(17, createManual($paper, $fold));
}

