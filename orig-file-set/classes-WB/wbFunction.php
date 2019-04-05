<?php
/*
 * Created on Feb 5, 2012
 *
 * This is WBfunction it is designed to be an attempt at refactoring MPfunction
 * The goal is to provide better orthogonality by emphasizing the unit of the node
 */

 /**
  * @author Nonfinity
  * @todo build out child information retrieval functions ... similar to MPfunction?
  *
  */
abstract class wbFunction {
	/* WBfunction is designed around a three mode setup
	 *  1. Child handling and input control
	 *  2. Output preparation (each node is agnostic to parent identity and quantity)
	 *  3. Internal consistency (validation and error checking)
	 */

	 // Define class variables here
	protected $children     	= array();
	protected $valueCache   	= array( 'current'		=> null,
                                	     'last'			=> null,
                                    	 'delta'		=> null );
	protected $objParams    	= array( 'object_id'	=> null,
										 'object_name'	=> null,
                                     	 'is_dirty'		=> false);
	protected $errors			= array( 'state'		=> false,
										 'list'			=> array());
	protected $payload			= array();

	// magic methods
	final	 public	   function __construct($obj_id, $children_input = null) {
		$this->objParams['object_id'] = $obj_id;
		//echo "\n<br>WBfunction constructor with object_id: $obj_id of type " . get_class($this);

		if (!is_null($children_input)) {
			// Perform initial structural error checks
			if (!is_array($children_input)) { $this->raiseError($this->objParams['object_id'],"STR-001","Attempted child assignment from improper format (not an array)"); }
			if (!is_subclass_of($this,get_class(),false)) { $this->raiseError($this->objParams['object_id'],"STR-002","Attempted child assignment from improper object class (tried: get_class($this))"); }

			// Assign children to this object
			if (!$this->errors['state']){ $this->children = $children_input; }
		}
	}
	final    public    function __invoke($forced = false) {
		$this->performCalc($forced);
		return $this->getValue();
	}
    final    public    function __toString() { return get_class($this); }

	// Input/output control methods
	final    protected function grabPayload($forced = false) {			// this method retrieves a result payload from a child
		if (!is_null($this->children)) {
			foreach ($this->children as $key => $value) {
				$this->payload[$key] = $value->sendPayload($forced);
			}
			// Merge errors into self and mirror dirty state
			foreach ($this->payload as $key => $value) {
				//echo "\n<br>payload for key($key) is dumped as \n<br>"; var_dump($value);
				if ($value['errState']) {
					$this->errors['state'] = true;
					//echo "\n<br>erList payload for key($key) is dumped as \n<br>"; var_dump($value);
					$this->errors['list'] = array_merge($this->errors['list'], $value['errList']);
				}
			}
		}
	}
	final    protected function sendPayload($forced = false) {			// this method prepares the payload for delivery to parent
		if ($forced || $this->objParams['is_dirty']) {
			$this->performCalc($forced);
		}
		return array(	'value' 	=> $this->valueCache['current'],
						'errState'	=> $this->errors['state'],
						'errList'	=> $this->errors['list']);
	}
	final    protected function boxChildren() {							// this prepares child data into a standard format for calcCore()
		$ret_array = null;
		foreach ($this->payload as $key => $value) { $ret_array[$key] = $value['value']; }
		return $ret_array;
	}

	// Internal Consistency
	final    protected function raiseError($source = null, $code = null, $message = null) {
    	if (is_null($source)) {
    		$this->errors['list'][] = array('source' => $this->objParams['object_id'],'code' => 'ERR-001','message' => 'Unspecified error source');
    	} else if (is_null($code)) {
    		$this->errors['list'][] = array('source' => $source,'code' => 'ERR-002','message' => 'Otherwise unspecified error code');
    	} else {
    		$this->errors['list'][] = array('source' => $source,'code' => $code,'message' => $message);
    		$this->errors['state'] = true;
    	}
    }
	final	 protected function getValue() {
		if ($this->errors['state']) {
			if (count($this->errors['list']) == 1) {
				//var_dump($this->errors['list']);
				return $this->errors['list'][0]['code'];
			} else {
				return "Many errors(" . count($this->errors['list']) . ")";
			}
		} else {
			return $this->valueCache['current'];
		}
	}
	final    protected function setValue($new_value, $return_value = false) {	// performs administrivia associated with updating a value
        if (is_numeric($new_value)) {
            $this->valueCache['last'] = $this->valueCache['current'];
            $this->valueCache['current'] = $new_value;
            $this->valueCache['delta'] = $new_value - $this->valueCache['last'];
            $this->objParams['is_dirty'] = true;

            if ($return_value) { return $new_value; }
        } else {
            $this->raiseError($this->objParams['object_id'],"VAL-001","Attempted to set object to non-numeric value ($new_value) in setValue()");
        }
    }
	final    protected function performCalc($forced = false) {
		if ($forced || $this->objParams['is_dirty']) { $this->grabPayload($forced); }

		$boxed = $this->boxChildren();
		$this->errorCheck($boxed);
		//echo "\n<br>Dump " . get_class($this) . "(" . $this->objParams['object_id'] . ") now:\n<br>"; var_dump($this->errors);

		if ($this->errors['state']) {
			// merge payload error states to self
		} else {
			if ($forced || $this->objParams['is_dirty']) {
		 		$new_value = $this->calcCore($boxed);
		 		$this->setValue($new_value,false);
		 	}
		}
	}

	// Setting and finding child nodes
	final    public    function setChild($child_name, $pointer) {		// this method sets a given object as a child

        if( array_key_exists($child_name, $this->children) ) {
            $this->children[$child_name] = $pointer;
            $this->objParams['is_dirty'] = true;
            //echo "\n<br>Parent (" . $this->objParams['object_id'] .") now has child in slot $child_name";
        } else {
            throw new Exception('Invalid child name provided to setChild.');
        }
    }
    // find child by ID? anything else?
    // Decision point: to return requested data or a pointer to object?

	// Core functions to be defined by root classes
	abstract protected function errorCheck($child_values);
	abstract protected function calcCore($child_values);
	abstract public	   function printHTML();
}
?>