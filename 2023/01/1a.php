<?php
$strings = preg_split("/\r\n|\n|\r/", file_get_contents('1-data.txt'));
$digits = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];

$totalSum = 0;
foreach ($strings as $string) {
    //Split string to array and find matches with intersect
    $arrayString = str_split($string);
    $foundDigits = array_intersect($arrayString, $digits);

    //Append the both numbers together and cast to int, so it can be summed up
    $firstValue = $foundDigits[array_key_first($foundDigits)];
    $lastValue = $foundDigits[array_key_last($foundDigits)];
    $totalSum += (int)($firstValue . $lastValue);
}

echo $totalSum;
