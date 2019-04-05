<?php
/**
 * Description of forLoop
 *
 * a simple FOR loop
 *
 * @author Nonfinity
 */
class forLoop extends node {
    public function printHTML() {
//        echo "<br>forLoop printHTML";

        return  "<br>for " . $this->children['varName']->printHTML() . 
                " = " . $this->children['forStart']->printHTML() .
                " to " . $this->children['forEnd']->printHTML() .
                "<br>{" . $this->children['codeBody']->printHTML() .
                "<br>}" . $this->children['nextCommand']->printHTML();
    }
}

?>