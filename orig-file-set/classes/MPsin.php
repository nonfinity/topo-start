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
class MPsin extends MP1param {
    protected function evaluateCore($forced = false) {
        return sin($this->children['param']->evaluate($forced));
    }

    protected function printHTML_core() { return "sin"; }
}

?>