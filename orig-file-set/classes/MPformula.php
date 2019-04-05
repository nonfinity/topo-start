<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MPformula
 *
 * @author Nonfinity
 */
class MPformula {
    //<editor-fold defaultstate="collapsed" desc="MP_formula Members">
    private $webObj         = array();
    private $webID          = 0;
    private $root_id        = 0;
    private $long_descr     = "";
    private $short_name     = "";
    private $cache          = array('eval'          => null,
                                    'memberCount'   => null);
    //</editor-fold>

    //<editor-fold defaultstate="collapsed" desc="MP_formula Magic Methods">
    public function __construct($webID) {
        $this->webID = $webID;

        $this->rebuild();
    }
    public function __destruct() { }
//  public function __sleep() { }
//  public function __wakeup() { }
    public function __toString() { return "".$this->evaluate(); }
    public function __invoke() { return $this->evaluate(); }
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

    //<editor-fold defaultstate="collapsed" desc="MP_formula Public Methods">
    public function evaluate($forced = false) {
        if ($this->cache['eval'] == null || $forced) {
            return $this->evaluateForce($forced);
        } else {
            return $this->cache['eval'];
        }
    }
    public function evaluateForce($forced = false) {
        $tmp = $this->webObj;

//        echo "<br>webOb j size = ". count($this->webObj) ." normal, and ". count($this->webObj, 1) ." recursive";
//        print_r($tmp);
        $this->cache['eval'] = $tmp->evaluate($forced);

//        echo "<br>Formula returns: ". $this->cache['eval'];
        return $this->cache['eval'];
    }
    public function rebuild() {
        $show_debug = false;
        //<editor-fold defaultstate="collapsed" desc="Formula Constants">
        $server_addr    = "localhost";
        $db             = "calc_engine1";
        $user           = "php_user";
        $pass           = "php_user";

        $qry_getObj     = "SELECT DISTINCT o.fx_object_id, ot.code_object_name
                           FROM fx_edges e INNER JOIN fx_objects o ON (e.parent_object_id = o.fx_object_id
                                                                    OR e.child_object_id = o.fx_object_id)
                                           INNER JOIN fx_object_types ot ON o.fx_object_type_id = ot.fx_object_type_id
                           WHERE e.web_id = ".$this->webID."
                           ORDER BY o.fx_object_id ASC";
        $qry_webBuilder  = "SELECT web_order, parent_object_id, child_object_id, child_leg_name
                           FROM fx_edges e
                           WHERE e.web_id = ".$this->webID."
                           ORDER BY e.web_order ASC";
        $qry_webInfo     = "SELECT fx_web_id web_id, web_descr, root_object FROM fx_web WHERE fx_web_id = ".$this->webID;
        //</editor-fold>

        $cn = new qryFactory($server_addr, $db, $user, $pass);
        $cn->open();
		if ($show_debug) { echo "<br>cn opened"; }

        // This populates the relevant information about this formula
        $webInfo = $cn->newQry("webInfo", $qry_webInfo);
            $row = $webInfo->getRow();
            $this->long_descr   = $row['web_descr'];
            $this->root_id      = $row['root_object'];
        $webInfo->release();
        if ($show_debug) { echo "<br>web info population"; }

        // This populates a temporary array used to house the objects
        // that belong to this formula before the tree is reconstructed
        $objList = $cn->newQry("objList", $qry_getObj);
        if ($show_debug) {  echo "<br>objList made"; }
            while ($row = $objList->getRow()) {
                $tmp_objId      = $row['fx_object_id'];
                $tmp_codename   = $row['code_object_name'];
                if ($show_debug) {  echo "<br>objID = $tmp_objId and codename = $tmp_codename"; 
                                    echo "<br>tmp_objSet[$tmp_objId] = new $tmp_codename($tmp_objId)"; }
                $tmp_objSet[$tmp_objId] = new $tmp_codename($tmp_objId);
            }
        $objList->release();
        if ($show_debug) {  echo "<br>temp object set populated"; }

        // This iterates through the tree and assigns each object the
        // children objects that belong to it
        $webSet = $cn->newQry("webSet", $qry_webBuilder);
            while ($row = $webSet->getRow()) {
		        if ($show_debug) {  echo "<br>entered webSet loop<br>"; }
                $pObj_id = $row['parent_object_id'];
                $cObj_id = $row['child_object_id'];
                $pObj    = $tmp_objSet[$pObj_id];

                if ($show_debug) {  
                    echo "<br>Set $pObj_id(".get_class($pObj).") child {$row['child_leg_name']} to $cObj_id(". get_class($tmp_objSet[$cObj_id]) .") <br><br><pre>";
                    print_r($tmp_objSet[$pObj_id]);
                    echo "</pre>"; }
                   
                $pObj->setChild($row['child_leg_name'],$tmp_objSet[$cObj_id]);
            }
        $webSet->release();
        if ($show_debug) {  echo "<br>tree built"; }

        //$cn->close();
        $this->webObj = $tmp_objSet[$this->root_id];
        if ($show_debug) {  echo "<br>webObj is of type ". get_class($this->webObj); }
    }
    public function memberCount() { return $this->cache['memberCount']; }
    public function getValueMembers() { return $this->getMembersByType($this->webObj,'MPvalue'); }
    public function getByID($member_id) { return $this->getMemberByID($this->webObj, $member_id); }
    public function printHTML() {
        $tmp = $this->webObj;
//        echo "<br>MPforuma printHTML";
        return $tmp->evaluate() . " = " . $tmp->printHTML();
    }
    //</editor-fold>

    //<editor-fold defaultstate="collapsed" desc="MP_formula Private Methods">
    private function getMembersByType($leaf_obj, $type_name) {
        //echo "<br><hr><pre>" . print_r($leaf_obj) . "</pre>";
        $tmpMembers = array();
        $childList = $leaf_obj->getChildren();
        //echo "<br><br>leaf: $leaf_obj<br>type: $type_name<br><pre>" . print_r($childList) . "</pre>";
        //echo "<br><br>leaf: $leaf_obj<br>type: $type_name<br>kids: " . (!is_null($childList) ? "true" : "false");
        if (!is_null($childList)) {
            foreach($childList as $key => $value) {
                if($type_name == get_class($value)) {
                    $tmpMembers[$value->getID()] = $value;

                }
                $tmpMembers = $this->getMembersByType($value, $type_name)  + $tmpMembers;
            }
        }
        return $tmpMembers;
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
        return null;
    }
    //</editor-fold>
}
?>