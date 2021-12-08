<?php declare(strict_types=1);

use h4kuna\Iterators;

require __DIR__ . '/../vendor/autoload.php';

/** @return array<string> */
function divideBySpace(string $input): array
{
	return explode(' ', $input);
}

class SignalDecode
{
	private const N_O = 0; // 0b1110111;
	private const N_1 = 1; // 0b0100100;
	private const N_2 = 2; // 0b1011101;
	private const N_3 = 3; // 0b1101101;
	private const N_4 = 4; // 0b0101110;
	private const N_5 = 5; // 0b1101011;
	private const N_6 = 6; // 0b1111011;
	private const N_7 = 7; // 0b0100101;
	private const N_8 = 8; // 0b1111111;
	private const N_9 = 9; // 0b1101111;

//	private const DIGIT_TO_DEC = [
//		self::N_O => 0,
//		self::N_1 => 1,
//		self::N_2 => 2,
//		self::N_3 => 3,
//		self::N_4 => 4,
//		self::N_5 => 5,
//		self::N_6 => 6,
//		self::N_7 => 7,
//		self::N_8 => 8,
//		self::N_9 => 9,
//	];
//
//	private const DEC_TO_DIGIT = [
//		0 => self::N_O,
//		1 => self::N_1,
//		2 => self::N_2,
//		3 => self::N_3,
//		4 => self::N_4,
//		5 => self::N_5,
//		6 => self::N_6,
//		7 => self::N_7,
//		8 => self::N_8,
//		9 => self::N_9,
//	];

	private const UNIQUE = [
		2 => self::N_1,
		4 => self::N_4,
		3 => self::N_7,
		7 => self::N_8,
	];


	public static function decode(array $signals)
	{
		$map = [];
		$groups = [6 => [], 5 => []];
		foreach ($signals as $k => $signal) {
			$number = self::number($signal);
			if ($number !== null) {
				$map[$number] = self::sort($signal);
				unset($signals[$k]);
				continue;
			}
			$groups[strlen($signal)][] = self::sort($signal);
		}

		$map[self::N_3] = self::findNumber($groups[5], $map[self::N_7], 2);
		$map[self::N_2] = self::findNumber($groups[5], $map[self::N_4], 3);
		$map[self::N_5] = reset($groups[5]);
		unset($groups[5]);

		$map[self::N_6] = self::findNumber($groups[6], $map[self::N_1], 5);
		$map[self::N_9] = self::findNumber($groups[6], $map[self::N_4], 2);
		$map[self::N_O] = reset($groups[6]);
		unset($groups[6]);

		$out = [];
		foreach ($map as $k => $data) {
			$out[self::join($data)] = $k;
		}

		return $out;
	}


	public static function matchUnique(string $signal): bool
	{
		return self::number($signal) !== null;
	}


	public static function toInt(array $numbers, array $map): int
	{
		$num = '';
		foreach ($numbers as $number) {
			$num .= $map[self::join(self::sort($number))];
		}

		return (int) $num;
	}


	private static function join(array $data): string
	{
		return implode('', $data);
	}


	private static function number(string $signal): ?int
	{
		$length = strlen($signal);

		return self::UNIQUE[$length] ?? null;
	}


	private static function findNumber(array &$signals, array $signal7, int $result): array
	{
		foreach ($signals as $key => $signal) {
			if (count(array_diff($signal, $signal7)) === $result) {
				unset($signals[$key]);

				return $signal;
			}
		}

		throw new \Exception('not found');
	}


	private static function sort(string $signal): array
	{
		$data = str_split($signal);
		sort($data);

		return $data;
	}

}

function decodeNumbers(array $data): int
{
	$sum = 0;
	foreach ($data as $signalData) {
		$map = SignalDecode::decode($signalData[0]);
		$sum += SignalDecode::toInt($signalData[1], $map);
	}

	return $sum;
}


function decodeUnique(array $input): int
{
	$sum = 0;
	foreach ($input as $data) {
		foreach ($data[1] as $signal) {
			if (SignalDecode::matchUnique($signal)) {
				++$sum;
			}
		}
	}

	return $sum;
}


/**
 * @return array
 */
function prepareData(string $data): array
{
	$input = new Iterators\TextIterator($data);
	$input->setFlags($input::TRIM_LINE);
	$output = [];
	foreach ($input as $line) {
		[$signalInput, $numbersInput] = explode(' | ', $line);
		$signals = divideBySpace($signalInput);
		$numbers = divideBySpace($numbersInput);

		$output[] = [$signals, $numbers];
	}

	return $output;
}










