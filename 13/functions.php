<?php declare(strict_types=1);

use h4kuna\Iterators;
use Nette\Utils;

require __DIR__ . '/../vendor/autoload.php';

function prepareData(string $data): array
{
	[$coordinates, $fold] = explode(PHP_EOL . PHP_EOL, $data);

	$iterator = new Iterators\TextIterator($coordinates);
	$iterator->setFlags($iterator::TRIM_LINE);

	$maxX = $maxY = 0;
	$coordinates = [];
	foreach ($iterator as $line) {
		[$x, $y] = toInt($line);
		$coordinates[] = [$x, $y];
		$maxX = max($maxX, $x);
		$maxY = max($maxY, $y);
	}

	$paper = array_fill(0, $maxY + 1, array_fill(0, $maxX + 1, '.'));
	foreach ($coordinates as $coordinate) {
		$paper[$coordinate[1]][$coordinate[0]] = '#';
	}

	$folding = [];
	$iterator = new Iterators\TextIterator($fold);
	$iterator->setFlags($iterator::TRIM_LINE);
	foreach ($iterator as $item) {
		$match = Utils\Strings::match($item, '/fold along (?<coo>x|y)=(?<val>\d+)/');
		if ($match === null) {
			throw new \Exception('invalid input data');
		}
		$folding[] = ['coo' => $match['coo'], 'val' => (int) $match['val']];
	}

	return [$paper, $folding];
}


function mergePaper(array $a, array $b): array
{
	foreach ($a as $y => $row) {
		foreach ($row as $x => $v) {
			if (!isset($b[$y][$x])) {
				break;
			}
			if ($v === '#') {
				continue;
			} elseif ($b[$y][$x] === '#') {
				$a[$y][$x] = $b[$y][$x];
			}
		}
	}

	return $a;
}


function countDots(array $paper): int
{
	$counter = 0;
	foreach ($paper as $row) {
		foreach ($row as $value) {
			if ($value === '#') {
				++$counter;
			}
		}
	}

	return $counter;
}


function renderCode(array $paper): void
{
	foreach ($paper as $row) {
		foreach ($row as $value) {
			echo $value;
		}
		echo PHP_EOL;
	}
	echo PHP_EOL;
}


/**
 * @param array<array{val: int, coo: string}> $folding
 */
function createManual(array $paper, array $folding): int
{
	foreach ($folding as $item) {
		if ($item['coo'] === 'y') {
			$a = array_slice($paper, 0, $item['val']);
			$b = array_reverse(array_slice($paper, $item['val'] + 1));
			$paper = mergePaper($a, $b);
		} else {
			foreach ($paper as $y => $row) {
				for ($x = 0; $x < $item['val']; ++$x) {
					$x2 = ($item['val'] * 2) - $x;
					$v = $paper[$y][$x];
					if ($v === '#') {
						continue;
					} elseif ($paper[$y][$x2] === '#') {
						$paper[$y][$x] = $paper[$y][$x2];
					}
				}
				$paper[$y] = array_slice($paper[$y], 0, $item['val']);
			}
		}
		break;
	}

	return countDots($paper);
}


/**
 * @param array<array{val: int, coo: string}> $folding
 */
function renderManual(array $paper, array $folding): void
{
	foreach ($folding as $item) {
		if ($item['coo'] === 'y') {
			$a = array_slice($paper, 0, $item['val']);
			$b = array_slice($paper, $item['val'] + 1);
			if (count($a) !== count($b)) {
				$max = count($a) - count($b);
				for ($i = 0; $i < $max; ++$i) {
					$b[] = [];
				}
			}
			$paper = mergePaper($a, array_reverse($b));
		} else {
			foreach ($paper as $y => $row) {
				for ($x = 0; $x < $item['val']; ++$x) {
					$x2 = ($item['val'] * 2) - $x;
					$v = $paper[$y][$x];
					if ($v === '#') {
						continue;
					} elseif ($paper[$y][$x2] === '#') {
						$paper[$y][$x] = $paper[$y][$x2];
					}
				}
				$paper[$y] = array_slice($paper[$y], 0, $item['val']);
			}
		}
	}

	renderCode($paper);
}
