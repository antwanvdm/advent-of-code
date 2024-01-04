<?php
list($map, $items) = array_map(fn($text) => preg_split("/\r\n|\n|\r/", $text), preg_split("/\r\n\r\n|\n\n|\r\r/", file_get_contents('19-data-sample.txt')));

//Map the conversions to indexed array we can easily use in our recursive logic
$conversions = [];
foreach ($map as $conversion) {
    $conversionData = explode('{', str_replace('}', '', $conversion));
    $test = '';
    $conversions[$conversionData[0]] = explode(',', $conversionData[1]);
}

/**
 * Recursive logic to match the result based on either value or condition
 *
 * @param array $data
 * @param array $conversions
 * @param string $input
 * @param int $sum
 * @return int
 */
function nextItem(array $data, array $conversions, string $input = 'in', int $sum = 0): int
{
    foreach ($conversions[$input] as $conversion) {
        if (!str_contains($conversion, ':')) {
//            $sum += calculateSum([1, 4000]);
            if ($conversion === 'A') {
                $sum += calculateSum([1, 4000]);
            } elseif ($conversion !== 'R') {
                $sum += nextItem($data, $conversions, $conversion, $sum);
            }

//            return match ($conversion) {
//                'A' => $sum,
//                'R' => 0,
//                default => nextItem($data, $conversions, $conversion, $sum)
//            };
        } elseif (str_contains($conversion, '<')) {
            list($condition, $result) = explode(':', $conversion);
            list($key, $value) = explode('<', $condition);

            if ($result === 'A') {
                $sum += calculateSum([1, (int)$value - 1]);
            } elseif ($result !== 'R') {
                $sum += nextItem($data, $conversions, $result, $sum);
            } else {
                $sum += calculateSum([(int)$value, 4000]);
            }
//            $sum += nextItem($data, $conversions, $result, $sum);
//            return $sum;
//            return match ($result) {
//                'A' => $sum,
//                'R' => 0,
//                default => nextItem($data, $conversions, $result, $sum)
//            };
        } elseif (str_contains($conversion, '>')) {
            list($condition, $result) = explode(':', $conversion);
            list($key, $value) = explode('>', $condition);

            if ($result === 'A') {
                $sum += calculateSum([(int)$value + 1, 4000]);
            } elseif ($result !== 'R') {
                $sum += nextItem($data, $conversions, $result, $sum);
            } else {
                $sum += calculateSum([1, (int)$value]);
            }
//            return match ($result) {
//                'A' => $sum,
//                'R' => 0,
//                default => nextItem($data, $conversions, $result, $sum)
//            };
        }
    }

    //This should never happen
    return $sum;
}

$sum = 0;
//Loop through the actual items and sum the result based on the result of the conversion (A = number or R = 0)
foreach ($items as $item) {
    $options = ['x' => [1, 4000], 'm' => [1, 4000], 'a' => [1, 4000], 's' => [1, 4000]];
    $sum += nextItem($options, $conversions);
}

echo $sum;


//$data = [
//    'x' => [1, 4000],
//    'm' => [1, 4000],
//    'a' => [1, 4000],
//    's' => [1, 4000],
//];

// Function to calculate the sum of all combinations for a given key
function calculateSum($ranges)
{
    $sum = 0;
    $rangeStart = $ranges[0];
    $rangeEnd = $ranges[1];

    for ($i = $rangeStart; $i <= $rangeEnd; $i++) {
        for ($j = $rangeStart; $j <= $rangeEnd; $j++) {
            $sum += $i + $j;
        }
    }

    return $sum;
}

//$totalSum = 0;
//// Iterate through each key in the data array
//foreach ($data as $key => $ranges) {
//    // Calculate and print the sum for each key
//    $result = calculateSum($ranges);
//    $totalSum+= $result;
//}
//echo $totalSum;
