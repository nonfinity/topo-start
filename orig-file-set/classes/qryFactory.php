<?php
error_reporting(E_ALL ^ E_DEPRECATED);
/* *****************************************************************************
//  MySql_qryObject Library                                             Mike Poe
//
//  --== DESCRIPTION ==--
//  This library is set up to simplify interaction with a mysql database and the
//  resource objects that are returned from it. Yes it is totally possible to do
//  anything in here without this library, the idea is to bring it into a single
//  location for ease of use.
//
//  --== MEMBER LIST ==--
//  qryFactory      :   A connecion class that generates qryObject recordsets
//  qryObject       :   A recordset resource with most functionality built-in
//
//  --== To Do List ==--
//  - Create a clone method for qryFactory and qryObject
//  - - create serialize and deserialize functions for qryFactory and qryObject
//  - Add column count method to qryObject
//  - Is there a way to leave qryFactory cn_link property private or protected?
//  - - can an abstract class or interface be used to create hierarchy?
//  - build formatted output functions
//  - - allow ability to provide output format as a method argument
//  - - separate from __toString() magic method
//
//  MySql_qryObject Library                                             Mike Poe
// ****************************************************************************/

// <editor-fold defaultstate="collapsed" desc="qryFactory Header Comments">*****
//  qryFactory Class                                                    Mike Poe
//
//  --== DESCRIPTION ==--
//  This class is designed to simplify MySql database queries by bundling
//  all the relevant workings into a single factory and object class.
//  This is the Factory class that generates qryObj objects based on sql input
//
//  --== EDIT LOG ==--
//  2011-02-11 : Creation Start Date
//  2011-02-12 : First working version completed
//
//  qryFactory Class                                                    Mike Poe
// ***************************************************************</editor-fold>

/**
 * Description of qryFactory
 *
 * @author Nonfinity
 */
    class qryFactory {
        // PRIVATE VARIABLES (Everything, unless explicitly needed otherwise)
        //<editor-fold defaultstate="collapsed" desc="qryFactory Property Declarations">
        private $server = "";
        private $db_name = "";
        private $uname = "";
        private $pword = "";
        private $client_flags = 0;
        private $new_link = false;
        private $persistent = false;
        private $is_open = false;
        private $children = array();
        private $cn_link;
        //</editor-fold>

        // PROTECTED VARIABLES (With reason for not being private)
        //protected $cn_link;     // Needs to be accessed by child qryObject's
                                  // but qryObject is NOT A CHILD!

        // The Collection of Magic Methods used
        public function __construct($server_addr, $db, $user, $pass,
                                    $new_links = false, $flags = 0, $persist = false) {

            $this->server = $server_addr;
            $this->db_name = $db;
            $this->uname = $user;
            $this->pword = $pass;
            $this->new_link = $new_links;
            $this->client_flags = $flags;
            $this->persistent = $persist;

            $this->test();
        }
        public function __destruct() {
            foreach ($this->children as $key => $value) {
                //echo "<br>" . $value->name;
                $value->release();
            }
            $this->close();
        }
        public function __sleep() {
            return array('server','db_name','uname','pword','new_link','client_flag','persistent');
        }
        public function __wakeup() {
            $this->open();
        }
        public function __get($name) {
            return array_key_exists($name, get_class_vars(get_class($this))) ? $this->$name : null;
        }

        // Custom Functions used for this Class
        public function test() {
            if ($this->is_open != true) {  // check it once
                $this->open();

               if ($this->is_open != true) { // check it again (2 fails and you're out!)
                   $err_str = "Error in Connecting to mysql database(". $this->server ."): " .
                              mysql_error($this->cn_link) ."(". mysql_errno($this->cn_link) .")";

                   trigger_error(htmlentities($err_str), E_USER_ERROR);
               }
               else {
                   $this->close();
               }
           }
        }
        public function open() {
            try {
                if ($this->persistent) {
                    $this->cn_link = mysql_pconnect($this->server,$this->uname,$this->pword,$this->client_flags);
                }
                else {
                    $this->cn_link = mysql_connect($this->server,$this->uname,$this->pword,$this->client_flags);
                }

                mysql_select_db($this->db_name, $this->cn_link);
                $this->is_open = TRUE;
            }
            catch (Exception $e) {
                trigger_error(htmlentities($e->getMessage()), E_USER_ERROR);
            }
        }
        public function close() {
            if ($this->is_open) {
             //   if (mysql_ping($this->cn_link)) { mysql_close($this->cn_link); }
                $this->is_open = FALSE;
            }
        }
        public function newQry($qryName, $sql, $use_buffer = TRUE) {
            $tmpQry = new qryObject($this, $qryName, $sql, $use_buffer);

            $this->children[] = $tmpQry;

            return $tmpQry;
        }
        public function getChildByName($name) {
            foreach ($this->children as $key) {
                if ($this->children[$key]->name == $name) {
                    return $this->children[$key];
                }
            }
        }
        public function getLink() { return $this->cn_link; }
    }
?>