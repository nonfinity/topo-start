<?php
/**
 * Description of boolEqual
 *
 * boolean equivalence operator
 *
 * @author Nonfinity
 */
class boolEqual extends node {
    public function printHTML() {
//        echo "<br>boolEqual printHTML";

        return  $this->children['valLeft']->printHTML() . 
                " = " . $this->children['valRight']->printHTML();
    }
}

?>