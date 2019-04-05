<?php
/*
 * Created on Feb 8, 2012
 *
 */
 /**
  * This is the child function for Division in the WB set of classes
  * @author Nonfinity
  */

class wbDivide extends wbFunction {
	protected $children = array(	'numerator' 	=> null,
									'denominator'	=> null);

	final    protected function errorCheck($child_values) {
		if ($child_values['denominator'] == 0) {
			$this->raiseError($this->objParams['object_id'],"VAL-003","wbDivide by zero!");
		}
	}
	final    protected function calcCore($child_values) {
		return $child_values['numerator'] / $child_values['denominator'];
	}
}
?>
