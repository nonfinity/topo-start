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
class MP2add extends MP2params {
    protected function evaluateCore($forced = false) {
        return $this->children['left']->evaluate($forced) + $this->children['right']->evaluate($forced);
    }

    protected function printHTML_core() { return "+"; }
}

?>