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
abstract class MP1param extends MPfunction {
    public function __construct($obj_id, $parent = null, $val_param = null) {

        $this->objParams['object_id'] = $obj_id;
        if (!is_null($parent)) { $this->setParent($parent); }

//        echo "<br>MP1param construct entered: ".$this->getID();

        $this->children['param'] = $val_param;

//        echo "<br>MP1param construct: count=". count($this->children);
        //print_r($this->children);
    }

    public function printHTML() {
        //echo "<br>MP2params printHTML";
        $tmp_param  = $this->children['param'];

        return $this->printHTML_core() . "( " . $tmp_param->printHTML() . " )";
    }

    abstract protected function printHTML_core();
}

?>