<?php
$data = preg_split("/\r\n|\n|\r/", file_get_contents('4-data.txt'));
$cards = array_column(array_map(fn($line) => explode(': ', trim(str_replace('Card ', '', $line))), $data), 1, 0);

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
