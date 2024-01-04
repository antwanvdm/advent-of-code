<?php
$data = preg_split("/\r\n|\n|\r/", file_get_contents('6-data.txt'));
$totalRace = array_map(fn($item) => implode('', array_filter(explode(' ', $item), fn($number) => is_numeric($number))), $data);

//Loop through possible times of the race, and calculate based on remaining time
$winScenarios = 0;
for ($i = 0; $i <= $totalRace[0]; $i++) {
    $distance = $i * ($totalRace[0] - $i);

    //Add the scenario if it's a possible record breaker
    if ($distance > $totalRace[1]) {
        $winScenarios++;
    }
}

//And we're done!
echo $winScenarios;
