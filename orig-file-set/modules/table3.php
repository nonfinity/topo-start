<?php
    $server = "localhost";
	$db     = "calc_engine2";
	$user   = "php_user";
	$pass   = "php_user";

    if(empty($_GET)) { ?>
        <form action="modules/new_graph.php" method="post">
        Enter name for new graph:
        <input type="text"   name="graph_name">
        <input type="hidden" name="source_loc" value="<?php echo $homefile ?>">
        <input type="submit"                   value="Submit">
        </form>
    <?php 
    } else { 
        $qry_list = "SELECT (g.root_node_id = n.node_id) as is_root, "
                  . "       n.node_id, n.text_name as node_name, "
                  . "       nt.nodeType_id, nt.text_name as type_name, " 
                  . "       ns.nodeSocket_id, ns.text_name as socket_name"
                  . "  FROM graphs g INNER JOIN nodes       n  on g.graph_id = n.graph_id "
                  . "                INNER JOIN nodeTypes   nt on n.nodeType_id = nt.nodeType_id"
                  . "                LEFT  JOIN nodeSockets ns on nt.nodeType_id = ns.nodeType_id"
                  . " WHERE g.graph_id = " . $_GET['fxs'];
        $qry_node = "SELECT (g.root_node_id = n.node_id) as is_root, n.* "
                  . "  FROM nodes n INNER JOIN graphs g ON g.graph_id = n.graph_id "
                  . " WHERE g.graph_id = " . $_GET['fxs'];
        $qry_sockets  = "SELECT n.node_id, n.text_name as node_name, "
                      . "       nt.nodeType_id, nt.text_name as type_name, " 
                      . "       ns.nodeSocket_id, ns.text_name as socket_name"
                      . "  FROM nodes n INNER JOIN nodeTypes   nt on n.nodeType_id = nt.nodeType_id"
                      . "               LEFT  JOIN nodeSockets ns on nt.nodeType_id = ns.nodeType_id"
                      . " WHERE n.node_id = :node_id";
    ?>
    <table border="1">
        <tr>
            <th>Root</th>
            <th>Node Type</th>
            <th>Node Name</th>
            <th>Socket</th>
            <th>Child Node</th>
        </tr>
        
        <?php
            try {
                $cn = new PDO("mysql:host=$server;dbname=$db", $user, $pass);
                $cn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $nodes = $cn->prepare($qry_list); 
                $nodes->execute();
                
                while ($row = $nodes->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr><td>" . ($row['is_root'] ? "root" : "") . "</td>"
                       . "<td>" . $row['type_name'] . "</td><td>" . $row['node_name'] . "</td>"
                       . "<td>" . $row['socket_name'] . "</td><td>no child name</td></tr>";
                }
            }
            catch(PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
            $cn = null;
        ?>
    </table>
    <?php } ?>
