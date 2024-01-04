<?php
$strings = preg_split("/\r\n|\n|\r/", file_get_contents('1-data.txt'));
$digits = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
//Prepend with 'zero' zo keys have same number as actual numbers
$digitsAsText = ['zero', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine'];

$totalSum = 0;
foreach ($strings as $string) {
    //Split string to array and find matches with intersect
    $arrayString = str_split($string);
    $foundDigits = array_intersect($arrayString, $digits);

    //More complex, find string digits and return their found location (index of array is the number I need which is cool)
    $foundDigitsAsTextFirstLast = array_map(fn($text) => ['first' => strpos($string, $text), 'last' => strrpos($string, $text)], array_filter($digitsAsText, fn($text) => str_contains($string, $text)));

    //Add them to the previous list, appending both first and last (will overwrite if the same)
    foreach ($foundDigitsAsTextFirstLast as $digit => $firstLast) {
        $foundDigits[$firstLast['first']] = $digit;
        $foundDigits[$firstLast['last']] = $digit;
    }

    //Sort on found location, so we have the first and last and correct order
    ksort($foundDigits);

    //Append the both numbers together and cast to int, so it can be summed up
    $firstValue = $foundDigits[array_key_first($foundDigits)];
    $lastValue = $foundDigits[array_key_last($foundDigits)];
    $totalSum += (int)($firstValue . $lastValue);
}

echo $totalSum;
