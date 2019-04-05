<?php
/*
 * Created on Feb 8, 2012
 *
 */
 /**
  * This is the child function for Addition in the WB set of classes
  * @author Nonfinity
  */

class wbAdd extends wbFunction {
	protected $children = array(	'left' 	=> null,
									'right'	=> null);

	final    protected function errorCheck($child_values) {
		// There are no data errors possible here
		// which are not captured in WBfunction
	}
	final    protected function calcCore($child_values) {
		return $child_values['left'] + $child_values['right'];
	}
}
?>
