<?php
/**
 * Description of subFunction
 *
 * declares a subroutine (not a function though... that's later)
 *
 * @author Nonfinity
 */
class subFunction extends node {
    public function printHTML() {
//        echo "<br>subFunction printHTML";

        return  '<br>Sub ' . $this->children['subName']->printHTML() . "()"
                . '<br>{' . $this->children['codeBody']->printHTML()
                . '<br>}' . $this->children['nextCommand']->printHTML();
    }
}

?>