<?php
/*
 * Created on Feb 7, 2012
 *
 * This is a module that will return a list of all the .php file in the home directory
 * Mainly to help navigate around the various test files now in existence
 */

 	$filelist = scandir('./');

 	echo "List of Files\n<ul>";
 	foreach ($filelist as $key => $value) {
		if (substr($value,strlen($value)-4) == '.php') {
			echo "\n<li><a href=\"$value\">$value</a></li>";
		}
	}
	echo "</ul>";
?>
