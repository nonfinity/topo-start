<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MP2add
 *
 * @author Nonfinity
 */
class MPe extends MPconst {
	protected function evaluateCore($forced = false) {
        return exp(1);
    }

    protected function printHTML_core() { return "e"; }
}

?>