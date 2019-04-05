<?php
/**
 * Description of assignment
 *
 * The value assignment operator
 *
 * @author Nonfinity
 */
class assignment extends node {
    public function printHTML() {
//        echo "<br>assignment printHTML";

        return  "<br>" . $this->children['varName']->printHTML() . 
                " = " . $this->children['valRight']->printHTML()
                . $this->children['nextCommand']->printHTML();
    }
}

?>