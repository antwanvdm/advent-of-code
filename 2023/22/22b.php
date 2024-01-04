<?php
$data = preg_split("/\r\n|\n|\r/", file_get_contents('22-data.txt'));
$bricks = array_map(fn($line) => array_map(fn($coords) => explode(',', $coords), explode('~', $line)), $data);
usort($bricks, fn($a, $b) => max($a[0][2], $a[1][2]) < max($b[0][2], $b[1][2]) ? -1 : 1);

/**
 * @param $a
 * @param $b
 * @return bool
 */
function rangesOverlap($a, $b): bool
{
    return max($a[0][0], $b[0][0]) <= min($a[1][0], $b[1][0]) && max($a[0][1], $b[0][1]) <= min($a[1][1], $b[1][1]);
}

//Loop through items
foreach ($bricks as $index => $brick) {
    $maxZ = 1;
    //Loop through every item we already processed
    for ($i = 0; $i < $index; $i++) {
        $check = $bricks[$i];
        if (rangesOverlap($brick, $check)) {
            $maxZ = max($maxZ, $check[1][2] + 1);
        }
    }

    //Reset the positions to the new Z
    $bricks[$index][1][2] -= $brick[0][2] - $maxZ;
    $bricks[$index][0][2] = $maxZ;
}

//Sort again to make sure lowest are first
usort($bricks, fn($a, $b) => max($a[0][2], $a[1][2]) < max($b[0][2], $b[1][2]) ? -1 : 1);

//I need to prefill my overlap checks to make sure I also check empty overlaps that have overlaps the other way around
$overlapPerBrick = array_fill(0, count($bricks), []);
$overlappedBricks = [];
foreach ($bricks as $index => $brick) {
    //Only check bricks on top, the rest is not relevant
    $bricksProcessed = array_slice($bricks, 0, $index);
    $allBricksOnTop = array_filter($bricksProcessed, fn($a) => (int)$brick[0][2] === (int)$a[1][2] + 1);

    //Overlap magic
    foreach ($allBricksOnTop as $i => $bOt) {
        if (rangesOverlap($brick, $bricks[$i]) && $brick[0][2] === $bOt[1][2] + 1) {
            $overlapPerBrick[$i][] = $index;
            $overlappedBricks[$index][] = $i;
        }
    }
}

$sum = 0;
foreach ($bricks as $index => $brick) {
    $total = 0;
    $checked = [$index];

    //Check every item on top of the current brick
    $allOnTop = array_slice($overlapPerBrick, $index);
    foreach ($allOnTop as $brickIndex => $overlaps) {
        foreach ($overlaps as $overlap) {
            $brickIsChecked = 0;
            //Per item, I need to check the overlap
            foreach ($overlappedBricks[$overlap] as $match) {
                if (in_array($match, $checked)) {
                    $brickIsChecked++;
                }

                //If overlap is the same as current items & item has not yet been checked before, add to total
                if ($brickIsChecked === count($overlappedBricks[$overlap]) && !in_array($overlap, $checked)) {
                    $checked[] = $overlap;
                    $total++;
                }
            }
        }
    }

    //Add every total per brick to total sum
    $sum += $total;
}

echo $sum;
