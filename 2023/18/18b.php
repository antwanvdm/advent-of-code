<?php
$digPlan = preg_split("/\r\n|\n|\r/", file_get_contents('18-data-sample.txt'));
$digPlanSteps = array_map(fn($line) => explode(' ', $line), $digPlan);

function colorCodeToPlan($code)
{
    $directions = ['R', 'D', 'L', 'U'];
    $realCode = str_replace(['(#', ')'], '', $code);
    $steps = hexdec(substr($realCode, 0, 5));
    $direction = $directions[substr($realCode, 5, 1)];
    return [$direction, $steps];
}

$firstStep = colorCodeToPlan($digPlanSteps[0][2])[0];
$secondStep = colorCodeToPlan($digPlanSteps[1][2])[0];

//Gather information on map (array) to be filled
$totalX = 1;
$totalY = 1;
$gridLength = [];
foreach ($digPlanSteps as $digPlanStep) {
    $digPlanStep = colorCodeToPlan($digPlanStep[2]);
    if ($digPlanStep[0] === "R") {
        $totalX += $digPlanStep[1];
    }
    if ($digPlanStep[0] === "L") {
        $totalX -= $digPlanStep[1];
    }
    $gridLength['x'][] = $totalX;
    if ($digPlanStep[0] === "U") {
        $totalY -= $digPlanStep[1];
    }
    if ($digPlanStep[0] === "D") {
        $totalY += $digPlanStep[1];
    }
    $gridLength['y'][] = $totalY;
}

$startX = min($gridLength['x']);
$startY = min($gridLength['y']);
$xTiles = max($gridLength['x']) - $startX;
$yTiles = max($gridLength['y']) - $startY;

//Create the array based on the required spots
$map = array_fill(0, $yTiles + 1, array_fill(0, $xTiles + 1, '.'));

//Define where we start depending on data. For now, we know test data
//starts with RD and real data with LU, so only 1 statement required
if ($firstStep === 'R' && $secondStep === 'D') {
    $map[0][0] = '#';
    $currentRow = abs($startY) - 1;
    $currentColumn = abs($startX) - 1;
} else {
    $map[abs($startY)][abs($startX)] = '#';
    $currentRow = abs($startY) + 1;
    $currentColumn = abs($startX) + 1;
}

//Curve our path through the maze
foreach ($digPlanSteps as $index => $digPlanStep) {
    if ($digPlanStep[0] === "R") {
        $currentColumn++;
        for ($i = $currentColumn; $i < ($digPlanStep[1] + $currentColumn); $i++) {
            $map[$currentRow][$i] = '#';
        }
        $currentColumn = $i - 1;
    }
    if ($digPlanStep[0] === "L") {
        $currentColumn--;
        for ($i = $currentColumn; $i > ($currentColumn - $digPlanStep[1]); $i--) {
            $map[$currentRow][$i] = '#';
        }
        $currentColumn = $i + 1;
    }
    if ($digPlanStep[0] === "D") {
        $currentRow++;
        for ($i = $currentRow; $i < ($digPlanStep[1] + $currentRow); $i++) {
            $map[$i][$currentColumn] = '#';
        }
        $currentRow = $i - 1;
    }
    if ($digPlanStep[0] === "U") {
        $currentRow--;
        for ($i = $currentRow; $i > ($currentRow - $digPlanStep[1]); $i--) {
            $map[$i][$currentColumn] = '#';
        }
        $currentRow = $i + 1;
    }
}

/**
 * @param $matrix
 * @param $row
 * @param $col
 * @param $visited
 * @return int
 */
//function countEnclosedDots($matrix, $row, $col, &$visited): int
//{
//    //Check if the current position is out of bounds or already visited
//    if ($row < 0 || $col < 0 || $row >= count($matrix) || $col >= count($matrix[0]) || $visited[$row][$col]) {
//        return 0;
//    }
//
//    //Mark the current position as visited
//    $visited[$row][$col] = true;
//
//    //Check if the current position is a '#' (boundary)
//    if ($matrix[$row][$col] === "#") {
//        return 0;
//    }
//
//    //Count the current dot and recursively explore neighbors
//    $count = 1;
//    $count += countEnclosedDots($matrix, $row + 1, $col, $visited);
//    $count += countEnclosedDots($matrix, $row - 1, $col, $visited);
//    $count += countEnclosedDots($matrix, $row, $col + 1, $visited);
//    $count += countEnclosedDots($matrix, $row, $col - 1, $visited);
//
//    return $count;
//}
//
//$sum = 0;
//$startRow = 1;
//$startCol = 0;
////Gather all the surrounding # signs
//foreach ($map as $rowIndex => $mapLine) {
//    $lineSigns = array_keys($mapLine, '#');
//    if ($rowIndex === 0) {
//        //Start column based on drawing
//        $startCol = $lineSigns[0] + 1;
//    }
//    $sum += count($lineSigns);
//}
//
////Initialize a matrix to keep track of visited positions
//$visited = array_fill(0, count($map), array_fill(0, count($map[0]), false));
//
////Call the recursive function to get the total . in the boundaries
//$sum += countEnclosedDots($map, $startRow, $startCol, $visited);
//
//echo $sum;
//
//exit;

//TODO: So something efficient like below but without error margin... (
//TODO: if I make a corner, count that row backwards. Also remember which corners I made to prevent doubling of numbers

//Count the spots in between # with more #
$sum = 0;
//$map = array_slice($map, 35);
foreach ($map as $rowIndex => $mapLine) {
    $foundEntriesSets = array_keys($mapLine, '#');
    $hasGap = false;
    $gaps = [];
    $sum += count($foundEntriesSets);
    foreach ($foundEntriesSets as $index => $entry) {
        $prevEntry = $foundEntriesSets[$index - 1] ?? -1;
//        if ($hasGap !== $prevEntry) {
        $nextEntry = $foundEntriesSets[$index + 1] ?? false;
        $hasGap = $nextEntry - $entry > 1 ? $entry : false;
        if ($hasGap) {
            $gaps[] = $entry;
        }
//            if ($hasGap !== false && isEnclosed($map, $rowIndex, $entry + 1, count($map), count($map[0]))) {
//        $sum += $nextEntry !== false ? (($nextEntry - $entry) + 1) : 1;
        if (count($gaps) > 0 && count($gaps) % 2 !== 0 && $nextEntry !== false) {
            $sum += (($nextEntry - $entry) - 1);
        } else {
//                $sum += 1;
        }
//        }else {
//            $sum += 1;
//        }
    }
    $test = '';
}

echo $sum;
