<?php
$data = preg_split("/\r\n|\n|\r/", file_get_contents('21-data.txt'));
$maze = array_map(fn($line) => str_split($line), $data);

/**
 * @param $array
 * @return array|null
 */
function startingSpot($array): ?array
{
    foreach ($array as $rowIndex => $row) {
        foreach ($row as $colIndex => $value) {
            if ($value === 'S') {
                return [$rowIndex, $colIndex];
            }
        }
    }

    //If 'S' is not found
    return null;
}

$startingSpot = implode(',', startingSpot($maze));
$steps = 64;
$endPositions = [$startingSpot];
$test = '';

//Keep looping while steps are counted
while ($steps > 0) {
    //Unset current step and add relevant new options for every possible direction
    foreach ($endPositions as $index => $endPosition) {
        unset($endPositions[$index]);
        list($row, $column) = explode(',', $endPosition);

        $top = $maze[$row - 1][$column] ?? false;
        $topPosition = ($row - 1) . ',' . $column;
        if ($top !== '#' && !in_array($topPosition, $endPositions)) {
            $endPositions[] = $topPosition;
        }
        $left = $maze[$row][$column - 1] ?? false;
        $leftPosition = $row . ',' . ($column - 1);
        if ($left !== '#' && !in_array($leftPosition, $endPositions)) {
            $endPositions[] = $leftPosition;
        }
        $down = $maze[$row + 1][$column] ?? false;
        $downPosition = ($row + 1) . ',' . $column;
        if ($down !== '#' && !in_array($downPosition, $endPositions)) {
            $endPositions[] = $downPosition;
        }
        $right = $maze[$row][$column + 1] ?? false;
        $rightPosition = $row . ',' . ($column + 1);
        if ($right !== '#' && !in_array($rightPosition, $endPositions)) {
            $endPositions[] = $rightPosition;
        }
    }

    $steps--;
}

echo count($endPositions);
