<?php
/** @var array $games */
require_once '2-data.php';

$gameLimitations = [
    'red' => 12,
    'green' => 13,
    'blue' => 14,
];

$totalSum = 0;
foreach ($games as $id => $game) {
    //Explode the fields to make sure we have all cubes in an array
    $cubes = explode(', ', str_replace(';', ',', $game));

    $gameIsPossible = true;
    foreach ($cubes as $cube) {
        list($amount, $color) = explode(' ', $cube);

        //Exclude from sum if amount surpasses limitations
        if (isset($gameLimitations[$color]) && $amount > $gameLimitations[$color]) {
            $gameIsPossible = false;
        }
    }

    //Add ID to total sum
    if ($gameIsPossible) {
        $totalSum += $id;
    }

}

echo $totalSum;
