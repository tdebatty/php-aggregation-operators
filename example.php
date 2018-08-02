<?php

require_once __DIR__ . "/tests/bootstrap.php";

use Aggregation\WOWA;

$w = array(0.1, 0.2, 0.3, 0.4, 0.0);
$p = array(0.1, 0.2, 0.3, 0.4, 0.0);
$values = array(0.4, 0.2, 0.3, 0.1, 0.0);
echo WOWA::wowa($w, $p, $values);
