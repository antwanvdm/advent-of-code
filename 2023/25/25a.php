<?php
$data = preg_split("/\r\n|\n|\r/", file_get_contents('25-data-sample.txt'));
$connections = array_map(fn($line) => explode(' ', $line), array_column(array_map(fn($line) => explode(': ', $line), $data), 1, 0));
$components = array_values(array_unique(array_merge(array_keys($connections), ...array_values($connections))));

$oppositeConnections = [];
foreach ($connections as $key => $connection) {
    foreach($connection as $connectedKey) {
        $oppositeConnections[$connectedKey][] = $key;
    }
}

$test = '';
