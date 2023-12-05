<?php
/** @var array $games */
require_once '2-data.php';

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
