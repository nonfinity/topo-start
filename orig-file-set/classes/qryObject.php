<?php
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

/* *****************************************************************************
//  qryObject Class                                                     Mike Poe
//
//  --== DESCRIPTION ==--
//  This class is designed to simplify MySql database queries by bundling
//  all the relevant workings into a single factory and object class.
//  This is the qryObject class that is generated on demand
//
//  --== EDIT LOG ==--
//  2011-02-11 : Creation Start Date
//  2011-02-12 : First working version completed
//
//  qryObject Class                                                     Mike Poe
// ****************************************************************************/

/**
 * Description of qryObject
 *
 * @author Nonfinity
 */
    class qryObject {
        private $parent;    // qryFactory class
        private $result;    //returns a resource
        private $buffered   = FALSE;
        private $sql_in     = "";
        private $sql_out    = "";
        private $name       = "";
        private $qryType    = "";
        private $rowCount   = 0;
        private $currRow    = 0;

        // The Collection of Magic Methods used
        public function __construct($parent, $qryName, $sql, $use_buffer) {
            $this->parent   = $parent;
            $this->name     = $qryName;
            $this->buffered = $use_buffer;
            $this->sql_in   = $sql;
            $this->sql_out  = $sql;

            $this->requery();
        }
        public function __destruct() { $this->release(); }
        public function __invoke() { $this->getResult(); }
        public function __get($name) {
            $fn_name = strtolower('_get_' . $name);
            if (method_exists($this, $fn_name))
            {
                return $this->$fn_name();
            } else {
                return null;
            }
        }

        // Custom Functions used for this Class
        public function requery() {
            try {
                // FIRST: Attempt to pull in query data
                if ($this->buffered) {
                    $this->result = mysql_query($this->sql_out, $this->parent->getLink());
                } else {
                    $this->result = mysql_unbuffered_query($this->sql_out, $this->parent->$this->parent->getLink());
                }
                
                // AFTER: Begin initializing variables for a new set of data
                $this->currRow  = 0;
                $this->qryType  = strtoupper(substr($this->sql_out, 0, strpos($this->sql_out, ' ')));
                $this->rowCount = mysql_num_rows($this->result);
            }
            catch (Exception $e) {
                trigger_error(htmlentities($e->getMessage()), E_USER_ERROR);
            }
        }
        public function getRow($no_move = FALSE) {
            try {
                return mysql_fetch_assoc($this->result);
				/*
                if ($no_move) {
                    mysql_data_seek($this->result, $this->currRow);
                } else {
                    $this->currRow += 1;
                }
                */
            }
            catch (Exception $e) {
                trigger_error(htmlentities($e->getMessage()), E_USER_ERROR);
            }
        }
        public function getResult() { return $this->result; }
        public function release() {
            $worked = false;
            if ($this->qryType == "SELECT" || $this->qryType = "EXPLAIN" || $this->qryType == "DESCRIBE" || $this->qryType == "SHOW") {
//                echo "<br><br><pre>" . print_r($this->result) . "</pre>";
                if (!$this->result && !is_null($this->result)) { $worked = mysql_free_result($this->result); }
            }

            if ($worked) {
                $this->qryType  = "";
                $this->currRow  = 0;
                $this->rowCount = 0;
            }
        }

        // Private functions no one can know about
        private function jerkface($str) {
            return $str;
        }
        private function string_prep($str) {
            // This probably needs to be more robust
            return $str.'';
        }

        // ACCESSOR Functions for Tweaked Out __Get
        // set up to be all lower case
        private function _get_buffered()    { return $this->buffered; }
        private function _get_sql_in()      { return $this->sql_in; }
        private function _get_sql_out()     { return $this->sql_out; }
        private function _get_name()        { return $this->name; }
        private function _get_qrytype()     { return $this->qryType; }
        private function _get_rowcount()    { return $this->rowCount; }
        private function _get_currRow()     { return $this->currRow; }
    }
?>