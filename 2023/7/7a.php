<?php
/** @var array $camelCards */
require_once '7-data.php';

/**
 * @param $handCards1
 * @param $handCards2
 * @return int
 */
function orderCardsValue($handCards1, $handCards2): int
{
    $cardsValue = ['A', 'K', 'Q', 'J', 'T', 9, 8, 7, 6, 5, 4, 3, 2];

    foreach ($handCards1 as $index => $card) {
        if (array_search($card, $cardsValue) === array_search($handCards2[$index], $cardsValue)) {
            continue;
        }

        return array_search($card, $cardsValue) < array_search($handCards2[$index], $cardsValue) ? -1 : 1;
    }

    //Will never happen with this dataset
    return 0;
}

//Custom sorting based on the given rules
usort($camelCards, function ($a, $b) {
    list ($hand1,) = explode(' ', $a);
    list ($hand2,) = explode(' ', $b);

    //Make sure we have an array of the cards to compare
    $handCards1 = str_split($hand1);
    $handCards2 = str_split($hand2);

    //Get the unique values organized and sort by amount
    $hand1CountValues = array_count_values($handCards1);
    $hand2CountValues = array_count_values($handCards2);
    arsort($hand1CountValues);
    arsort($hand2CountValues);

    //If there immediately is a difference in count, return the result
    if (count($hand1CountValues) > count($hand2CountValues)) {
        return 1;
    } elseif (count($hand2CountValues) > count($hand1CountValues)) {
        return -1;
    }

    //Check for 5 of a kind
    if (count($hand1CountValues) === 1 && count($hand2CountValues) === 1) {
        return orderCardsValue($handCards1, $handCards2);
    }
    if (count($hand1CountValues) === 1 && count($hand2CountValues) > 1) {
        return 1;
    }
    if (count($hand2CountValues) === 1 && count($hand1CountValues) > 1) {
        return -1;
    }

    //Check for 4 of a kind // Full house
    if (count($hand1CountValues) === 2 && count($hand2CountValues) === 2) {
        if ($hand1CountValues[array_key_first($hand1CountValues)] === 4 &&
            $hand2CountValues[array_key_first($hand2CountValues)] === 4) {
            return orderCardsValue($handCards1, $handCards2);
        }
        if ($hand1CountValues[array_key_first($hand1CountValues)] === 4 &&
            $hand2CountValues[array_key_first($hand2CountValues)] === 3) {
            return -1;
        }
        if ($hand1CountValues[array_key_first($hand1CountValues)] === 3 &&
            $hand2CountValues[array_key_first($hand2CountValues)] === 4) {
            return 1;
        }

        if ($hand1CountValues[array_key_first($hand1CountValues)] === 3 &&
            $hand2CountValues[array_key_first($hand2CountValues)] === 3) {
            return orderCardsValue($handCards1, $handCards2);
        }
    }

    //Check for 3 of a kind or 2 pair
    if (count($hand1CountValues) === 3 && count($hand2CountValues) === 3) {
        if (($hand1CountValues[array_key_first($hand1CountValues)] === 3 &&
                $hand2CountValues[array_key_first($hand2CountValues)] === 3) ||
            ($hand1CountValues[array_key_first($hand1CountValues)] === 2 &&
                $hand2CountValues[array_key_first($hand2CountValues)] === 2)) {
            return orderCardsValue($handCards1, $handCards2);
        }
        if ($hand1CountValues[array_key_first($hand1CountValues)] === 3 &&
            $hand2CountValues[array_key_first($hand2CountValues)] === 2) {
            return -1;
        }
        if ($hand1CountValues[array_key_first($hand1CountValues)] === 2 &&
            $hand2CountValues[array_key_first($hand2CountValues)] === 3) {
            return 1;
        }
    }

    //Check for 1 pair
    if (count($hand1CountValues) === 4 && count($hand2CountValues) === 4) {
        return orderCardsValue($handCards1, $handCards2);
    }
    if (count($hand1CountValues) === 4 && count($hand2CountValues) === 5) {
        return 1;
    }
    if (count($hand2CountValues) === 5 && count($hand1CountValues) === 4) {
        return -1;
    }

    //Last scenario, all different cards
    return orderCardsValue($handCards1, $handCards2);
});

$totalSum = 0;
//Make a sum based on the bids in the reversed array (lowest cards first!)
foreach (array_reverse($camelCards) as $index => $camelCard) {
    list (, $bid) = explode(' ', $camelCard);
    $totalSum += (($index + 1) * $bid);
}

echo $totalSum;
