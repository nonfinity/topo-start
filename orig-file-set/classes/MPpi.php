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
class MPpi extends MPconst {
    protected function evaluateCore($forced = false) {
        return pi();
    }

    protected function printHTML_core() { return "&pi;"; }
}

?>