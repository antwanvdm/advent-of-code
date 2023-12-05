<?php
/** @var array $lines */
require_once '3-data.php';

$totalSum = 0;
$lines = array_map(fn($line) => str_split($line), $lines);

/**
 * @param false|array $line
 * @param int $index
 * @return array
 */
function getAsteriskLineIntersectionNumbers(false|array $line, int $index): array
{
    $numbers = [];
    if ($line) {
        $linePreviousChar = $line[$index - 1] ?? false;
        $lineCurrentChar = $line[$index] ?? false;
        $lineNextChar = $line[$index + 1] ?? false;

        if (is_numeric($linePreviousChar)) {
            $numbers[] = getTotalNumberFound($line, $index - 1, $linePreviousChar);
            //I need this extra check if 2 numbers on the same line are active (with a . precisely in the middle)
            if (!is_numeric($lineCurrentChar) && is_numeric($lineNextChar)) {
                $numbers[] = getTotalNumberFound($line, $index + 1, $lineNextChar);
            }
        } elseif (is_numeric($lineCurrentChar)) {
            $numbers[] = getTotalNumberFound($line, $index, $lineCurrentChar);
        } elseif (is_numeric($lineNextChar)) {
            $numbers[] = getTotalNumberFound($line, $index + 1, $lineNextChar);
        }
    }
    return $numbers;
}

/**
 * @param array $line
 * @param int $index
 * @param int $number
 * @return int
 */
function getTotalNumberFound(array $line, int $index, int $number): int
{
    $linePreviousChar = $line[$index - 1] ?? false;
    if (is_numeric($linePreviousChar)) {
        $number = $linePreviousChar . $number;
        $linePreviousChar = $line[$index - 2] ?? false;
        if (is_numeric($linePreviousChar)) {
            $number = $linePreviousChar . $number;
        }
    }
    $lineNextChar = $line[$index + 1] ?? false;
    if (is_numeric($lineNextChar)) {
        $number .= $lineNextChar;
        $lineNextChar = $line[$index + 2] ?? false;
        if (is_numeric($lineNextChar)) {
            $number .= $lineNextChar;
        }
    }
    return $number;
}

foreach ($lines as $lineIndex => $lineCharacters) {
    //Store corresponding lines to check on later
    $previousLine = $lines[$lineIndex - 1] ?? false;
    $nextLine = $lines[$lineIndex + 1] ?? false;

    $asteriskNumbers = [];
    foreach ($lineCharacters as $charIndex => $lineCharacter) {
        //Store corresponding characters to check on later
        $previousCharacter = $lineCharacters[$charIndex - 1] ?? false;
        $nextCharacter = $lineCharacters[$charIndex + 1] ?? false;

        if ($lineCharacter === "*") {
            //Check if any corresponding line has numbers we need to multiply
            if (!empty($previousLineNumbers = getAsteriskLineIntersectionNumbers($previousLine, $charIndex))) {
                $asteriskNumbers = array_merge($asteriskNumbers, $previousLineNumbers);
            }
            if (!empty($nextLineNumbers = getAsteriskLineIntersectionNumbers($nextLine, $charIndex))) {
                $asteriskNumbers = array_merge($asteriskNumbers, $nextLineNumbers);
            }
            //Check if any corresponding character had numbers we need to multiply
            if (is_numeric($previousCharacter)) {
                $asteriskNumbers[] = getTotalNumberFound($lineCharacters, $charIndex - 1, $previousCharacter);
            }
            if (is_numeric($nextCharacter)) {
                $asteriskNumbers[] = getTotalNumberFound($lineCharacters, $charIndex + 1, $nextCharacter);
            }

            //Only make a sum if 2 numbers are found + always reset array for next asterisk
            if (count($asteriskNumbers) === 2) {
                $totalSum += ($asteriskNumbers[0] * $asteriskNumbers[1]);
            }
            $asteriskNumbers = [];
        }
    }
}

echo $totalSum;
