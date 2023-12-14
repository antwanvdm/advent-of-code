<?php
/** @var array $lines */
require_once '14-data.php';

//Transpose because way easier to handle
$transposedLines = array_map(fn($line) => implode('', $line), array_map(null, ...array_map(fn($line) => str_split($line), $lines)));
foreach ($transposedLines as $lineIndex => $line) {
    $entries = str_split($line);
    foreach ($entries as $index => $entry) {
        if ($entry === 'O') {
            $prevIndex = $index - 1;
            //Move all our O up!
            while (isset($entries[$prevIndex]) && $entries[$prevIndex] === '.') {
                $entries[$prevIndex] = 'O';
                $entries[$prevIndex + 1] = '.';
                $prevIndex--;
            }
        }
    }
    $transposedLines[$lineIndex] = implode('', $entries);
}

//Transpose back for final calculation
$lines = array_map(fn($line) => implode('', $line), array_map(null, ...array_map(fn($line) => str_split($line), $transposedLines)));

$sum = 0;
$load = count($lines);
//Load number is the highest on top, so we count down
foreach ($lines as $line) {
    $sum += $load * substr_count($line, 'O');
    $load--;
}

echo $sum;
