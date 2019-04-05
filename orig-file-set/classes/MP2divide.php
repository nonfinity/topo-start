<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MP2divide
 *
 * @author Nonfinity
 */
class MP2divide extends MP2params {

	protected function errorCheck($forced = false) {
		if ($this->children['right']->evaluate($forced) == 0) {
			return array( $this->objParams['object_id'],	// originating object
						  10001,							// error code (numeric)
						  'Data Error: divide by zero');	// error message
		} else {
			return NULL;
		}
	}

    protected function evaluateCore($forced = false) {
		return $this->children['left']->evaluate($forced) / $this->children['right']->evaluate($forced);
    }

    protected function printHTML_core() { return "/"; }
}

?>