<?php
$data = preg_split("/\r\n|\n|\r/", file_get_contents('4-data.txt'));
$cards = array_column(array_map(fn($line) => explode(': ', trim(str_replace('Card ', '', $line))), $data), 1, 0);

$totalSum = 0;
$copiesPerCard = [];

/**
 * @param int $cardNumber
 * @param int $totalWinners
 * @param array $copiesPerCard
 * @return void
 */
function createCopiesOfCard(int $cardNumber, int $totalWinners, array &$copiesPerCard): void
{
    //Create copies for every card
    for ($i = $cardNumber + 1; $i < $cardNumber + 1 + $totalWinners; $i++) {
        if (isset($copiesPerCard[$i])) {
            $copiesPerCard[$i]++;
        } else {
            $copiesPerCard[$i] = 1;
        }
    }
}

foreach ($cards as $cardNumber => $card) {
    //Get both type of numbers left and right of | sign
    list ($winningNumbers, $myNumbers) = explode(' | ', $card);
    $winningNumbers = array_filter(explode(' ', $winningNumbers), fn($number) => is_numeric($number));
    $myNumbers = array_filter(explode(' ', $myNumbers), fn($number) => is_numeric($number));

    //Use array_intersect to get the winning numbers that match my numbers & create copies
    $totalWinners = count(array_intersect($winningNumbers, $myNumbers));
    createCopiesOfCard($cardNumber, $totalWinners, $copiesPerCard);

    //Make sure the currently set copies also create new copies
    if (isset($copiesPerCard[$cardNumber])) {
        for ($i = 0; $i < $copiesPerCard[$cardNumber]; $i++) {
            createCopiesOfCard($cardNumber, $totalWinners, $copiesPerCard);
        }
    }
}

//Make a sum of both originals & copies created
$totalCopies = 0;
foreach ($copiesPerCard as $cardNumber => $amount) {
    $totalCopies += $amount;
}
$totalSum = count($cards) + $totalCopies;

echo $totalSum;
