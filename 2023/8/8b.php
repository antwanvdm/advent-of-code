<?php
/** @var array $data */
require_once '8-data.php';

$instructions = str_split($data['instructions']);
$routes = [];

//Make a nice mapping so we can easily target L or R based on the instructions
foreach ($data['routes'] as $route) {
    list($entry, $options) = explode(' = ', $route);
    list($L, $R) = explode(', ', str_replace(['(', ')'], '', $options));
    $routes[$entry] = ['L' => $L, 'R' => $R];
}

/**
 * Recursive loop to count eventual total steps required
 *
 * @param array $instructions
 * @param array $routes
 * @param string $nextStep
 * @param int $steps
 * @return int
 */
function getStepsForInstructions(array $instructions, array $routes, string $nextStep, int $steps = 0): int
{
    foreach ($instructions as $instruction) {
        $nextStep = $routes[$nextStep][$instruction];
        $steps++;

        //Break out when we reach a string that ends with Z
        if (str_ends_with($nextStep, 'Z')) {
            return $steps;
        }
    }
    return getStepsForInstructions($instructions, $routes, $nextStep, $steps);
}

//Get all the strings that end with A
$firstSteps = array_keys(array_filter($routes, fn($key) => str_ends_with($key, 'A'), ARRAY_FILTER_USE_KEY));

//Get all the steps to reach the first string that ends with Z
$totalStepsPerInstruction = [];
foreach ($firstSteps as $firstStep) {
    $totalStepsPerInstruction[] = getStepsForInstructions($instructions, $routes, $firstStep);
}

//Now I understood I needed code that calculates how many times I needed to sum up the numbers to
//reach the same result for every individual number. I found out I needed math concepts which I'm
//not strong at, at all. So I used ChatGPT to give me the code for the logic I needed.
//Apparently I needed the greatest common divisor (GCD) and the least common multiple (LCM)
function gcd($a, $b)
{
    while ($b != 0) {
        $remainder = $a % $b;
        $a = $b;
        $b = $remainder;
    }
    return $a;
}

function lcm($a, $b)
{
    return abs($a * $b) / gcd($a, $b);
}

//Calculate the LCM for all numbers
$result = $totalStepsPerInstruction[0];
foreach ($totalStepsPerInstruction as $number) {
    $result = lcm($result, $number);
}

echo $result;
