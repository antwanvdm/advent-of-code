<?php
$data = preg_split("/\r\n|\n|\r/", file_get_contents('2-data.txt'));
$games = array_column(array_map(fn($line) => explode(': ', str_replace('Game ', '', $line)), $data), 1, 0);

$totalSum = 0;
foreach ($games as $id => $game) {
    //Explode the fields to make sure we have all cubes in an array
    $cubes = explode(', ', str_replace(';', ',', $game));

    $cubeByColor = [];
    foreach ($cubes as $cube) {
        list($amount, $color) = explode(' ', $cube);

        //Set the highest amount needed to know the minimum cubes needed for a color
        if (!isset($cubeByColor[$color]) || $amount > $cubeByColor[$color]) {
            $cubeByColor[$color] = $amount;
        }
    }

    //Multiply the color values & add total to sum
    $multipliedColors = $cubeByColor['red'] * $cubeByColor['green'] * $cubeByColor['blue'];
    $totalSum += $multipliedColors;
}

echo $totalSum;
