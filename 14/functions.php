<?php declare(strict_types=1);

use h4kuna\Iterators;

require __DIR__ . '/../vendor/autoload.php';

function prepareData(string $data): array
{
	[$template, $rules] = explode(PHP_EOL . PHP_EOL, $data);

	$stack = [];
	$prev = null;
	foreach (str_split($template) as $char) {
		if ($prev !== null) {
			addCounter($stack, $prev . $char);
		}

		$prev = $char;
	}

	$iterator = new Iterators\TextIterator($rules);
	$iterator->setFlags($iterator::TRIM_LINE);

	$rules = [];
	foreach ($iterator as $line) {
		[$key, $v] = explode(' -> ', $line);
		$rules[$key] = $v;
	}

	return [$stack, $rules];
}


function buildByStep(array $stack, array $rules, int $steps): int
{
	if ($stack === []) {
		return 0;
	}

	for ($i = 0; $i < $steps; ++$i) {
		$newStack = [];
		foreach ($stack as $group => $count) {
			$newGroup = $group[0] . $rules[$group];
			addCounter($newStack, $newGroup, $count);
			$newGroup = $rules[$group] . $group[1];
			addCounter($newStack, $newGroup, $count);
		}

		$count = $newStack[$newGroup];
		unset($newStack[$newGroup]);
		$newStack[$newGroup] = $count;
		$stack = $newStack;
	}

	$counter = [];
	foreach ($stack as $group => $count) {
		addCounter($counter, $group[0], $count);
	}

	addCounter($counter, array_key_last($stack)[1]);

	return max($counter) - min($counter);
}




