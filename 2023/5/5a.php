<?php
/** @var array $seedsData */
require_once '5-data.php';

//Get the initial row for starting numbers and array to store all our final locations numbers
$locations = [];
$seeds = explode(' ', array_shift($seedsData));

foreach ($seeds as $seed) {
    //Store current location which will be overridden for every next step in the transport
    $location = (int)$seed;
    foreach ($seedsData as $source => $rangeNumbers) {
        foreach ($rangeNumbers as $numberSet) {
            //Get the 3 essential numbers in line
            list ($destinationRange, $sourceRange, $rangeLength) = explode(' ', $numberSet);

            //If current location number is in range, let's override the location for next step
            if ($location >= (int)$sourceRange && $location <= (int)$sourceRange + (int)$rangeLength) {
                //Find corresponding number in destination, and continue to next loop
                $location = ($location - (int)$sourceRange) + (int)$destinationRange;
                continue 2;
            }
        }
    }
    $locations[] = $location;
}

//Get the lowest value
echo min($locations);
