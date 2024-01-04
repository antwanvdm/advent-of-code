<?php
//Probably most complex data parsing of the year
$data = preg_split("/\r\n\r\n|\n\n|\r\r/", file_get_contents('5-data.txt'));
$seedPairs = array_chunk(explode(' ', str_replace('seeds: ', '', $data[0])), 2);
array_shift($data);
$seedsData = array_column(array_map(function ($line) {
    $lines = preg_split("/\r\n|\n|\r/", $line);
    $source = str_replace(' map:', '', array_shift($lines));
    return [$source, $lines];
}, $data), 1, 0);

//Array to store all our final locations numbers
$locations = [];

//Create actual seed ranges based on the pairs
$seedRanges = [];
foreach ($seedPairs as $seedPair) {
    $seedRanges[] = [
        'min' => (int)$seedPair[0],
        'max' => (int)($seedPair[0] + $seedPair[1]) - 1,
    ];
}

//Loop through all the data, within the data the ranges, and within the ranges the numbers to check
foreach ($seedsData as $type => $rangeNumbers) {
    foreach ($seedRanges as $seedRangeIndex => $range) {
        foreach ($rangeNumbers as $numberSet) {
            //Gather all information with names as logic as possible
            list ($destinationRange, $sourceRange, $rangeLength) = explode(' ', $numberSet);
            $destinationRange = (int)$destinationRange;
            $sourceRange = (int)$sourceRange;
            $rangeLength = (int)$rangeLength;
            $sourceRangeEnd = $sourceRange + $rangeLength - 1;
            $mappingDifference = $destinationRange - $sourceRange;

            //First check the left side
            $keepCurrentRangeForCheck = false;
            if ($range['min'] < $sourceRange) {
                $seedRanges[$seedRangeIndex] = [
                    'min' => $range['min'],
                    'max' => min($range['max'], $sourceRange - 1)
                ];

                //I need this only for the test data, worked for real data without this.. weird.
                $keepCurrentRangeForCheck = true;
            }

            //Secondly the right side
            if ($sourceRangeEnd < $range['max']) {
                if ($keepCurrentRangeForCheck) {
                    $seedRanges[] = $range;
                }
                $seedRanges[$seedRangeIndex] = [
                    'min' => max($sourceRangeEnd + 1, $range['min']),
                    'max' => $range['max']
                ];
            }

            //Finally anything in between (took me ages to find the right if/else...)
            if ($sourceRange <= $range['max'] && $range['min'] <= $sourceRangeEnd) {
                if ($keepCurrentRangeForCheck) {
                    $seedRanges[] = $range;
                }
                $seedRanges[$seedRangeIndex] = [
                    'min' => max($range['min'], $sourceRange) + $mappingDifference,
                    'max' => min($range['max'], $sourceRangeEnd) + $mappingDifference
                ];
                break;
            }
        }
    }
}

//Just get the lowest possible option here
echo min(...array_map(fn($item) => $item['min'], $seedRanges));
