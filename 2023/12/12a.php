<?php
/** @var array $lines */
require_once '12-data.php';

/**
 * Recursive loop to get all possible combinations of # and . in place of all ? in the string
 *
 * @param $data
 * @param int $index
 * @param array $combinations
 * @return void
 */
function generateCombinations($data, int $index = 0, array &$combinations = []): void
{
    if ($index === count($data)) {
        $combinations[] = $data;
        return;
    }

    if ($data[$index] === '?') {
        $data[$index] = '#';
        generateCombinations($data, $index + 1, $combinations);

        $data[$index] = '.';
        generateCombinations($data, $index + 1, $combinations);

        // Reset back to '?' for further combinations
        $data[$index] = '?';
    } else {
        generateCombinations($data, $index + 1, $combinations);
    }
}

$arrangements = 0;
foreach ($lines as $line) {
    list ($springString, $order) = explode(' ', $line);
    $springs = str_split($springString);
    $orderNumbers = explode(',', $order);
    $combinations = [];
    generateCombinations($springs, 0, $combinations);

    //Check every combination
    foreach ($combinations as $springCombination) {
        $groups = [];
        $group = [];
        foreach ($springCombination as $index => $spring) {
            $nextChar = $springCombination[$index + 1] ?? false;

            //Make groups of #
            if ($spring !== '.') {
                $group[] = $spring;
                if ($nextChar === '.' || $nextChar === false) {
                    $groups[] = $group;
                    $group = [];
                }
            }
        }

        //If the groups match the total expected numbers
        if (count($groups) === count($orderNumbers)) {
            $totalCombinations = 0;
            foreach ($orderNumbers as $orderIndex => $orderNumber) {
                if ((int)$orderNumber === count($groups[$orderIndex])) {
                    $totalCombinations++;
                    //Only count the arrangement if the matches match the total groups
                    if ($totalCombinations === count($groups)) {
                        $arrangements++;
                    }
                }
            }
        }
    }
}

echo $arrangements;
