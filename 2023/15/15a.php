<?php
//And the easiest data parsing of the year
$data = file_get_contents('15-data.txt');

/**
 * For every hash: Get ASCII for string, multiply by 17 and get remainder of 256
 *
 * @param $hash
 * @return int
 */
function getValueForHash($hash): int
{
    $currentValue = 0;
    $chars = str_split($hash);
    foreach ($chars as $char) {
        $currentValue += ord($char);
        $currentValue *= 17;
        $currentValue = $currentValue % 256;
    }
    return $currentValue;
}

//Get all hashes from the data & make total sum of every hash
$hashes = explode(',', $data);
$sum = 0;
foreach ($hashes as $hash) {
    $sum += getValueForHash($hash);
}
echo $sum;
