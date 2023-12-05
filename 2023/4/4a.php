<?php
/** @var array $cards */
require_once '4-data.php';

$totalSum = 0;
foreach ($cards as $card) {
    //Get both type of numbers left and right of | sign
    list ($winningNumbers, $myNumbers) = explode(' | ', $card);
    $winningNumbers = array_filter(explode(' ', $winningNumbers), fn($number) => is_numeric($number));
    $myNumbers = array_filter(explode(' ', $myNumbers), fn($number) => is_numeric($number));

    //Use array_intersect to get the winning numbers that match my numbers
    $realWinners = array_intersect($winningNumbers, $myNumbers);

    //Double result per extra score (and set to 1 in the beginning)
    $result = 0;
    foreach ($realWinners as $realWinner) {
        $result = $result === 0 ? 1 : $result * 2;
    }

    $totalSum += $result;
}

echo $totalSum;
