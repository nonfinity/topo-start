<?php
/**
 * Description of varDeclare
 *
 * @author Nonfinity
 */
class varDeclare extends node {
    public function printHTML() {
        //echo "<br>varDeclare printHTML";
        /*
        $tmp_declareType  = $this->children['declareType'];
        $tmp_varName = $this->children['varName'];
        $tmp_varType = $this->children['varType'];
        $tmp_nextCommand = $this->children['nextCommand'];
        */
        return  "<br>Dim " . $this->children['varName']->printHTML() . 
                " as " . $this->children['varType']->printHTML()
                . $this->children['nextCommand']->printHTML();
    }
}

?>