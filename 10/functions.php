<?php declare(strict_types=1);

use h4kuna\Iterators;

require __DIR__ . '/../vendor/autoload.php';

/**
 * @return array<array<Number>>
 */
function prepareData(string $data): Iterators\TextIterator
{
	$iterator = new Iterators\TextIterator($data);
	$iterator->setFlags($iterator::TRIM_LINE);

	return $iterator;
}

class OpenChar
{
	private const OPEN = [
		'(' => ')',
		'[' => ']',
		'{' => '}',
		'<' => '>',
	];

	public string $char;

	public ?OpenChar $prev;


	public function __construct(string $char, ?OpenChar $prev)
	{
		$this->char = $char;
		$this->prev = $prev;
	}


	public static function isOpen(string $char): bool
	{
		return isset(self::OPEN[$char]);
	}


	public function isClose(string $char): bool
	{
		return self::OPEN[$this->char] === $char;
	}


	public function closeChar(): string
	{
		return self::OPEN[$this->char];
	}

}

class CorruptedLineException extends \Exception
{

}

function countScore(string $char): int
{
	switch ($char) {
		case ')':
			return 3;
		case ']':
			return 57;
		case '}':
			return 1197;
		case '>':
			return 25137;
		default:
			throw new \Exception('Unsupported char');
	}
}


function analyzeCorruptedLines(Iterators\TextIterator $iterator): array
{
	$score = [];
	$autocompleteScore = [];
	foreach ($iterator as $line) {
		try {
			$openChar = null;
			$broken = false;
			foreach (str_split($line) as $char) {
				if (OpenChar::isOpen($char)) {
					$openChar = new OpenChar($char, $openChar);
				} elseif ($openChar === null) {
					throw new \Exception('Missing object OpenTag');
				} elseif (!$openChar->isClose($char)) {
					$score[$line] = countScore($char);
					$broken = true;
					$openChar = $openChar->prev ?? null;
				} else {
					$openChar = $openChar->prev ?? null;
				}
			}

			if ($broken === false && $openChar !== null) {
				$autocompleteScore[] = countAutocompleteScore($openChar);
			}
		} catch (CorruptedLineException $e) {
			// intentionally empty
		}
	}

	sort($autocompleteScore);
	$match = (int) floor(count($autocompleteScore) / 2);

	return ['corrupted' => array_sum($score), 'autocomplete' => $autocompleteScore[$match]];
}


function valueOfCloseChar(string $char): int
{
	switch ($char) {
		case ')':
			return 1;
		case ']':
			return 2;
		case '}':
			return 3;
		case '>':
			return 4;
		default:
			throw new \Exception('Unknown close char');
	}
}


function countAutocompleteScore(OpenChar $openChar): int
{
	$prev = $openChar;
	$sum = 0;
	do {
		$sum *= 5;
		$sum += valueOfCloseChar($prev->closeChar());
		$prev = $prev->prev;
	} while ($prev !== null);

	return $sum;
}





