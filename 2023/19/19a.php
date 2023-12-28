<?php
list($map, $items) = array_map(fn($text) => preg_split("/\r\n|\n|\r/", $text), preg_split("/\r\n\r\n|\n\n|\r\r/", file_get_contents('19-data.txt')));

//Map the conversions to indexed array we can easily use in our recursive logic
$conversions = [];
foreach ($map as $conversion) {
    $conversionData = explode('{', str_replace('}', '', $conversion));
    $test = '';
    $conversions[$conversionData[0]] = explode(',', $conversionData[1]);
}

/**
 * Recursive logic to match the result based on either value or condition
 *
 * @param array $data
 * @param array $conversions
 * @param string $input
 * @return int
 */
function nextItem(array $data, array $conversions, string $input = 'in'): int
{
    foreach ($conversions[$input] as $conversion) {
        if (!str_contains($conversion, ':')) {
            return match ($conversion) {
                'A' => array_sum($data),
                'R' => 0,
                default => nextItem($data, $conversions, $conversion)
            };
        } elseif (str_contains($conversion, '<')) {
            list($condition, $result) = explode(':', $conversion);
            list($key, $value) = explode('<', $condition);
            if ($data[$key] < $value) {
                return match ($result) {
                    'A' => array_sum($data),
                    'R' => 0,
                    default => nextItem($data, $conversions, $result)
                };
            }
        } elseif (str_contains($conversion, '>')) {
            list($condition, $result) = explode(':', $conversion);
            list($key, $value) = explode('>', $condition);
            if ($data[$key] > $value) {
                return match ($result) {
                    'A' => array_sum($data),
                    'R' => 0,
                    default => nextItem($data, $conversions, $result)
                };
            }
        }
    }

    //This should never happen
    return 0;
}

$sum = 0;
//Loop through the actual items and sum the result based on the result of the conversion (A = number or R = 0)
foreach ($items as $item) {
    $optionsData = explode(',', str_replace(['}', '{'], '', $item));
    $options = [];
    foreach ($optionsData as $option) {
        list($key, $value) = explode('=', $option);
        $options[$key] = $value;
    }

    $sum += nextItem($options, $conversions);
}

echo $sum;
