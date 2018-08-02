# php-aggregation-operators

PHP implementation of aggregation operators

Currently available:
- Weigted Ordered Weighted Aggregation (WOWA)

To come:
- Ordered Weigted Average (OWA)
- Choquet's integral

# Installation

```
composer require webd/aggregation
```

# Usage


```php
require "vendor/autoload.php";

use Aggregation\WOWA;

$w = array(0.1, 0.2, 0.3, 0.4, 0.0);
$p = array(0.1, 0.2, 0.3, 0.4, 0.0);
$values = array(0.4, 0.2, 0.3, 0.1, 0.0);
echo WOWA::wowa($w, $p, $values);
```
