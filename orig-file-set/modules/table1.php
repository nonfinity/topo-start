<?php
	$x = $_GET['fxs'];
	$a = new MPformula($x);
	$y = $a->getValueMembers();
    
	$get_empty = empty($_GET);

	if (!$get_empty) {
	    foreach ($_GET as $key => $value) {
	        $tmp = $a->getByID($key);
			//echo "<br>$tmp";
			if (!empty($tmp)) { $tmp->updateValue($value); }
        }
    }

    //$action_line = "http://www.mwpoe.com/old_test/" . $homefile;
    $action_line = "http://localhost/site1/" . $homefile;
?>
<form action="<?php echo $action_line; ?>" method="get"><input type="hidden" name="fxs" value="<?php echo $_GET['fxs']; ?>"><table border="1">
<?php
    foreach ($y as $key => $value) {
        echo "\n<tr>";
    	echo "\n\t<td><label for=\"$key\">Value for $key</td>";
    	echo "\n\t<td><input type=\"text\" name=\"$key\" value=\"".$value->evaluate()."\"></td>";
    	echo "\n</tr>";
    }
?>
<tr>
    <td><input type="submit" value="Go!"> <input type="reset"></td>
    <td>Result:
        <?php
            if(empty($_GET)) {
                echo "null";
            } else {
                echo $a->evaluate();
            }
        ?>
    </td>
</tr>
<tr>
    <td colspan="2"><?php echo $a->printHTML(); ?></td>
</tr>
</table>
</form>