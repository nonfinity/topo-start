<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of autoloader
 *
 * @author Nonfinity
 */
class autoloader {
    public static $instance;
    private $_src=array('includes/', 'classes/', 'classes-WB/'); //array of folders to check
    private $_ext=array('.php'); //array of file extensions

    /* initialize the autoloader class */
    public static function init(){
        if(self::$instance==NULL){
            self::$instance=new self();
        }
        return self::$instance;
    }

    /* put the custom functions in the autoload register when the class is initialized */
    private function __construct(){
        spl_autoload_register(array($this, 'clean'));
        spl_autoload_register(array($this, 'dirty'));
    }

    /* the clean method to autoload the class without any includes, works in most cases */
    private function clean($class){
        global $docroot;
        $class=str_replace('_', '/', $class);
        spl_autoload_extensions(implode(',', $this->_ext));
        foreach($this->_src as $resource){
            set_include_path($docroot . $resource);
            spl_autoload($class);
        }
    }

    /* the dirty method to autoload the class after including the php file containing the class */
    private function dirty($class){
        global $docroot;
        $class=str_replace('_', '/', $class);
        foreach($this->_src as $resource){
            foreach($this->_ext as $ext){
                @include($docroot . $resource . $class . $ext);
            }
        }
        spl_autoload($class);
    }

}
?>