<?php
/** @var array $races */
require_once '6-data.php';

$winScenarios = [];
foreach ($races as $index => $race) {
    $winScenarios[$index] = 0;

    //Loop through possible times of the race, and calculate based on remaining time
    for ($i = 0; $i <= $race['time']; $i++) {
        $distance = $i * ($race['time'] - $i);

        //Add the scenario if it's a possible record breaker
        if ($distance > $race['distance']) {
            $winScenarios[$index]++;
        }
    }
}

//Beautiful function that multiplies all numbers in the array :-D
echo array_product($winScenarios);
