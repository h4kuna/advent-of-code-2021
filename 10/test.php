<?php declare(strict_types=1);

require __DIR__ . '/functions.php';

$source = '[({(<(())[]>[[{[]{<()<>>
[(()[<>])]({[<{<<[]>>(
{([(<{}[<>[]}>{[]{[(<()>
(((({<>}<{<{<>}{[]{[]{}
[[<[([]))<([[{}[[()]]]
[{[{({}]{}}([{[{{{}}([]
{<[[]]>}<{[{[{[]{()[[[]
[<(<(<(<{}))><([]([]()
<{([([[(<>()){}]>(<<{{
<{([{{}}[<[[[<>{}]]]>[]]' . PHP_EOL;

$data = prepareData($source);

['corrupted' => $corrupted, 'autocomplete' => $autocomplete] = analyzeCorruptedLines($data);

// A
dump($corrupted);

// B
dump($autocomplete);
