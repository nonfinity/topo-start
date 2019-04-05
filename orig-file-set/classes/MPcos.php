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
class MPcos extends MP1param {
    protected function evaluateCore($forced = false) {
        return cos($this->children['param']->evaluate($forced));
    }

    protected function printHTML_core() { return "cos"; }
}

?>