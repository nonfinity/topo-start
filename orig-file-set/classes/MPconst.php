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
abstract class MPconst  extends MPfunction {
    public function __construct($obj_id, $parent = null) {

        $this->objParams['object_id'] = $obj_id;
//        if (!is_null($parent)) { $this->setParent($parent); }
		$this->objParams['parent_obj']    = $parent;

//        echo "<br>MPconst  construct entered: ".$this->getID();

        $this->children          = null;

//        echo "<br>MP1param construct: count=". count($this->children);
        //print_r($this->children);
    }

    public function printHTML() {
         //echo "<br>MPconst printHTML";
         return $this->printHTML_core();
     }
}
?>