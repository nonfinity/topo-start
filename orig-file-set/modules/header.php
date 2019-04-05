<?php

//	$docroot=$_SERVER['DOCUMENT_ROOT'] . implode('/',array_slice(explode('/',$_SERVER['PHP_SELF']),0,-1)) . '/';
	$docroot=getcwd() . '\\';
	$homefile = implode('\\',array_slice(explode('/',$_SERVER['PHP_SELF']),-1,1));

	//if this page is loaded without a formula, reload it with the default formula of fxs=1
	if (empty($_GET) /*|| $_GET['fxs'] == 0*/) {
		header("Location: ./$homefile?fxs=1");
	}

    // Use the includer for debugging and autoloader for actual use
    // The autoloader will allow sloppy errors to slip through unhandled and unannounced
    if (true)
    {
        //autoloader file
        require($docroot . 'includes/autoloader.php'); //'library/autoload/autoloader.php'
        autoloader::init();
    } else {
    	// manual includer
        require $docroot . 'includes/includer.php';
    }
    error_reporting(E_ALL );

?>