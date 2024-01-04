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

function move($heatLoss, $path, &$paths, &$visited, $lavaLines, $row, $column): void
{
    $path['lastMoves'][] = $path['direction'];
    $lastMoves = array_slice($path['lastMoves'], -3, 3);
    $lastThreeMovesAreSame = count($lastMoves) === 3 && count(array_unique($lastMoves)) === 1;
    $path['heatLoss'] += $heatLoss;

    $up = $lavaLines[$row - 1][$column] ?? false;
    $down = $lavaLines[$row + 1][$column] ?? false;
    $left = $lavaLines[$row][$column - 1] ?? false;
    $right = $lavaLines[$row][$column + 1] ?? false;

    $options = [
        'right' => ['up', 'down', 'right'],
        'left' => ['up', 'down', 'left'],
        'up' => ['right', 'left', 'up'],
        'down' => ['right', 'left', 'down'],
    ];

    $pathOptions = $options[$path['direction']];
    if ($lastThreeMovesAreSame) {
        array_pop($pathOptions);
    }

    foreach ($pathOptions as $pathOption) {
        $upPath = array_merge($path, ['direction' => 'up', 'row' => $path['row'] - 1]);
        if ($pathOption === 'up' && $up !== false && !in_visited_array($upPath, $visited)) {
            $paths[] = $upPath;
        }
        $downPath = array_merge($path, ['direction' => 'down', 'row' => $path['row'] + 1]);
        if ($pathOption === 'down' && $down !== false && !in_visited_array($downPath, $visited)) {
            $paths[] = $downPath;
        }
        $leftPath = array_merge($path, ['direction' => 'left', 'column' => $path['column'] - 1]);
        if ($pathOption === 'left' && $left !== false && !in_visited_array($leftPath, $visited)) {
            $paths[] = $leftPath;
        }
        $rightPath = array_merge($path, ['direction' => 'right', 'column' => $path['column'] + 1]);
        if ($pathOption === 'right' && $right !== false && !in_visited_array($rightPath, $visited)) {
            $paths[] = $rightPath;
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
    foreach ($paths as $index => $path) {
        //Break out once the first arrived (which should be the cheapest
        if ($path['row'] === count($lavaLines) - 1 && $path['column'] === count($lavaLines[0]) - 1) {
            $leastHeatLoss = $path['heatLoss'];
            break 2;
        }

        //Make the moves (new paths)
        move((int)$lavaLines[$path['row']][$path['column']], $path, $paths, $visited, $lavaLines, $path['row'], $path['column']);

        //Always unset current path as we are creating new paths
        unset($paths[$index]);
    }
    usort($paths, fn($a, $b) => $a['heatLoss'] < $b['heatLoss'] ? -1 : 1);

    //Sort on cheapest option
    $test = '';
}

//print_r($paths);
echo $leastHeatLoss;


//TODO: kijken naar wat de beste optie is (optellen waardes per keer en bepalen wat de volgende logische is)


//1440 TOO HIGH