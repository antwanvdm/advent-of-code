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

$splitsDone = [];
$positions = [['row' => 0, 'column' => 0, 'direction' => 'right']];
$energizedTiles = [];

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
echo count(array_unique(array_map(fn($tile) => $tile['row'] . ',' . $tile['column'], $energizedTiles)));
