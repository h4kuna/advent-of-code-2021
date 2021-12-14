<?php declare(strict_types=1);

use Tester\Assert;

require __DIR__ . '/functions.php';
{
	$source = 'NNCB

CH -> B
HH -> N
CB -> H
NH -> C
HB -> C
HC -> B
HN -> C
NN -> C
BH -> H
NC -> B
NB -> B
BN -> B
BB -> N
BC -> B
CC -> N
CN -> C' . PHP_EOL;
	[$stack, $rules] = prepareData($source);

	Assert::same(1588, buildByStep($stack, $rules, 10));
}

// Assert::same(2188189693529, buildByStep($polymer, $rules, 40));

