<?php declare(strict_types=1);

/**
 * @return array<int>
 */
function toInt(string $input): array
{
	$numbers = [];
	foreach (explode(',', $input) as $k => $number) {
		$numbers[$k] = (int) $number;
	}

	return $numbers;
}
