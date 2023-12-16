<?php
/** @var array $data */
require_once '15-data.php';

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

$boxes = [];
foreach ($hashes as $hash) {
    //If string ends with "-", remove the entry
    if (str_contains($hash, '-')) {
        $code = rtrim($hash, '-');
        array_walk($boxes, function (&$box) use ($code) {
            if (isset($box[$code])) {
                unset($box[$code]);
            }
        });
    } else {
        //If string contains "=" add or replace
        list($code, $focal) = explode('=', $hash);
        $box = getValueForHash($code);
        $boxes[$box][$code] = $focal;
    }
}

$sum = 0;
//Magic calculation based on so many text (this assignment was all about reading..)
foreach ($boxes as $boxIndex => $boxEntries) {
    $slot = 1;
    foreach ($boxEntries as $code => $focal) {
        $sum += ($boxIndex + 1) * $slot * $focal;
        $slot++;
    }
}
echo $sum;



