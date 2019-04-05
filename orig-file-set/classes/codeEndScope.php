<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of codeEndScope
 *
 * @author Nonfinity
 */
class codeEndScope extends MPfunction {
    public function __construct($obj_id, $parent = null) {

        $this->objParams['object_id'] = $obj_id;
        if (!is_null($parent)) { $this->setParent($parent); }

//        echo "<br>codeEndScope construct entered: ".$this->getID();
//        echo "codeEndScope construct: count=". count($this->children);
        //print_r($this->children);
    }

    public function printHTML() {
        //echo "<br>codeEndScope printHTML";
        return "";
    }

    public function evaluateCore($forced = false) { return 0; }
    
    //abstract protected function printHTML_core();
}

?>