<?php
/**
 * Description of noSockets
 *
 *  This is the middle class for all nodeTypes that have no sockets
 *
 * @author Nonfinity
 */
abstract class noSockets extends node {
    protected $strInput;
    
    public function __construct($node_id, $node_name, $value = null) {
        $this->node_id      = $node_id;
        $this->node_name    = $node_name;
        $this->children     = null;
        $this->strInput     = $value;
    }
    
    final public function setValue($value) {
        $this->strInput = $value;
    }
}

?>