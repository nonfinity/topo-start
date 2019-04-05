<?php
/**
 * Description of msgBox
 *
 * A simple implementation of the MsgBox operator in Excel VBA
 *
 * @author Nonfinity
 */
class msgBox extends node {
    public function printHTML() {
//        echo "<br>msgBox printHTML";

        return  '<br>MsgBox "' . $this->children['msgString']->printHTML() . '"' .
                $this->children['nextCommand']->printHTML();
    }
}

?>