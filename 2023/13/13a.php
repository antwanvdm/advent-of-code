<?php
/** @var array $mirrors */
require_once '13-data.php';

/**
 * Switch rows & columns, as rows are easier to check upon
 *
 * @param $array
 * @return array
 */
function transposeArray($array): array
{
    return array_map(null, ...$array);
}

/**
 * Find the first reflection row and the calculated next reflections after this row
 * If we find a match with row 4 & 3, this will also check 5 & 2, 6 & 1, etc.
 *
 * @param $mirror
 * @return array|int[]
 */
function getReflectionMatch($mirror): array
{
    foreach ($mirror as $lineIndex => $mirrorLine) {
        $previousLine = $mirror[$lineIndex - 1] ?? false;

        if ($mirrorLine === $previousLine) {
            $totalReflections = 0;
            for ($i = $lineIndex - 1, $j = $lineIndex; $i >= 0 && $j < count($mirror); $i--, $j++) {
                if ($mirror[$i] === $mirror[$j]) {
                    $totalReflections++;
                }
            }

            //Only return if the row was next to either the left or right side
            if ($lineIndex + $totalReflections === count($mirror) || $lineIndex - $totalReflections === 0) {
                return [
                    'row' => $lineIndex,
                    'totalReflections' => $totalReflections,
                ];
            }
        }
    }

    return [
        'row' => 0,
        'totalReflections' => 0,
    ];
}

$sum = 0;
foreach ($mirrors as $mirrorIndex => $mirror) {
    //Get reflection matches for both rows and columns
    $horizontalMatches = getReflectionMatch($mirror);
    $verticalMirror = array_map(fn($line) => implode('', $line), transposeArray(array_map(fn($line) => str_split($line), $mirror)));
    $verticalMatches = getReflectionMatch($verticalMirror);

    //Sum up, normal amount or multiplied by 100 in case of horizontal
    if ($horizontalMatches['row'] !== 0) {
        $sum += ($horizontalMatches['row'] * 100);
    } else {
        $sum += $verticalMatches['row'];
    }
}

echo $sum;
