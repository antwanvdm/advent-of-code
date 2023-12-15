<?php
/** @var array $lines */
require_once '14-data.php';

/**
 * @param $lines
 * @return string[]
 */
function transposeLines($lines): array
{
    return array_map(fn($line) => implode('', $line), array_map(null, ...array_map(fn($line) => str_split($line), $lines)));
}

/**
 * @param $lines
 * @return string[]
 */
function revertLines($lines): array
{
    return array_map(fn($line) => strrev($line), $lines);
}

/**
 * @param $lines
 * @return string[]
 */
function northCycle($lines): array
{
    $transposedLines = moveLines(transposeLines($lines));
    return transposeLines($transposedLines);
}

/**
 * @param $lines
 * @return string[]
 */
function westCycle($lines): array
{
    return moveLines($lines);
}

/**
 * @param $lines
 * @return string[]
 */
function southCycle($lines): array
{
    $revertedLines = moveLines(revertLines(transposeLines($lines)));
    return transposeLines(revertLines($revertedLines));
}

/**
 * @param $lines
 * @return string[]
 */
function eastCycle($lines): array
{
    $revertedLines = moveLines(revertLines($lines));
    return revertLines($revertedLines);
}

/**
 * Run all cycles from north, to west, to south, to east
 *
 * @param $lines
 * @return string[]
 */
function cycle($lines): array
{
    return eastCycle(southCycle(westCycle(northCycle($lines))));
}

/**
 * @param $lines
 * @return string[]
 */
function moveLines($lines): array
{
    //Transpose because way easier to handle
    foreach ($lines as $lineIndex => $line) {
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
        $lines[$lineIndex] = implode('', $entries);
    }

    //Transpose back for final calculation
    return $lines;
}

$candidates = [];
for ($i = 1; $i <= 500; $i++) {
    //All cycles a few numbers of times to get a pattern
    $lines = cycle($lines);
    $prevSum = $sum ?? 0;

    $sum = 0;
    $load = count($lines);
    //Load number is the highest on top, so we count down
    foreach ($lines as $line) {
        $sum += $load * substr_count($line, 'O');
        $load--;
    }

    //Store the numbers so we can calculate for 1000000000
    $sumDiff = $prevSum - $sum;
    $candidates[$sum][] = $i;
}

//Only give me the results that actually have a pattern (one-day flies don't count)
$candidates = array_filter($candidates, fn($item) => count($item) > 1);
$target = 1000000000;

//Time to check which of the remaining candidates
foreach ($candidates as $number => $positions) {
    $diff = $positions[1] - $positions[0];

    //Rule out patterns that aren't consistent
    for ($i = 2; $i < count($positions); $i++) {
        $newDiff = $positions[$i] - $positions[$i - 1];
        if ($newDiff !== $diff) {
            continue 2;
        }
    }

    if (checkTargetIsWithoutDecimals($target, $positions[0], $diff)) {
        echo $number;
        break;
    }
}

/**
 * Check if the target is a real number after calculation, that's our match
 *
 * @param $target
 * @param $firstTerm
 * @param $commonDifference
 * @return bool
 */
function checkTargetIsWithoutDecimals($target, $firstTerm, $commonDifference): bool
{
    $result = ($target - $firstTerm) / $commonDifference + 1;
    return !is_float($result);
}
