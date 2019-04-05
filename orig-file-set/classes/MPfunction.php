<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MPfunction
 *
 * @author Nonfinity
 */
abstract class MPfunction {
    protected $children     = array();
    protected $valueCache   = array( 'current'    => null,
                                     'last'       => null,
                                     'delta'      => null );
    protected $objParams    = array( 'object_id'  => null,
                                     'parent_obj' => null,
                                     'is_dirty'   => false);
	protected $errors		= array( 'state'	  => false,
									 'origObject' => null,
									 'code'		  => null,
									 'message'	  => null);

    final public function __invoke() { return $this->evaluate(); }
    final public function __toString() { return get_class($this); }

    final public function setChild($child_id, $pointer) {
        
        //echo "<br><P>$child_id<pre>" . print_r($pointer) . "</pre></p>";
        
        if( array_key_exists($child_id, $this->children) ) {
            $this->children[$child_id] = $pointer;
            $this->objParams['is_dirty'] = true;
        } else {
            throw new Exception('Invalid child name provided to setChild.');
        }
    }
    final public function setParent($pointer) {
        $this->objParams['parent_obj'] = $pointer;
        $this->objParams['is_dirty'] = true;
    }
    final public function getChildren() { return $this->children; }
    final public function getChildNames() {
        foreach ($this->children as $key => $value) {
            $tmp[] = $key;
        }
        return $tmp;
    }
    final public function getChildIDs() {
        foreach ($this->children as $key => $value) {
            $tmp[] = $value->getID();
        }
        return $tmp;
    }
    final public function getID() { return $this->objParams['object_id']; }
    final public function evaluate($forced = false) {
        // Check all children and if any of them are not assigned
        // then set the $null_children flag to TRUE
        $null_children = false;
        if (!is_null($this->children)) {
            foreach ($this->children as $key => $value) {
                if (is_null($value)) { $null_children = true; }
            }
        }

        // Bomb out if there are any null (or unset) children
        if ($null_children) {
            throw new Exception('Attempt to Evaluate failed because of null children');
        } else {
            // The following conditions predicate a re-calc, otherwise return the cached value
            // 1. This object has never been successfully calculated
            // 2. This object is dirty
            // 3. There is an external request for a forced recalc
            if (is_null($this->valueCache['current']) || $this->objParams['is_dirty'] || $forced) {
                return $this->evaluateForced($forced);
            } else {
                return $this->valueCache['current'];
            }
        }
    }
    final protected function evaluateForced($forced = false) {
        return $this->setValue($this->evaluateCore($forced), true);
        $this->objParams['is_dirty'] = false;
    }
    final protected function setValue($new_value, $return_value) {
//        echo "<br>Attempting to set Object[". $this->objParams['object_id'] ."] equal to $new_value";

        // we are temporarily throwing caution into the wind and allowing non-numeric values to flow (comments 1-4)
        // 1. if (is_numeric($new_value)) {
            $this->valueCache['last'] = $this->valueCache['current'];
            $this->valueCache['current'] = $new_value;
            $this->valueCache['delta'] = $new_value - $this->valueCache['last'];
            $this->objParams['is_dirty'] = true;

            if ($return_value) { return $new_value; }
        // 2. } else {
        // 3.     return false;
        // 4. }
    }
    final public function getDirty() { return $this->objParams['is_dirty']?'true':'false'; }


    abstract public function printHTML();
    abstract protected function evaluateCore($forced = false);
}
?>