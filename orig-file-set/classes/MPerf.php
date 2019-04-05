<?php

/*
 * This error function calculation was taken from the PHPExcel project ( http://phpexcel.codeplex.com )
 * All props to them that have the math skill to know how to do this
 */

/**
 * This calculates the error function. I'm using it as a stepping stone to try out options calculations
 *
 * @author Nonfinity
 */
class MPerf extends MP1param {
    protected function evaluateCore($forced = false) {
        $x = $this->children['param']->evaluate($forced);
        $_two_sqrtpi = 1.128379167095512574;

		if (abs($x) > 2.2) {
			return 1 - _erfcVal($x);
		}
		$sum = $term = $x;
		$xsqr = ($x * $x);
		$j = 1;
		do {
			$term *= $xsqr / $j;
			$sum -= $term / (2 * $j + 1);
			++$j;
			$term *= $xsqr / $j;
			$sum += $term / (2 * $j + 1);
			++$j;
			if ($sum == 0.0) {
				break;
			}
		} while (abs($term / $sum) > PRECISION);
		return $_two_sqrtpi * $sum;
    }

    private static function _erfcVal($x) {
		if (abs($x) < 2.2) {
			return 1 - self::_erfVal($x);
		}
		if ($x < 0) {
			return 2 - self::ERFC(-$x);
		}
		$a = $n = 1;
		$b = $c = $x;
		$d = ($x * $x) + 0.5;
		$q1 = $q2 = $b / $d;
		$t = 0;
		do {
			$t = $a * $n + $b * $x;
			$a = $b;
			$b = $t;
			$t = $c * $n + $d * $x;
			$c = $d;
			$d = $t;
			$n += 0.5;
			$q1 = $q2;
			$q2 = $b / $d;
		} while ((abs($q1 - $q2) / $q2) > PRECISION);
		return self::$_one_sqrtpi * exp(-$x * $x) * $q2;
	}	//	function _erfcVal()

    protected function printHTML_core() { return "erf"; }
}

?>