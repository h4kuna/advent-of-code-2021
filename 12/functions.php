<?php declare(strict_types=1);

use h4kuna\Iterators;

require __DIR__ . '/../vendor/autoload.php';

class Node
{
	public const EDGE_START = 'start';

	public string $name;

	public ?int $counter;

	/** @var array<Node> */
	public array $edges = [];


	public function __construct(string $name)
	{
		$this->name = $name;
		$this->counter = $name === strtoupper($name) ? null : 0;
	}


	public function add(Node $node): void
	{
		$this->edges[$node->name] = $node;
		$node->edges[$this->name] = $this;
	}


	public function isSmall(): bool
	{
		return $this->counter !== null;
	}


	public function isEnd(): bool
	{
		return $this->name === 'end';
	}


	public function isStart(): bool
	{
		return $this->name === self::EDGE_START;
	}

}

class Counter
{
	public static $count = 0;

}

/**
 * @return array<Node>
 */
function prepareData(string $data): array
{
	$iterator = new Iterators\TextIterator($data);
	$iterator->setFlags($iterator::TRIM_LINE);
	$nodes = [];
	foreach ($iterator as $line) {
		[$from, $to] = explode('-', $line);
		if (!isset($nodes[$from])) {
			$nodes[$from] = new Node($from);
		}
		if (!isset($nodes[$to])) {
			$nodes[$to] = new Node($to);
		}

		$nodes[$from]->add($nodes[$to]);
	}

	return $nodes;
}


function renderPath(array $path): void
{
	echo implode(',', array_keys($path)) . PHP_EOL;
}


function onTheWay(Node $here, array $start): ?array
{
	$out = [];
	foreach ($here->edges as $edge) {
		$paths = $start;
		if ($edge->isSmall() && isset($paths[$edge->name])) {
			continue;
		}
		$paths[$edge->name] = $edge;
		if ($edge->isEnd()) {
			// ++Counter::$count;
			// renderPath($path);
			$out[] = $paths;
			continue;
		}
		$out = array_merge($out, onTheWay($edge, $paths));
	}

	return $out;
}

class Paths
{
	public array $paths = [];

	public string $useDouble = '';


	public function __construct(array $paths)
	{
		$this->paths = $paths;
	}


	public function canUse(Node $node): bool
	{
		if ($node->isStart()) {
			return false;
		} elseif (!$node->isSmall() || !isset($this->paths[$edge->name]) || (isset($this->paths[$edge->name]) && $this->useDouble === '')) {
			return true;
		}

		return false;
	}


	public function add(Node $node): void
	{
		if ($node->isSmall() && isset($this->paths[$node->name])) {
			$key = "_{$node->name}";
			$this->useDouble = $node->name;
		} else {
			$key = $node->name;
		}

		$this->paths[$key] = $node;
	}

}

/**
 * @param array<Node> $data
 */
function findPaths(array $data): int
{
	$start = [$data[Node::EDGE_START]->name => $data[Node::EDGE_START]];
	$paths = onTheWay($data[Node::EDGE_START], $start);

	return count($paths);
}


function onTheWaySmall(Node $here, array $start): array
{
	$out = [];
	foreach ($here->edges as $edge) {
		$paths = $start;
		if ($edge->isSmall() && isset($paths[$edge->name]) && isset($paths['_']) || $edge->isStart()) {
			continue;
		}
		$key = $edge->isSmall() && isset($paths[$edge->name]) ? '_' : $edge->name;
		$paths[$key] = $edge;
		if ($edge->isEnd()) {
			$out[] = $paths;
			continue;
		}
		$out = array_merge($out, onTheWaySmall($edge, $paths));
	}

	return $out;
}


/**
 * @param array<Node> $data
 */
function findPathsSmall(array $data): int
{
	$start = [$data[Node::EDGE_START]->name => $data[Node::EDGE_START]];
	$paths = onTheWaySmall($data[Node::EDGE_START], $start);

	return count($paths);
}
