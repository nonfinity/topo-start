<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MP2multiply
 *
 * @author Nonfinity
 */
class MP2multiply extends MP2params {
    protected function evaluateCore($forced = false) {
        return $this->children['left']->evaluate($forced) * $this->children['right']->evaluate($forced);
    }

    protected function printHTML_core() { return "*"; }
}

?>