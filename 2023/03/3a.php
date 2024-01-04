<?php
$data = preg_split("/\r\n|\n|\r/", file_get_contents('3-data.txt'));
$lines = array_map(fn($line) => str_split($line), $data);
$totalSum = 0;

/**
 * @param false|array $line
 * @param int $index
 * @param bool $numberIntersection
 * @return bool
 */
function hasNumberLineIntersection(false|array $line, int $index, bool $numberIntersection): bool
{
    if ($line) {
        $linePreviousChar = $line[$index - 1] ?? false;
        $lineCurrentChar = $line[$index] ?? false;
        $lineNextChar = $line[$index + 1] ?? false;
        if (($linePreviousChar !== false && !is_numeric($linePreviousChar) && $linePreviousChar !== '.') ||
            ($lineCurrentChar !== false && !is_numeric($lineCurrentChar) && $lineCurrentChar !== '.') ||
            ($lineNextChar !== false && !is_numeric($lineNextChar) && $lineNextChar !== '.')
        ) {
            $numberIntersection = true;
        }
    }
    return $numberIntersection;
}

foreach ($lines as $lineIndex => $lineCharacters) {
    //Store corresponding lines to check on later
    $previousLine = $lines[$lineIndex - 1] ?? false;
    $nextLine = $lines[$lineIndex + 1] ?? false;

    $number = '';
    $numberIntersection = false;
    foreach ($lineCharacters as $charIndex => $lineCharacter) {
        //Store corresponding characters to check on later
        $previousCharacter = $lineCharacters[$charIndex - 1] ?? false;
        $nextCharacter = $lineCharacters[$charIndex + 1] ?? false;

        if (is_numeric($lineCharacter)) {
            //If we found a number we can append the number to the string
            $number .= $lineCharacter;

            //Check if this number had intersection with symbol in other lines, only check next line if previous didn't have a match
            if ($numberIntersection === false) {
                $numberIntersection = hasNumberLineIntersection($previousLine, $charIndex, $numberIntersection);
                $numberIntersection = hasNumberLineIntersection($nextLine, $charIndex, $numberIntersection);

                //Check if this number had intersection with symbol with surrounding characters in same line
                if (($previousCharacter !== false && !is_numeric($previousCharacter) && $previousCharacter !== '.') ||
                    ($nextCharacter !== false && !is_numeric($nextCharacter) && $nextCharacter !== '.')
                ) {
                    $numberIntersection = true;
                }
            }
        } else {
            //If we didn't find a number, it means we can make the sum IF part of the number had symbol intersection
            if ($numberIntersection) {
                $totalSum += (int)$number;
                $numberIntersection = false;
            }

            $number = '';
        }

        //Special check for the last character on the line (else this number will never be added...)
        if ($nextCharacter === false && $numberIntersection) {
            $totalSum += (int)$number;
        }
    }
}

echo $totalSum;
