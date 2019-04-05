<?php

/*
 * Only use this set for debugging purposes
 * Particularly if the autoloader is suspect or just acting wonky
 */
	include_once './classes/qryObject.php';
	include_once './classes/qryFactory.php';

	include_once './classes/MPformula.php';
	include_once './classes/MPfunction.php';

	include_once './classes/MP1param.php';
	include_once './classes/MP2params.php';
	include_once './classes/MPconst.php';

	$file_paths = array('./classes/','./classes-WB');

	foreach ($file_paths as $f_key => $f_value) {
		$filelist = scandir($f_value);

		foreach ($filelist as $key => $value) {
			if (substr($value,strlen($value)-3) == 'php') {
				include_once $f_value . $value;
			} else {
				//echo "<br>$value";
			}
		}
	}
?>