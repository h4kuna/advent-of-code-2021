<?php declare(strict_types=1);

use Tester\Assert;

require __DIR__ . '/functions.php';
{
	$source = 'start-A
start-b
A-c
A-b
b-d
A-end
b-end' . PHP_EOL;
	$data = prepareData($source);

	Assert::same(10, findPaths($data));
}


{
	$source = 'dc-end
HN-start
start-kj
dc-start
dc-HN
LN-dc
HN-end
kj-sa
kj-HN
kj-dc' . PHP_EOL;

	$data = prepareData($source);

	Assert::same(19, findPaths($data));
}

{
	$source = 'fs-end
he-DX
fs-he
start-DX
pj-DX
end-zg
zg-sl
zg-pj
pj-he
RW-he
fs-DX
pj-RW
zg-RW
start-pj
he-WI
zg-he
pj-fs
start-RW' . PHP_EOL;

	$data = prepareData($source);

	Assert::same(226, findPaths($data));
}
// B
{
	$source = 'start-A
start-b
A-c
A-b
b-d
A-end
b-end' . PHP_EOL;
	$data = prepareData($source);

	Assert::same(36, findPathsSmall($data));
}

{
	$source = 'dc-end
HN-start
start-kj
dc-start
dc-HN
LN-dc
HN-end
kj-sa
kj-HN
kj-dc' . PHP_EOL;

	$data = prepareData($source);

	Assert::same(103, findPaths($data));
}

{
	$source = 'fs-end
he-DX
fs-he
start-DX
pj-DX
end-zg
zg-sl
zg-pj
pj-he
RW-he
fs-DX
pj-RW
zg-RW
start-pj
he-WI
zg-he
pj-fs
start-RW' . PHP_EOL;

	$data = prepareData($source);

	Assert::same(3509, findPaths($data));
}
