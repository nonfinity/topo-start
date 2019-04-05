<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of codeForLoop
 *
 * @author Nonfinity
 */
class codeForLoop extends MPfunction {
    public function __construct($obj_id, $parent = null, $val_nextCommand = null,
                                $val_declareType = null, $val_varName = null, $val_varType = null) {

        $this->objParams['object_id'] = $obj_id;
        if (!is_null($parent)) { $this->setParent($parent); }

//        echo "<br>codeForLoop construct entered: ".$this->getID();

        $this->children['declareType'] = $val_declareType;
        $this->children['varName'] = $val_varName;
        $this->children['varType'] = $val_varType;
        //$this->children['nextCommand'] = $val_nextCommand;
//        echo "codeForLoop construct: count=". count($this->children);
        //print_r($this->children);
    }

    public function printHTML() {
        //echo "<br>codeForLoop printHTML";
        $tmp_declareType  = $this->children['declareType'];
        $tmp_varName = $this->children['varName'];
        $tmp_varType = $this->children['varType'];
        
        return "<br>" . $tmp_declareType->printHTML() . " " . $tmp_varName->printHTML() . " as " . $tmp_varType->printHTML();
    }

    public function evaluateCore($forced = false) { return 0; }
    
    //abstract protected function printHTML_core();
}

?>