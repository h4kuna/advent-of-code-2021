<?php declare(strict_types=1);

use h4kuna\Iterators;

require __DIR__ . '/../vendor/autoload.php';

$binaryInput = __DIR__ . '/03input.txt';

function diagnostic(Iterators\TextIterator $data): int
{
	$sum = [];
	foreach ($data as $line) {
		$sum = array_fill(0, strlen($line), [0 => 0, 1 => 0]);
		break;
	}

	foreach ($data as $line) {
		foreach (array_reverse(str_split($line)) as $pos => $value) {
			++$sum[$pos][$value];
		}
	}

	$epsilon = $gamma = 0;
	foreach ($sum as $pos => $values) {
		if ($values[0] === $values[1]) {
			throw new \Exception('What do do?');
		} elseif ($values[0] > $values[1]) {
			$epsilon |= 2 ** $pos;
		} else {
			$gamma |= 2 ** $pos;
		}
	}

	return $gamma * $epsilon;
}


function divideZeroOne(iterable $data, int $pos = 0): array
{
	$zero = $one = [];
	foreach ($data as $line) {
		if (substr($line, $pos, 1) === '0') {
			$zero[] = $line;
		} else {
			$one[] = $line;
		}
	}

	$zeroCount = count($zero);
	$oneCount = count($one);

	if ($zeroCount === $oneCount) {
		return ['zero' => $zero, 'one' => $one];
	} elseif ($zeroCount > $oneCount) {
		return ['most' => $zero, 'least' => $one];
	}

	return ['most' => $one, 'least' => $zero];
}


/**
 * @param array<string> $data
 */
function isOneValue(array $data): bool
{
	return count($data) === 1;
}


/**
 * @param array<string> $data
 */
function lastValueToInt(array $data): int
{
	$value = reset($data);

	return bindec($value);
}


/**
 * @param array<string> $data
 */
function foundRate(array $data, string $type): int
{
	if ($type === 'oxygen') {
		$firstType = 'most';
		$secondType = 'one';
	} elseif ($type === 'co2') {
		$firstType = 'least';
		$secondType = 'zero';
	} else {
		throw new \Exception('Unsupported type.');
	}
	$length = strlen(current($data));
	for ($i = 1; $i < $length; ++$i) {
		$preFetch = divideZeroOne($data, $i);
		$data = $preFetch[$firstType] ?? $preFetch[$secondType];
		if (isOneValue($data)) {
			return lastValueToInt($data);
		}
	}
	if (isOneValue($data)) {
		return lastValueToInt($data);
	}
	throw new \Exception('One value not found for count rate.');
}


function lifeSupport(Iterators\TextIterator $data): int
{
	['most' => $oxygenData, 'least' => $co2Data] = divideZeroOne($data);
	$oxygen = foundRate($oxygenData, 'oxygen');
	$co2 = foundRate($co2Data, 'co2');

	return $oxygen * $co2;
}


// run
$data = new Iterators\TextIterator(file_get_contents($binaryInput));
$data->setFlags($data::SKIP_EMPTY_LINE | $data::TRIM_LINE);

// A
dump(diagnostic($data));
// 1131506

// B
dump(lifeSupport($data));

