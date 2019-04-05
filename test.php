<?php
/*
 * Created on Feb 8, 2012
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

//	$t1 = array(array('source' => 5,'code' => 'ERR-001','message' => 'Unspecified error source'));
//	$t2 = array(array('source' => 7,'code' => 'ERR-002','message' => 'Otherwise unspecified error code'));
//	$t3 = array_merge($t1, $t2);

//	var_dump($t3);



echo "<BR><BR>" . $_SERVER['DOCUMENT_ROOT'] . implode('/',array_slice(explode('/',$_SERVER['PHP_SELF']),0,-1)) . '/';
echo "<BR><BR>" . getcwd() . implode('/',array_slice(explode('/',$_SERVER['PHP_SELF']),0,-1)) . '/';
echo "<BR><BR>" . getcwd();
echo "<BR><BR>" . 'localhost' . implode('/',array_slice(explode('/',$_SERVER['PHP_SELF']),0,-1)) . '/';


//echo "<BR><BR>" . implode('/',array_slice(explode('/',$_SERVER['PHP_SELF']),-1,1));
//echo "<BR><BR>" . $_SERVER['PHP_SELF'];

?>
