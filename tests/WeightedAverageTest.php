<?php

namespace Aggregation;

use PHPUnit\Framework\TestCase;

class WeightedAverageTest extends TestCase {
    public function testCompute() {

        $this->assertEquals(
                0.9, WeightedAverage::compute(
                        [0.1, 0.1, 0.2], [1, 2, 3]));
    }
}
