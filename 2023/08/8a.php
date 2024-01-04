<?php
$data = preg_split("/\r\n\r\n|\n\n|\r\r/", file_get_contents('8-data.txt'));
$instructions = str_split($data[0]);
$rawRoutes = preg_split("/\r\n|\n|\r/", $data[1]);

//Make a nice mapping, so we can easily target L or R based on the instructions
$routes = [];
foreach ($rawRoutes as $route) {
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

        //Break out when we reach ZZZ
        if ($nextStep === 'ZZZ') {
            return $steps;
        }
    }
    return getStepsForInstructions($instructions, $routes, $nextStep, $steps);
}

//Important: Start at AAA, I didn't to this in the first place and ended up rewriting in JS because I got a segmentation fault
//Fun note: The fault occurs because the recursive loop would never end. Same happened in JS of course...
$firstStep = 'AAA';
echo getStepsForInstructions($instructions, $routes, $firstStep);
