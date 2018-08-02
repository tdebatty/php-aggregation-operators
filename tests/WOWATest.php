<?php

namespace Aggregation;

use PHPUnit\Framework\TestCase;

class WOWATest extends TestCase {
    public function testWowa() {
        $w = array(0.1, 0.2, 0.3, 0.4, 0.0);
        $p = array(0.1, 0.2, 0.3, 0.4, 0.0);
        $values = array(0.4, 0.2, 0.3, 0.1, 0.0);

        $result = WOWA::wowa($w, $p, $values);
        $this->assertEquals(0.194296875, $result);
    }
}
