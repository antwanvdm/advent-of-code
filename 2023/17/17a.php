<?php
$lavaPool = preg_split("/\r\n|\n|\r/", file_get_contents('17-data-sample.txt'));
$lavaLines = array_map(fn($line) => str_split($line), $lavaPool);

/**
 * @param $path
 * @param $visited
 * @return bool
 */
function in_visited_array($path, &$visited): bool
{
    foreach ($visited as $item) {
        $checkPath = [$path['row'], $path['column']];
        if ($item === $checkPath) {
            return true;
        }
    }

    $visited[] = [$path['row'], $path['column']];
    return false;
}

function move($path, &$paths, &$visited, $lavaLines): void
{
    $lastMoves = array_slice($path['lastMoves'], -3, 3);
    $lastThreeMovesAreSame = count($lastMoves) === 3 && count(array_unique($lastMoves)) === 1;

    $up = $lavaLines[$path['row'] - 1][$path['column']] ?? false;
    $down = $lavaLines[$path['row'] + 1][$path['column']] ?? false;
    $left = $lavaLines[$path['row']][$path['column'] - 1] ?? false;
    $right = $lavaLines[$path['row']][$path['column'] + 1] ?? false;

    $options = [
        'right' => ['up', 'down', 'right'],
        'left' => ['up', 'down', 'left'],
        'up' => ['right', 'left', 'up'],
        'down' => ['right', 'left', 'down'],
    ];
    $keys = [
        'up' => [-1, 0],
        'down' => [1, 0],
        'left' => [0, -1],
        'right' => [0, 1],
    ];

    $pathOptions = $options[$path['direction']];
    if ($lastThreeMovesAreSame) {
        array_pop($pathOptions);
    }

    foreach ($pathOptions as $pathOption) {
        $nextPath = [
            'direction' => $pathOption,
            'row' => $path['row'] + $keys[$pathOption][0],
            'column' => $path['column'] + $keys[$pathOption][1],
            'heatLoss' => $path['heatLoss'] + $$pathOption ?? 0,
            'lastMoves' => array_merge($path['lastMoves'], [$pathOption])
        ];
        if ($$pathOption !== false && !in_visited_array($nextPath, $visited)) {
            $paths[] = $nextPath;
            usort($paths, fn($a, $b) => $a['heatLoss'] < $b['heatLoss'] ? -1 : 1);
        }
    }
}

$paths = [
    ['row' => 0, 'column' => 1, 'direction' => 'right', 'heatLoss' => 0, 'lastMoves' => ['right']],
    ['row' => 1, 'column' => 0, 'direction' => 'down', 'heatLoss' => 0, 'lastMoves' => ['down']],
];
$leastHeatLoss = 0;
$visited = [];
while (count($paths) > 0) {
    $path = array_shift($paths);
    //Break out once the first arrived (which should be the cheapest)
    if ($path['row'] === count($lavaLines) - 1 && $path['column'] === count($lavaLines[0]) - 1) {
        $leastHeatLoss = $path['heatLoss'];
        print_r($path['lastMoves']);
        break;
    }

    //Make the moves (new paths)
    move($path, $paths, $visited, $lavaLines);
    $test = '';
}

echo $leastHeatLoss;


//TODO: kijken naar wat de beste optie is (optellen waardes per keer en bepalen wat de volgende logische is)


//1440 TOO HIGH
//1261 TOO HIGH