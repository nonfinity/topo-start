<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MP2params
 *
 * @author Nonfinity
 */
abstract class MP2params extends MPfunction {
    public function __construct($obj_id, $parent = null, $val_left = null, $val_right = null) {

        $this->objParams['object_id'] = $obj_id;
        if (!is_null($parent)) { $this->setParent($parent); }

//        echo "<br>MP2params construct entered: ".$this->getID();

        $this->children['left'] = $val_left;
        $this->children['right'] = $val_right;
//        echo "MP2params construct: count=". count($this->children);
        //print_r($this->children);
    }

    public function printHTML() {
        //echo "<br>MP2params printHTML";
        $tmp_left  = $this->children['left'];
        $tmp_right = $this->children['right'];
        return "( " . $tmp_left->printHTML() . " " . $this->printHTML_core() . " " . $tmp_right->printHTML() . " )";
    }

    abstract protected function printHTML_core();
}

?>