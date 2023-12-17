<?php
$contraption = preg_split("/\r\n|\n|\r/", file_get_contents('16-data.txt'));
$contraptionLines = array_map(fn($line) => str_split($line), $contraption);

/**
 * @param $needle
 * @param $haystack
 * @return bool
 */
function in_multi_array($needle, $haystack): bool
{
    foreach ($haystack as $item) {
        if ($item === $needle) {
            return true;
        }
    }

    return false;
}

/**
 * @param $tile
 * @param $position
 * @param $positions
 * @param $splitsDone
 * @return bool
 */
function checkTile($tile, &$position, &$positions, &$splitsDone): bool
{
    if ($tile === '|' && ($position['direction'] === 'right' || $position['direction'] === 'left')) {
        //Split to move up/down. Creates an extra position. Get out if we already found this split before
        if (in_multi_array(['row' => $position['row'], 'column' => $position['column']], $splitsDone)) {
            return false;
        }
        $splitsDone[] = ['row' => $position['row'], 'column' => $position['column']];

        $position['row'] = $position['row'] - 1;
        $position['direction'] = 'up';
        $positions[] = ['row' => ($position['row'] + 1), 'column' => $position['column'], 'direction' => 'down'];
    } elseif ($tile === '-' && ($position['direction'] === 'up' || $position['direction'] === 'down')) {
        //Split to move left/right. Creates an extra position. Get out if we already found this split before
        if (in_multi_array(['row' => $position['row'], 'column' => $position['column']], $splitsDone)) {
            return false;
        }
        $splitsDone[] = ['row' => $position['row'], 'column' => $position['column']];

        $position['column'] = $position['column'] - 1;
        $position['direction'] = 'left';
        $positions[] = ['row' => $position['row'], 'column' => $position['column'] + 1, 'direction' => 'right'];
    } elseif ($tile === '/') {
        //Change directions based on current situation
        switch ($position['direction']) {
            case 'right':
                $position['row']--;
                $position['direction'] = 'up';
                break;
            case 'left':
                $position['row']++;
                $position['direction'] = 'down';
                break;
            case 'up':
                $position['column']++;
                $position['direction'] = 'right';
                break;
            case 'down':
                $position['column']--;
                $position['direction'] = 'left';
                break;
        }
    } elseif ($tile === '\\') {
        //Change directions based on current situation
        switch ($position['direction']) {
            case 'right':
                $position['row']++;
                $position['direction'] = 'down';
                break;
            case 'left':
                $position['row']--;
                $position['direction'] = 'up';
                break;
            case 'up':
                $position['column']--;
                $position['direction'] = 'left';
                break;
            case 'down':
                $position['column']++;
                $position['direction'] = 'right';
                break;
        }
    } else {
        //Nothing is happening (either . or irrelevant switch), so continue our path
        switch ($position['direction']) {
            case 'right':
                $position['column']++;
                break;
            case 'left':
                $position['column']--;
                break;
            case 'up':
                $position['row']--;
                break;
            case 'down':
                $position['row']++;
                break;
        }
    }

    return true;
}

/**
 * The same code as 16a, but now in a method to re-use :)
 *
 * @param $contraptionLines
 * @param $startPosition
 * @return int
 */
function getTotalEnergizedTilesForStartPosition($contraptionLines, $startPosition): int
{
    $splitsDone = [];
    $positions = [$startPosition];
    $energizedTiles = [$startPosition];

    //Kep looping as long as some paths are still being followed
    while (count($positions) > 0) {
        foreach ($positions as $index => &$position) {
            if (!isset($contraptionLines[$position['row']]) || !isset($contraptionLines[$position['row']][$position['column']])) {
                unset($positions[$index]);
            } elseif (!checkTile($contraptionLines[$position['row']][$position['column']], $position, $positions, $splitsDone)) {
                unset($positions[$index]);
            }

            //Only add when next path is available
            if (isset($contraptionLines[$position['row']][$position['column']])) {
                $energizedTiles[] = $position;
            }
        }
    }

    //Remove double records before echo
    return count(array_unique(array_map(fn($tile) => $tile['row'] . ',' . $tile['column'], $energizedTiles)));
}

$options = [];
//Loop through options and add all possible outcomes (starting top, left, right, bottom)
for ($i = 0; $i < count($contraptionLines); $i++) {
    for ($j = 0; $j < count($contraptionLines[0]); $j++) {
        if ($i === 0) {
            $options[] = getTotalEnergizedTilesForStartPosition($contraptionLines, ['row' => $i, 'column' => $j, 'direction' => 'down']);
        }
        if ($i === count($contraptionLines) - 1) {
            $options[] = getTotalEnergizedTilesForStartPosition($contraptionLines, ['row' => $i, 'column' => $j, 'direction' => 'up']);
        }
    }

    $options[] = getTotalEnergizedTilesForStartPosition($contraptionLines, ['row' => $i, 'column' => 0, 'direction' => 'right']);
    $options[] = getTotalEnergizedTilesForStartPosition($contraptionLines, ['row' => $i, 'column' => count($contraptionLines[0]) - 1, 'direction' => 'left']);
}

//Get the maximum number out of all options
echo max($options);
