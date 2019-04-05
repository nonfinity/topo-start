<?php
/**
 * Description of ifThen
 *
 * An if-then command. Currently doesn't support if-then-else
 *
 * @author Nonfinity
 */
class ifThen extends node {
    public function printHTML() {
//        echo "<br>ifThen printHTML";

        return  "<br>if (" . $this->children['logical']->printHTML() . 
                ") then<br>{" . $this->children['codeBody']->printHTML() .
                "<br>}". $this->children['nextCommand']->printHTML();
    }
}

?>