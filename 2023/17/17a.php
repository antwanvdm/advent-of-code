<?php
$lavaPool = preg_split("/\r\n|\n|\r/", file_get_contents('17-data.txt'));
$lavaLines = array_map(fn($line) => str_split($line), $lavaPool);

/**
 * @param $path
 * @param $lastMoves
 * @param $visited
 * @return bool
 */
function in_visited_array($path, $lastMoves, &$visited): bool
{
    //Combine row, column, direction & last 3 moves to have a unique check
    $hash = $path['row'] . '-' . $path['column'] . '-' . $path['direction'] . '-' . json_encode($lastMoves);
    if (isset($visited[$hash])) {
        return true;
    }

    $visited[$hash] = 1;
    return false;
}

/**
 * @param $path
 * @param $paths
 * @param $visited
 * @param $lavaLines
 * @return void
 */
function move($path, &$paths, &$visited, $lavaLines): void
{
    $lastMoves = array_slice($path['lastMoves'], -3, 3);
    $lastThreeMovesAreSame = count($lastMoves) === 3 && count(array_unique($lastMoves)) === 1;

    $options = [
        'right' => ['up', 'down', 'right'],
        'left' => ['up', 'down', 'left'],
        'up' => ['right', 'left', 'up'],
        'down' => ['right', 'left', 'down'],
    ];
    $directions = [
        'up' => [-1, 0],
        'down' => [1, 0],
        'left' => [0, -1],
        'right' => [0, 1],
    ];

    //Get all next moves based on the current direction & remove the last option when limit has been reached
    $pathOptions = $options[$path['direction']];
    if ($lastThreeMovesAreSame) {
        array_pop($pathOptions);
    }

    foreach ($pathOptions as $pathOption) {
        $nextRow = $path['row'] + $directions[$pathOption][0];
        $nextColumn = $path['column'] + $directions[$pathOption][1];

        //Check if next would be within boundaries
        if ($nextRow >= 0 && $nextRow < count($lavaLines) && $nextColumn >= 0 && $nextColumn < count($lavaLines[0])) {
            $nextPath = [
                'direction' => $pathOption,
                'row' => $nextRow,
                'column' => $nextColumn,
                'heatLoss' => $path['heatLoss'] + $lavaLines[$nextRow][$nextColumn],
                'lastMoves' => array_merge($path['lastMoves'], [$pathOption])
            ];
            //Add path if we didn't visit it before
            if (!in_visited_array($nextPath, $lastMoves, $visited)) {
                $paths->insert($nextPath, -$nextPath['heatLoss']); //Sort with negative number else we get the longest path
            }
        }
    }
}

//Use priority queue to make sure sorting will go faster (eg: automatically)
$paths = new \SplPriorityQueue();
$paths->insert(['row' => 0, 'column' => 0, 'direction' => 'right', 'heatLoss' => 0, 'lastMoves' => []], 0);
$paths->insert(['row' => 0, 'column' => 0, 'direction' => 'down', 'heatLoss' => 0, 'lastMoves' => []], 0);

$visited = [];
while (!$paths->isEmpty()) {
    //Get the path with lowest heatLoss from the queue
    $path = $paths->extract();

    //Break out once the first arrived (which should be the cheapest)
    if ($path['row'] === count($lavaLines) - 1 && $path['column'] === count($lavaLines[0]) - 1) {
        echo $path['heatLoss'];
        break;
    }

    //Make the moves (new paths)
    move($path, $paths, $visited, $lavaLines);
}
