<?php
$camelCards = preg_split("/\r\n|\n|\r/", file_get_contents('7-data.txt'));

/**
 * @param $handCards1
 * @param $handCards2
 * @return int
 */
function orderCardsValue($handCards1, $handCards2): int
{
    $cardsValue = ['A', 'K', 'Q', 'T', 9, 8, 7, 6, 5, 4, 3, 2, 'J'];

    foreach ($handCards1 as $index => $card) {
        if (array_search($card, $cardsValue) === array_search($handCards2[$index], $cardsValue)) {
            continue;
        }

        return array_search($card, $cardsValue) < array_search($handCards2[$index], $cardsValue) ? -1 : 1;
    }

    //Will never happen with this dataset
    return 0;
}

/**
 * Convert values that are "J" to the best other option
 *
 * @param $handCards
 * @param $handCountValues
 * @return void
 */
function mapJCardsInSet($handCards, &$handCountValues): void
{
    $JCardInHand = 'J';

    //Find the first (and best) option to convert J to (except J itself)
    foreach ($handCountValues as $card => $amount) {
        if ($card !== 'J') {
            $JCardInHand = $card;
            break;
        }
    }

    //Add the count to this best option and unset J
    foreach ($handCards as $handCard) {
        if ($handCard === 'J') {
            $handCountValues[$JCardInHand]++;
        }
    }
    unset($handCountValues['J']);
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

    //Extra sorting to make sure keys are ordered in by the value they have
    $cardsValue = ['A', 'K', 'Q', 'T', 9, 8, 7, 6, 5, 4, 3, 2, 'J'];
    uksort($hand1CountValues, fn($a, $b) => array_search($a, $cardsValue) < array_search($b, $cardsValue) ? -1 : 1);
    uksort($hand2CountValues, fn($a, $b) => array_search($a, $cardsValue) < array_search($b, $cardsValue) ? -1 : 1);
    arsort($hand1CountValues);
    arsort($hand2CountValues);

    //Convert magic for J-cards
    if (in_array('J', $handCards1) && count($hand1CountValues) > 1) {
        mapJCardsInSet($handCards1, $hand1CountValues);
    }
    if (in_array('J', $handCards2) && count($hand2CountValues) > 1) {
        mapJCardsInSet($handCards2, $hand2CountValues);
    }

    /** ALL THE CODE BELOW REMAINS THE SAME AS 7a **/

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
