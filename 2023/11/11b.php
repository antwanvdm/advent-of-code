<?php
/** @var array $universe */
require_once '11-data.php';

/**
 * @param $universe
 * @param array $appendedRows
 * @param array $appendedColumns
 * @return array
 */
function expandUniverse($universe, &$appendedRows = [], &$appendedColumns = []): array
{
    //Expand rows
    $addedRows = 0;
    foreach ($universe as $index => $line) {
        if (count(array_unique($line)) === 1) {
            $addedRows++;
            $appendedRows[] = $index + $addedRows;
            array_splice($universe, $index + $addedRows, 0, [$line]);
        }
    }

    //Gather columns to expand columns
    $totalRows = count($universe);
    $totalColumns = count($universe[0]);
    $columnsToExpand = [];
    for ($i = 0; $i < $totalColumns; $i++) {
        $columnChars = [];
        for ($j = 0; $j < $totalRows; $j++) {
            $columnChars[] = $universe[$j][$i];
        }
        if (count(array_unique($columnChars)) === 1) {
            $columnsToExpand[] = $i;
        }
    }

    //Actually expand columns
    for ($j = 0; $j < $totalRows; $j++) {
        $addedColumns = 0;
        foreach ($columnsToExpand as $i) {
            $addedColumns++;
            $appendedColumns[] = $i + $addedColumns;
            array_splice($universe[$j], $i + $addedColumns, 0, '.');
        }
    }

    $appendedColumns = array_unique($appendedColumns);

    return $universe;
}

$universe = array_map(fn($line) => str_split($line), $universe);

//I now store 2 values to remember which rows and columns had gaps
$universe = expandUniverse($universe, $appendedRows, $appendedColumns);

//Gather all the indexes from the galaxies in the universe
$galaxies = [];
foreach ($universe as $rowIndex => $row) {
    foreach ($row as $columnIndex => $column) {
        if ($column === '#') {
            $galaxies[] = ['row' => $rowIndex, 'column' => $columnIndex];
        }
    }
}

//Sum up the differences between rows and columns
$totalSteps = 0;
foreach ($galaxies as $index => $galaxy) {
    foreach ($galaxies as $checkIndex => $checkGalaxy) {
        if ($index === $checkIndex) {
            continue;
        }

        //If the stored rows are part of the route, append 999998 (because we already added 1 in the first script)
        foreach ($appendedRows as $appendedRow) {
            if (($appendedRow > $galaxy['row'] && $appendedRow < $checkGalaxy['row']) ||
                ($appendedRow > $checkGalaxy['row'] && $appendedRow < $galaxy['row'])) {
                $totalSteps += 999998;
            }
        }

        //If the stored columns are part of the route, append 999998 (because we already added 1 in the first script)
        foreach ($appendedColumns as $appendedColumn) {
            if (($appendedColumn > $galaxy['column'] && $appendedColumn < $checkGalaxy['column']) ||
                ($appendedColumn > $checkGalaxy['column'] && $appendedColumn < $galaxy['column'])) {
                $totalSteps += 999998;
            }
        }

        $totalSteps += abs($galaxy['row'] - $checkGalaxy['row']);
        $totalSteps += abs($galaxy['column'] - $checkGalaxy['column']);
    }
}

//Divide by 2, as we calculated everything twice in previous loop (from a to b, and b to a)
echo $totalSteps / 2;
