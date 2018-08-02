<?php
namespace Aggregation;

define("EPSILON", 1E-10);
class WOWA {
    public static function wowa(array $w, array $p, array $a) {
        $num_values = count($w);
        $omega = array();

        $fer = setQ($w);
        sortBothBy($a, $p);

        $omega[0] = $fer->eval4($p[0], $num_values);

        $acc = $p[0];
        for ($i = 1; $i < $num_values; $i++) {
            $accv = $acc;
            $acc += $p[$i];
            $omega[$i] = $fer->eval4($acc, $num_values) - $fer->eval4($accv, $num_values);
        }

        return dotProduct($a, $omega);
    }
}

function dotProduct(array $a, array $b) {
    $acc = 0;
    for ($i=0; $i<count($a); $i++) {
        $acc += $a[$i] * $b[$i];
    }
    return $acc;
}

class wwLdf
{
    /**
     *
     * @var Point[]
     */
    public $d = array();


    /**
     *
     * @var InterpolationFunction[]
     */
    public $f = array();


    /**
     *
     * @param Point[] $points
     */
    public function ferQ($points) {
        $num_values = count($points) - 1;
        //var_dump($points);

        // Array of lines (wwrecta)
        /* @var $lines Line[] */
        $lines = array();  //wwLr L = new wwLr(num_values + 1); //******* Darrera modificacio

        //Ldf f2;
        // Aixo de sota ho trec.
        // wwLp dd = new wwLp (num_values);
        //$temp_function = new Funcio();

        // I si canvio d per dd en la definicio de la funcio, aixo ho puc treure
        // dd = d; /* avoids too many indirection passes */


        for ($i = 0; $i < $num_values; $i++) {
            $lines[] = new Line;
            $this->d[] = new Point;
            //$this->f[] = new Funcio;
        }
        //tempFun.initfunc(num_values);
        $this->d[$num_values] = new Point;


        // Start of computation...
        computeLi($lines, $points);
        //L.calculaLi(dd, num_values);

        for ($i = 0; $i < $num_values; $i++) {
            $this->d[$i]->x = $points[$i]->x;
            $this->d[$i]->y = $points[$i]->y;

            $this->f[$i] = new InterpolationFunction;
            $this->f[$i]->computeParameters($lines[$i], $lines[$i+1], $points[$i], $points[$i + 1]);
        }

        //echo $this->f[0];

        $this->d[$num_values]->x = $points[$num_values]->x;
        $this->d[$num_values]->y = $points[$num_values]->y;
    }

    public function eval4($x, $num_values) {
        /*echo "d[0]->x : " . $this->d[0]->x . "\n";
        echo "d[1]->x : " . $this->d[1]->x . "\n";
        echo "d[2]->x : " . $this->d[2]->x . "\n";
        echo "x : $x\n";*/
        for ($i = 0; $i < $num_values; $i++) {

            if (    leq($this->d[$i]->x, $x) AND
                    leq($x, $this->d[$i + 1]->x)) {

                //echo "!";
                return ($this->f[$i]->evalx($x));
            }
        }

        if (leq($x, $this->d[0]->x)) {
            return 0.0;
        }

        if (leq($this->d[$num_values]->x, $x)) {
            return 1.0;
        }

        return 0.0;
    }
}

/**
 * Sort both arrays using the values of the first one (value)
 */
function sortBothBy(array &$a_value, array &$p_unit) {

    $count = count($a_value);
    $temp = 0.0;

    for ($i = 0; $i < $count; $i++) {
        for ($j = $i+1; $j < $count; $j++) {

            if ($a_value[$j] > $a_value[$i]) {
                //echo "Switch\n";
                $temp = $p_unit[$j];
                $p_unit[$j] = $p_unit[$i];
                $p_unit[$i] = $temp;

                $temp = $a_value[$j];
                $a_value[$j] = $a_value[$i];
                $a_value[$i] = $temp;
            }
        }
    }
}

function setQ(array $w) {
    $count = count($w);

    /* @var $points Point[] */
    $points = array();

    // First point is (0,0)
    $points[0] = new Point;

    for ($i = 1.0; $i < $count+1; $i++) {
        $points[$i] = new Point;
        $points[$i]->x = $i / $count;
        $points[$i]->y = $w[$i - 1] + $points[$i - 1]->y;
    }

    //var_dump($points);

    $df = new wwLdf();
    //var_dump($df);
    // I'm here !!!
    $df->ferQ($points);
    //var_dump($df);
    return $df;
}

function computeSi(Point $di, Point $di_1) {
    if ($di->x == $di_1->x) {
        echo "Division by 0!\n";
        var_dump($di);
        var_dump($di_1);
        debug_print_backtrace();
        exit();
    }
    return ($di->y - $di_1->y) / ($di->x - $di_1->x);
}

function computeMi($si, $siP1, Point $di, Point $di_1, Point $diP1) {
    if (($si * $siP1) < 0.0) {
        return 0.0;
    }

    if (abs($si) > abs($siP1)) {
        $bx = ($diP1->y - $di->y) / $si + $di->x;
        $cx = ($bx + $diP1->x) / 2.0;
        return ($diP1->y - $di->y) / ($cx - $di->x);
    }

    if (abs($si) < abs($siP1)) {
        $bx = ($di->x - ($di->y - $di_1->y) / $siP1);
        $cx = ($bx + $di_1->x) / 2.0;
        return ($di->y - $di_1->y) / ($di->x - $cx);
    }

    return $si;
}

/**
 *
 * @param Line[] $lines
 * @param Point[] $points
 */
function computeLi(&$lines, &$points) {
    $N = count($points);

    $s = array(); // Array of floats (1 -> $N-1)
    $m = array(); // array of floats (1 -> $N-2)


    for ($i = 1; $i < $N; $i++) {
        $s[$i] = computeSi($points[$i], $points[$i - 1]);
    }

    for ($i = 1; $i < ($N - 1); $i++) {
        $m[$i] = computeMi($s[$i], $s[$i + 1], $points[$i], $points[$i - 1], $points[$i + 1]);
    }

    if (($m[1] == 0.0) && ($s[1] == 0.0)) {
        $m[0] = 0.0;

    } else if ($m[1] == 0.0) {
        $m[0] = PHP_INT_MAX;

    } else {
        $m[0] = $s[1] * $s[1] / $m[1];
    }

    if (($m[$N - 2] == 0.0) && ($s[$N-1] == 0.0)) { // TODO: check this: there could be a minimal difference between $s[$N] and 0 (see wwbasics.eq)
        $m[$N-1] = 0.0;

    } else if ($m[$N - 2] == 0.0) {
        $m[$N-1] = PHP_INT_MAX;

    } else {
        $m[$N-1] = $s[$N-1] * $s[$N-1] / $m[$N - 2];
    }

    for ($i = 0; $i < $N; $i++) {
        $lines[$i] = new Line;
        $lines[$i]->m = $m[$i];
        $lines[$i]->n = $points[$i]->y - $m[$i] * $points[$i]->x;
    }
}


class Line
{
    public $m = 0.0;
    public $n = 0.0;

    /**
     *
     * @param Point $p0
     * @param Point $p1
     * @return \Line
     */
    public static function buildFromPoints(Point $p0, Point $p1) {
        $l = new Line;
        $l->m = ($p0->y - $p1->y) / ($p0->x - $p1->x);
        $l->n = ($p1->x * $p0->y - $p0->x * $p1->y) / ($p1->x - $p0->x);
        return $l;
    }

    /**
     * Value of y for given $x
     *
     * @param type $x
     * @return type
     */
    public function evalx($x) {
        return $this->m * $x + $this->n;
    }
}

class Point
{
    public $x = 0.0;
    public $y = 0.0;

    public function __toString() {
        return "(" . $this->x . ", " . $this->y . ")";
    }

    public static function Bernstein(Point $di, Point $wi, Point $oi, $x) {
        $xi = $di->x;
        $mi = $di->y;
        $b = $wi->y;
        $ti = $oi->x;
        $mbi = $oi->y;

        if ((($ti - $xi) * ($ti - $xi)) == 0) /* Check so as not to divide */ /* by zero 		      */ {
            $epsilon = 0.000001;
            if (abs($ti - $x) * ($xi - $x) < $epsilon) {
                $y = ($di->y + $oi->y) / 2;
            } else {

            }
        }

        if ($ti == $xi) {
            $y = $mi;

        } else {
            $y = $mi * ($ti - $x) * ($ti - $x) + 2 * $b * ($x - $xi) * ($ti - $x);
            $y = $y + $mbi * ($x - $xi) * ($x - $xi);
            $y = $y / (($ti - $xi) * ($ti - $xi));
        }
        return $y;
    }
}

class InterpolationFunction
{
    /**
     * 1: linear
     * 2: double bernstein
     * @var int
     */
    public $type = 1;

    public $m = 0.0;
    public $n = 0.0;

    /**
     *
     * @var Point
     */
    public $di;

    /**
     *
     * @var Point
     */
    public $vi;

    /**
     *
     * @var Point
     */
    public $oi;

    /**
     *
     * @var Point
     */
    public $wi;

    /**
     *
     * @var Point
     */
    public $diP1;

    public function __toString() {
        return
                "t: " . $this->t . "\n" .
                "m: " . $this->m . "\n" .
                "n: " . $this->n . "\n" .
                "di: " . $this->di . "\n" .
                "vi: " . $this->vi . "\n" .
                "oi: " . $this->oi . "\n" .
                "wi: " . $this->wi . "\n" .
                "diP1" . $this->diP1 . "\n" ;
    }


    public function __construct() {
        $this->di = new Point;
        $this->vi = new Point;
        $this->oi = new Point;
        $this->wi = new Point;
        $this->diP1 = new Point;
    }

    public function computeParameters(Line $Li, Line $LiP1, Point $Di, Point $DiP1) {
        if (($Li->m == $LiP1->m) && ($Li->n == $LiP1->n)) {
            $this->t = 1;
            $this->m = $Li->m;
            $this->n = $Li->n;

        } else if ($Li->m == $LiP1->m) {
            $this->calcDVOWDNa($Li, $LiP1, $Di, $DiP1);

        } else {
            $this->calcDVOWDa($Li, $LiP1, $Di, $DiP1);
        }
    }

    public function calcDVOWDNa(Line $Li, Line $LiP1, Point $di, Point $diP1) {

        $vi = new Point;
        $wi = new Point;
        $oi = new Point;

        $tip = ($di->x + $diP1->x) / 2;
        $vi->x = ($di->x + $tip) / 2;
        $vi->y = $Li->m * ($di->x + $tip) / 2 + $Li->n;
        $wi->x = ($diP1->x + $tip) / 2;
        $wi->y = $LiP1->m * ($diP1->x + $tip) / 2 + $LiP1->n;

        $R = Line::buildFromPoints($vi, $wi);
        $oi->x = $tip;
        $oi->y = $R->evalx($tip);

        $this->t = 2;
        $this->di->x = $di->x;
        $this->di->y = $di->y;

        $this->vi->x = $vi->x;
        $this->vi->y = $vi->y;
        $this->oi->x = $oi->x;
        $this->oi->y = $oi->y;
        $this->wi->x = $wi->x;
        $this->wi->y = $wi->y;

        $this->diP1->x = $diP1->x;
        $this->diP1->y = $diP1->y;

    }

    public function calcDVOWDa(Line $Li, Line $LiP1, Point $di, Point $diP1) {

        $vi = new Point;
        $wi = new Point;
        $oi = new Point;

        // Different from DVOWDNa
        $ti = ($Li->n - $LiP1->n) / ($LiP1->m - $Li->m);
        $zi = ($LiP1->m * $Li->n - $Li->m * $LiP1->n) / ($LiP1->m - $Li->m);

        if (    leq($di->x, $ti) and
                leq($ti , $diP1->x) and
                leq($di->y, $zi) and
                leq($zi, $diP1->y)) {
            $tip = $ti;


        } else {
            $tip = ($di->x + $diP1->x) / 2.0;
        }

        $vi->x = ($di->x + $tip) / 2;
        $vi->y = $Li->m * ($di->x + $tip) / 2 + $Li->n;
        $wi->x = ($diP1->x + $tip) / 2;
        $wi->y = $LiP1->m * ($diP1->x + $tip) / 2 + $LiP1->n;
        $oi->x = $tip;

        $R = Line::buildFromPoints($vi, $wi);

        $oi->y = $R->evalx($tip);

        $this->t = 2;
        $this->di->x = $di->x;
        $this->di->y = $di->y;
        $this->vi->x = $vi->x;
        $this->vi->y = $vi->y;
        $this->oi->x = $oi->x;
        $this->oi->y = $oi->y;
        $this->wi->x = $wi->x;
        $this->wi->y = $wi->y;
        $this->diP1->x = $diP1->x;
        $this->diP1->y = $diP1->y;

    }

    public function evalx($x) {
        if ($this->t == 1) {
            return  $this->m * $x + $this->n;


        } else {
            //echo "di: " . $this->di->x . " vi: " . $this->vi->x . " oi: " . $this->oi->x . "\n";

            $xi = $this->di->x;
            $ti = $this->oi->x;
            if (($xi <= $x) and ($x <= $ti)) {

                $y = Point::Bernstein($this->di, $this->vi, $this->oi, $x);

            } else /* x in [ti, xiP1] */ {
                $y = Point::Bernstein($this->oi, $this->wi, $this->diP1, $x);
            }

        }

        if ($y < 0.0) {
            $y = 0.0;
        }

        if ($y > 1.0) {
            $y = 1.0;
        }

        return $y;

    }
}

function print_array($a) {
    echo implode(" | ", $a);
    echo "\n";
}

function leq($x, $y) {
    return (($x - $y) <= EPSILON);
}
