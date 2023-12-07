<?php
/** @var array $totalRace */
require_once '6-data.php';

//Loop through possible times of the race, and calculate based on remaining time
$winScenarios = 0;
for ($i = 0; $i <= $totalRace['time']; $i++) {
    $distance = $i * ($totalRace['time'] - $i);

    //Add the scenario if it's a possible record breaker
    if ($distance > $totalRace['distance']) {
        $winScenarios++;
    }
}

//And we're done!
echo $winScenarios;
