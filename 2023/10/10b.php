<?php
$data = preg_split("/\r\n|\n|\r/", file_get_contents('10-data.txt'));
$paths = array_map(fn($path) => str_split($path), $data);

//Als ik langs iets kom wat ik al heb gehad (oftewel alles wat is aangeraakt door mijn previous paths en ook weer aangeraakt wordt door mijn nieuwe path
//Vorige eind array gebruiken als startinput. Vanuit hier weten wat het pad is geweest en wat er dus 'enclosed' is.
//Letten op bochten en vanuit daar redeneren
//Pad is 13646 van de beschikbare 19600 karakters.

//Map all possibilities per tile
$pipes = [
    '.' => ['top' => [], 'down' => [], 'left' => [], 'right' => []], //Nothing
    'S' => ['top' => [], 'down' => [], 'left' => [], 'right' => []], //Starting point, also nothing
    '|' => [
        'top' => ['|', '7', 'F'],
        'down' => ['|', 'L', 'J'],
        'left' => [],
        'right' => []
    ], //up-down
    '-' => [
        'top' => [],
        'down' => [],
        'left' => ['-', 'F', 'L'],
        'right' => ['-', '7', 'J']
    ], //left-right
    'L' => [
        'top' => ['|', 'F', '7'],
        'down' => [],
        'left' => [],
        'right' => ['-', 'J', '7']
    ], //up-right
    'J' => [
        'top' => ['|', 'F', '7'],
        'down' => [],
        'left' => ['-', 'F', 'L'],
        'right' => []
    ], //up-left
    '7' => [
        'top' => [],
        'down' => ['|', 'J', 'L'],
        'left' => ['-', 'F', 'L'],
        'right' => []
    ], //down-left
    'F' => [
        'top' => [],
        'down' => ['|', 'J', 'L'],
        'left' => [],
        'right' => ['-', 'J', '7']
    ], //down-right
];

//Find our current line and index based on starting position (S)
$currentLineIndex = array_key_first(array_filter($paths, fn($path) => in_array('S', $path)));
$currentIndex = array_search('S', $paths[$currentLineIndex]);

/**
 * @param int $lineIndex
 * @param int $charIndex
 * @param array $paths
 * @param array $pipes
 * @param array $previousPaths
 * @return int[]
 */
function getNextPath(int $lineIndex, int $charIndex, array $paths, array $pipes, array $previousPaths): array
{
    $currentLine = $paths[$lineIndex];
    $previousLine = $paths[$lineIndex - 1] ?? false;
    $nextLine = $paths[$lineIndex + 1] ?? false;
    $previousChar = $currentLine[$charIndex - 1] ?? false;
    $nextChar = $currentLine[$charIndex + 1] ?? false;

    //Check for previous line (top)
    if ($previousLine && in_array($previousLine[$charIndex], $pipes[$currentLine[$charIndex]]['top'])) {
        $nexPath = ['line' => $lineIndex - 1, 'index' => $charIndex];
        if (count(array_filter($previousPaths, fn($previousPath) => $previousPath === $nexPath)) === 0) {
            return $nexPath;
        }
    }

    //Check for next line (down)
    if ($nextLine && in_array($nextLine[$charIndex], $pipes[$currentLine[$charIndex]]['down'])) {
        $nexPath = ['line' => $lineIndex + 1, 'index' => $charIndex];
        if (count(array_filter($previousPaths, fn($previousPath) => $previousPath === $nexPath)) === 0) {
            return $nexPath;
        }
    }

    //Check for previous character (left)
    if ($previousChar && in_array($previousChar, $pipes[$currentLine[$charIndex]]['left'])) {
        $nexPath = ['line' => $lineIndex, 'index' => $charIndex - 1];
        if (count(array_filter($previousPaths, fn($previousPath) => $previousPath === $nexPath)) === 0) {
            return $nexPath;
        }
    }

    //Check for next character (right)
    if ($nextChar && in_array($nextChar, $pipes[$currentLine[$charIndex]]['right'])) {
        return ['line' => $lineIndex, 'index' => $charIndex + 1];
    }

    //Probably will never happen if the code is bug-free.
    die('This should never happen');
}

//I know based on the map that the first routes go left and right
$nextPaths = [
    0 => ['line' => $currentLineIndex, 'index' => $currentIndex - 1],
    1 => ['line' => $currentLineIndex, 'index' => $currentIndex + 1],
];
//SAMPLE DATA PATH SITUATION
//$nextPaths = [
//    0 => ['line' => $currentLineIndex + 1, 'index' => $currentIndex],
//    1 => ['line' => $currentLineIndex, 'index' => $currentIndex + 1],
//];

//Let's store the first to routes we walked so we can always check we don't go back in the route
$previousPaths = [
    0 => [['line' => $currentLineIndex, 'index' => $currentIndex], $nextPaths[0]],
    1 => [['line' => $currentLineIndex, 'index' => $currentIndex], $nextPaths[1]],
];

$totalPaths = 1;
//Loop through the paths as long as they are not the same. When they are the same we've reached the furthest point
while ($nextPaths[0] !== $nextPaths[1]) {
    $nextPaths[0] = getNextPath($nextPaths[0]['line'], $nextPaths[0]['index'], $paths, $pipes, $previousPaths[0]);
    $nextPaths[1] = getNextPath($nextPaths[1]['line'], $nextPaths[1]['index'], $paths, $pipes, $previousPaths[1]);
    $previousPaths[0][] = $nextPaths[0];
    $previousPaths[1][] = $nextPaths[1];
    $totalPaths++;
}

echo $totalPaths;
