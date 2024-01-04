<?php
//Probably most complex data parsing of the year
$data = preg_split("/\r\n\r\n|\n\n|\r\r/", file_get_contents('5-data.txt'));
$seeds = explode(' ', str_replace('seeds: ', '', $data[0]));
array_shift($data);
$seedsData = array_column(array_map(function ($line) {
    $lines = preg_split("/\r\n|\n|\r/", $line);
    $source = str_replace(' map:', '', array_shift($lines));
    return [$source, $lines];
}, $data), 1, 0);

//Array to store all our final locations numbers
$locations = [];

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
