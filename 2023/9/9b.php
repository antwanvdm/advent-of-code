<?php
/** @var array $sensors */
require_once '9-data.php';

/**
 * @param array $numbers
 * @param array $allNumbers
 * @return int
 */
function getFinalSequenceNumberDown(array $numbers, array $allNumbers): int
{
    $newNumbers = [];
    $totalZeros = 0;

    //Loop through current numbers (per row) and count zeros in the new array
    foreach ($numbers as $index => $number) {
        if ($number === 0) {
            $totalZeros++;
        }

        //Since we have to make a sum with the next number, move on if we reach the end
        $nextNumber = $numbers[$index + 1] ?? false;
        if ($nextNumber === false) {
            break;
        }
        $newNumbers[] = $nextNumber - $number;
    }

    //If we have only zeros, we can move up the tree (that's why we reverse the array!)
    if ($totalZeros === count($newNumbers) + 1) {
        return getFinalSequenceNumbersUp(array_reverse($allNumbers));
    }

    //Else we can add the sequence and move on to the next line since we have more than just zeros
    $allNumbers[] = $newNumbers;
    return getFinalSequenceNumberDown($newNumbers, $allNumbers);
}

/**
 * @param array $sequence
 * @return int
 */
function getFinalSequenceNumbersUp(array $sequence): int
{
    $sum = 0;
    foreach ($sequence as $index => $numbers) {
        //If our next line doesn't exist, our sum is done
        $nextNumbers = $sequence[$index + 1] ?? false;
        if ($nextNumbers === false) {
            break;
        }

        $sum += $nextNumbers[array_key_last($nextNumbers)];
    }

    return $sum;
}

$totalSum = 0;
foreach ($sensors as $sensor) {
    //Per sensor, we need all the numbers (this time reversed to get the beginning) and sum up the result of our recursive magic
    $numbers = array_reverse(explode(' ', $sensor));
    $totalSum += getFinalSequenceNumberDown($numbers, [$numbers]);
}

echo $totalSum;

