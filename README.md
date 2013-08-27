php-aggregation-operators
=========================

PHP implementation of aggregation operators

Currently available:
- Weigted Ordered Weighted Aggregation (WOWA)

To come:
- Ordered Weigted Average (OWA)
- Choquet's integral

Usage
-----

```php
$w = array(0.1, 0.2, 0.3, 0.4, 0.0);
$p = array(0.1, 0.2, 0.3, 0.4, 0.0);
$values = array(0.4, 0.2, 0.3, 0.1, 0.0);
echo WOWA::wowa($w, $p, $values);

```