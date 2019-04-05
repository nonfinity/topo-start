<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MPValue
 *
 * @author Nonfinity
 */
class MPvalue extends MPfunction {
    public function __construct($obj_id, $parent = null, $value = null) {
        $this->children                 = null;
        $this->objParams['object_id']     = $obj_id;
        $this->objParams['parent_obj']    = $parent;
        if (!is_null($value)) { $this->setValue($value, false); }
    }
    public function updateValue($new_value) {
        $this->setValue($new_value, false);
//        $this->evaluate(true);
    }

    protected function evaluateCore($forced = false) {
//         return $this->valueCache['current'];
        if ($this->valueCache['current'] == '') {
            return 0;
        } else {
            return $this->valueCache['current'];
        }
     }

     public function printHTML() {
         //echo "<br>MPvalue printHTML";
         return $this->evaluate();
     }
}

?>