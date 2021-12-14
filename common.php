<?php declare(strict_types=1);

\Tracy\Debugger::$maxDepth = 10;

/**
 * @return array<int>
 */
function toInt(string $input, string $delimiter = ','): array
{
	$numbers = [];

	if ($delimiter === '') {
		$data = str_split($input);
	} else {
		$data = explode($delimiter, $input);
	}

	foreach ($data as $k => $number) {
		$numbers[$k] = (int) $number;
	}

	return $numbers;
}
