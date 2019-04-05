<?php
/**
 * Description of codeInput
 *
 *  Holds string input from the user (code names and hard coded constants, etc)
 *
 * @author Nonfinity
 */
class codeInput extends noSockets {
    public function printHTML() {
         //echo "<br>codeInput printHTML: " . $this->strInput;
         if (is_null($this->strInput)) { 
            return 'NULL'; 
        } else {
            return $this->strInput;
        }
     }
}

?>