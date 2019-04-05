<?php
/*
 * Created on Feb 5, 2012
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 	include_once './modules/db_conn.php';

 	$qry_webList = "SELECT fx_web_id, web_descr" .
 			   " FROM fx_web" .
               " WHERE root_object <> 0" .
 			   " LIMIT 0,30";

	$cn = new qryFactory($server_addr, $db, $user, $pass);
	$cn->open();

	$weblist = $cn->newQry("webList", $qry_webList);
?>
	Available Webs
	<ul><?php
		while ($row = $weblist->getRow()) {
			$tmp_id  = $row['fx_web_id'];
			$tmp_desc = $row['web_descr'];

			echo "\n" . '<li><a href="' . $homefile . '?fxs=' . $tmp_id . '">' . $tmp_desc . '</a></li>';
			//echo '\n<li><a href="' .$homefile . "?fxs=" . $tmp_id . "\">" . $tmp_desc . "</a></li>";
		}
	?></ul>