<?php
/**
 * Description of MPformula
 *
 *  This is the PHP object that represents the entire graph.
 *
 * @author Nonfinity
 */
class graphRoot {
    //<editor-fold defaultstate="collapsed" desc="graph_root Members">
    private $graph          = null;
    private $graphID        = 0;
    private $root_id        = 0;
    private $long_descr     = "";
    private $short_name     = "";
    //</editor-fold>

    //<editor-fold defaultstate="collapsed" desc="graph_root Magic Methods">
    public function __construct($graphID) {
        $this->graphID = $graphID;
        $this->rebuild();
    }
    public function __destruct() { }
//  public function __sleep() { }
//  public function __wakeup() { }
    public function __toString() { return "".$this->printHTML(); }
    public function __invoke() { return $this; }
//  public function __clone() { }
    public function __get($name) {
        switch($name) {
            case "long_descr":
                return $this->long_descr;
                break;
            case "short_name":
                return $this->short_name;
                break;
            default:
                if (array_key_exists($name, $this->cache))
                {
                    return $this->cache[$name];
                } else {
                    return null;
                }
                break;
        }
    }
    //</editor-fold>

    //<editor-fold defaultstate="collapsed" desc="graph_root Public Methods">
    public function rebuild() {
        $show_debug      = false;
        
        $server_addr     = "localhost";
        $db              = "calc_engine2";
        $user            = "php_user";
        $pass            = "php_user";

        $qry_graphHeader = "SELECT graph_id, description, root_node_id FROM graphs WHERE graph_id = ".$this->graphID;
        $qry_nodeList    = "SELECT n.node_id, n.text_name as node_name, nt.text_name as code_name
                              FROM nodes n INNER JOIN nodeTypes nt on n.nodetype_id = nt.nodetype_id
                             WHERE n.graph_id = ".$this->graphID."
                             ORDER BY n.node_id";
        $qry_newEdges    = "SELECT n1.node_id as head_node_id, n1.text_name as head_name,
                                   ns.text_name as socket_name,
                                   n2.node_id as tail_node_id, n2.text_name as tail_name
                              FROM graphs g INNER JOIN nodes       n1 on n1.graph_id = g.graph_id
                                            INNER JOIN nodes       n2 on n2.graph_id = g.graph_id
                                            INNER JOIN edges       e  on (e.graph_id = g.graph_id
                                                                      and n1.node_id = e.head_node_id
                                                                      and n2.node_id = e.tail_node_id)
                                            INNER JOIN nodeTypes   nt on n1.nodeType_id = nt.nodeType_id
                                            INNER JOIN nodeSockets ns on (ns.nodeSocket_id = e.nodeSocket_id
                                                                      and ns.nodeType_id = nt.nodeType_id)
                             WHERE g.graph_id = ".$this->graphID."
                             ORDER BY n1.node_id asc";
        
        $cn = new qryFactory($server_addr, $db, $user, $pass);
        $cn->open();
		if ($show_debug) { echo "<br>cn opened"; }

        // This is graph header info
        $webInfo = $cn->newQry("webInfo", $qry_graphHeader);
            $row = $webInfo->getRow();
            $this->long_descr   = $row['description'];
            $this->root_id      = $row['root_node_id'];
        $webInfo->release();
        if ($show_debug) { echo "<br>web info population"; }

        // This populates a temporary array used to house the nodes
        // that belong to this graph before the tree is constructed
        $nodeList = $cn->newQry("nodeList", $qry_nodeList);
        if ($show_debug) {  echo "<br>nodeList starting"; }
            while ($row = $nodeList->getRow()) {
                $tmp_nodeID      = $row['node_id'];
                $tmp_nodeName      = $row['node_name'];
                $tmp_codename   = $row['code_name'];
                if ($show_debug) { echo "<br>nodeSet[$tmp_nodeID] = new $tmp_codename($tmp_nodeID)"; }
                //echo "<br>nodeSet[$tmp_nodeID] = new $tmp_codename($tmp_nodeID)";
                $nodeSet[$tmp_nodeID] = new $tmp_codename($tmp_nodeID, $tmp_nodeName);
            }
        $nodeList->release();
        if ($show_debug) { echo "<br>nodeList completed. Array filled."; }
        
        //foreach($nodeSet as $key => $value) {
        //    echo "<br>" . $value->getName() . "($key) of class " . get_class($value);
        //}
        //echo "<hr>";
        
        // This pulls in all the edge and socket combinations
        // Now iterate through edges and assign them to nodes
        $edgeList = $cn->newQry("edgeList", $qry_newEdges);
        if ($show_debug) { echo "<br>Edges assignment beginning"; }
            while ($row = $edgeList->getRow()) {
                $tmp_head_id = $row['head_node_id'];
                $tmp_tail_id = $row['tail_node_id'];
                $tmp_socket = $row['socket_name'];
                
                //echo "<br>Set " . $row['head_name'] . "($tmp_head_id) socket $tmp_socket with child " . $row['tail_name'] . "($tmp_tail_id)";
                if ($show_debug) { echo "<br>Set $tmp_head_id socket $tmp_socket with child " . $row['tail_name']; }
                $nodeSet[$tmp_head_id]->setChild($tmp_socket, $nodeSet[$tmp_tail_id]);
            }
        $edgeList->release();
        if ($show_debug) { echo "<br>All edges assigned"; }
        
        //$cn->close();
        $this->graph = $nodeSet[$this->root_id];
        if ($show_debug) {  echo "<br>graph is of type ". get_class($this->graph); }
    }  
    public function getValueMembers() { return $this->getMembersByType($this->graph,'codeInput'); }
    public function getByID($member_id) { return $this->getMemberByID($this->graph, $member_id); }
    public function printHTML() {
//        $tmp = $this->graph;
//        echo "<br>MPforuma printHTML";
        return $this->graph->printHTML();
    }
    //</editor-fold>

    //<editor-fold defaultstate="collapsed" desc="graph_root Private Methods">
    private function getMembersByType($currNode, $class_name, $blacklist = array()) {        
        $childList = $currNode->getChildren();
        $blacklist = $blacklist + array($currNode->getID());
        $matches = array();
        
        //echo "<br><br><br>node " . $currNode->getID() . " is null: " . (is_null($childList) ? "yes" : "no") . ". is empty: " . (empty($childList) ? "yes" : "no");
        //echo print_r($childList);
        if(!is_null($childList) && !empty($childList)) {
            foreach($childList as $key => $value) {
                //echo "<br>child $key => $value";
                if(!in_array($value->getID(),$blacklist)) { $matches = $matches + $this->getMembersByType($value, $class_name, $blacklist); }
            }
        }
        
        // test self for membership
        //echo "<br>Testing node (" . $currNode->getID() . ") of class " . get_class($currNode) . " vs $class_name: " . (is_subclass_of(get_class($currNode),$class_name) ? "yes" : "no");
        //if(is_subclass_of(get_class($currNode), $class_name)) {
        if($class_name == get_class($currNode)) { $matches = $matches + array($currNode->getID() => $currNode); } 

        return $matches;        
    }
    private function getMemberByID($leaf_obj, $search_id) {
        $childList = $leaf_obj->getChildren();
        if (!is_null($childList)) {
            foreach($childList as $key => $value) {
                if($search_id == $value->getID()) {
                    return $value;
                }
            }
            foreach($childList as $key => $value) {
                $tmp = $this->getMemberByID($value, $search_id);
                if (!is_null($tmp)) { return $tmp; }
            }
        }
        //echo "searching for $search_id. returning null";
        return null;
    }
    //</editor-fold>
    
    
    
    
    
    
    
    
  
}
?>