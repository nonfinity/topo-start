<?php
/*
 * Created on Feb 8, 2012
 *
 */
 /**
  * This is the child function for manually input or hard codedvalues in the WB set of classes
  * @author Nonfinity
  */

class wbValue extends wbFunction {
	protected $children = null;

	final    protected function errorCheck($child_values) {
		if (is_null($this->valueCache['current'])) {
			$this->raiseError($this->objParams['object_id'],"VAL-002","wbValue object value has not been set before attempting to retrieve");
		}
	}
	final    protected function calcCore($child_values) {
		//echo "\n<br>wbValue (".$this->objParams['object_id'].") assigned value: ".$this->valueCache['current'];
		return $this->valueCache['current'];
	}

	final    public    function writeValue($v) {
		$this->setValue($v);
	}

}
?>
