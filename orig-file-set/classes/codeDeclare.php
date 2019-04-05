<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of codeDeclare
 *
 * @author Nonfinity
 */
class codeDeclare extends MPfunction {
    public function __construct($obj_id, $parent = null, $val_nextCommand = null,
                                /*$val_declareType = null, */$val_varName = null, $val_varType = null) {

        $this->objParams['object_id'] = $obj_id;
        if (!is_null($parent)) { $this->setParent($parent); }

//        echo "<br>codeDeclare construct entered: ".$this->getID();

        //$this->children['declareType'] = $val_declareType;
        $this->children['varName'] = $val_varName;
        $this->children['varType'] = $val_varType;
        $this->children['nextCommand'] = $val_nextCommand;
//        echo "codeDeclare construct: count=". count($this->children);
        //print_r($this->children);
    }

    public function printHTML() {
        //echo "<br>codeDeclare printHTML";
        //$tmp_declareType  = $this->children['declareType'];
        $tmp_varName = $this->children['varName'];
        $tmp_varType = $this->children['varType'];
        $tmp_nextCommand = $this->children['nextCommand'];
        
        return "<br>Dim " . $tmp_varName->printHTML() . " as " . $tmp_varType->printHTML()
            . $tmp_nextCommand->printHTML();
    }

    public function evaluateCore($forced = false) { return 0; }
    
    //abstract protected function printHTML_core();
}

?>