<?php
    $server_addr    = "localhost";
	$db             = "calc_engine2";
	$user           = "php_user";
	$pass           = "php_user";
    
    $sql            = "INSERT INTO graphs(description, root_node_id) values('" . $_POST['graph_name'] . "', 0)";
    
    try {
        $cn = new PDO("mysql:host=$server_addr;dbname=$db", $user, $pass);
        // set the PDO error mode to exception
        $cn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // use exec() because no results are returned
        //$cn->exec($sql);
        $lastId = $cn->lastInsertId();
        
        // clean up and go home
        $cn = null;
        header("Location: ../" . $_POST['source_loc'] . "?fxs=" . $lastId);
        }
    catch(PDOException $e)
        {
        echo $sql . "<br>" . $e->getMessage();
        $cn = null;
        }

    
?>