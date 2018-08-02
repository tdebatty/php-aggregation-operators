<?php

namespace Aggregation;

/**
 * Weighted Average
 *
 * @author tibo
 */
class WeightedAverage {
    public static function compute($weights, $values) {
        $result = 0;
        for ($i = 0; $i < count($weights); $i++) {
            $result += $weights[$i] * $values[$i];
        }

        return $result;
    }
}
