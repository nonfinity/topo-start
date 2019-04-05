<?php
/*
 * Created on Feb 5, 2012
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 	$server_addr     = "localhost";
    $db              = "calc_engine2";
    $user            = "php_user";
    $pass            = "php_user";

 	$qry_list = "SELECT graph_id, description" .
                " FROM graphs" .
                " WHERE root_node_id <> 0" .
                " LIMIT 0,30";

	$cn = new qryFactory($server_addr, $db, $user, $pass);
	$cn->open();

	$weblist = $cn->newQry("webList", $qry_list);
?>
	Available Code Graphs
	<ul><?php
		while ($row = $weblist->getRow()) {
			$tmp_id  = $row['graph_id'];
			$tmp_desc = $row['description'];

			echo "\n" . '<li><a href="' . $homefile . '?fxs=' . $tmp_id . '">' . $tmp_desc . '</a></li>';
			//echo '\n<li><a href="' .$homefile . "?fxs=" . $tmp_id . "\">" . $tmp_desc . "</a></li>";
		}
	?></ul>