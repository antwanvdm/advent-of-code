<?php
$data = preg_split("/\r\n|\n|\r/", file_get_contents('6-data.txt'));
$races = array_map(null, ...array_map(fn($item) => array_values(array_filter(explode(' ', $item), fn($number) => is_numeric($number))), $data));

$winScenarios = [];
foreach ($races as $index => $race) {
    $winScenarios[$index] = 0;

    //Loop through possible times of the race, and calculate based on remaining time
    for ($i = 0; $i <= $race[0]; $i++) {
        $distance = $i * ($race[0] - $i);

        //Add the scenario if it's a possible record breaker
        if ($distance > $race[1]) {
            $winScenarios[$index]++;
        }
    }
}

//Beautiful function that multiplies all numbers in the array :-D
echo array_product($winScenarios);
