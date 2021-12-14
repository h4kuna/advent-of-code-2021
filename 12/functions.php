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


function onTheWay(Node $here, array $previous): void
{
	foreach ($here->edges as $edge) {
		$path = $previous;
		if ($edge->isSmall() && isset($path[$edge->name])) {
			continue;
		}
		$path[$edge->name] = $edge;
		if ($edge->isEnd()) {
			++Counter::$count;
			// renderPath($path);

			continue;
		}
		onTheWay($edge, $path);
	}
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
	Counter::$count = 0;
	foreach ($data[Node::EDGE_START]->edges as $node) {
		onTheWay($node, [$data[Node::EDGE_START]->name => $data[Node::EDGE_START], $node->name => $node]);
	}

	return Counter::$count;
}


function onTheWaySmall(Node $here, Paths $previous): void
{
	foreach ($here->edges as $edge) {
		$path = clone $previous;
		if (!$path->canUse($edge)) {
			continue;
		}
		$path->add($edge);
		if ($edge->isEnd()) {
			++Counter::$count;
			renderPath($path->paths);

			continue;
		}
		onTheWaySmall($edge, $path);
	}
}


/**
 * @param array<Node> $data
 */
function findPathsSmall(array $data): int
{
	Counter::$count = 0;
	foreach ($data[Node::EDGE_START]->edges as $node) {
		onTheWaySmall($node, new Paths([
			$data[Node::EDGE_START]->name => $data[Node::EDGE_START],
			$node->name => $node,
		]));
	}

	return Counter::$count;
}
