<?php
$data = preg_split("/\r\n|\n|\r/", file_get_contents('11-data.txt'));
$universe = array_map(fn($line) => str_split($line), $data);

/**
 * @param $universe
 * @return array
 */
function expandUniverse($universe): array
{
    //Expand rows
    $addedRows = 0;
    foreach ($universe as $index => $line) {
        if (count(array_unique($line)) === 1) {
            $addedRows++;
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
            array_splice($universe[$j], $i + $addedColumns, 0, '.');
        }
    }

    return $universe;
}

$universe = expandUniverse($universe);

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

        $totalSteps += abs($galaxy['row'] - $checkGalaxy['row']);
        $totalSteps += abs($galaxy['column'] - $checkGalaxy['column']);
    }
}

//Divide by 2, as we calculated everything twice in previous loop (from a to b, and b to a)
echo $totalSteps / 2;
