<?php
/**
 * Description of node
 *
 *  the abstract class from which all nodeType classes are polymorphed
 *
 * @author Nonfinity
 */
abstract class node {
    protected $node_id      = null;
    protected $node_name    = null;
    protected $parent_node  = null;
    protected $children     = array();
/*    protected $objParams    = array( 'object_id'  => null,
                                     'parent_obj' => null);
/*	protected $errors		= array( 'state'	  => false,
									 'origObject' => null,
									 'code'		  => null,
									 'message'	  => null); */
                                     
    public function __construct($node_id, $node_name) {
//        echo "<br>assignment construct entered: ".$this->getID();
        //$this->objParams['object_id'] = $node_id;
        $this->node_id   = $node_id;
        $this->node_name = $node_name;
        //if (!is_null($parent)) { $this->setParent($parent); }
        
        //children are delcared here because they are tested for key existence later
        
    }
//    final public function __invoke() { return $this->evaluate(); }
    final public function __invoke() { return $this; }
    final public function __toString() { return get_class($this); }

    final public function setChild($socket, $pointer) {
        
        //echo "<br><P>$socket<pre>" . print_r($pointer) . "</pre></p>";
        
        //if( array_key_exists($socket, $this->children) ) {
            $this->children[$socket] = $pointer;
        //    $this->objParams['is_dirty'] = true;
        //} else {
        //    throw new Exception('Invalid child name(' . $socket . ') provided to setChild.');
        //}
    }
    final public function setParent($pointer) {
        $this->parent_node = $pointer;
        //$this->objParams['parent_obj'] = $pointer;
        //$this->objParams['is_dirty'] = true;
    }
    final public function getChildren() { return $this->children; }
    final public function getChildNames() {
        foreach ($this->children as $key => $value) {
            $tmp[] = $key;
        }
        return $tmp;
    }
    final public function getChildIDs() {
        foreach ($this->children as $key => $value) {
            $tmp[] = $value->getID();
        }
        return $tmp;
    }
    final public function getID() { return $this->node_id;    }
    final public function getName() { return $this->node_name; }

    //abstract public function declareChildren();
    abstract public function printHTML();
}
?>