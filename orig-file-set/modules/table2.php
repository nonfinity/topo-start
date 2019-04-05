<?php
	$x = $_GET['fxs'];
	$a = new graphRoot($x);
	$y = $a->getValueMembers();
    
	$get_empty = empty($_GET);

	if (!$get_empty) {
	    foreach ($_GET as $key => $value) {
	        if ($key != 'fxs') {
                $tmp = $a->getByID($key);
                //echo "<br>$tmp is !empty: " . (!empty($tmp) ? "yes" : "no");
                //echo "<br>" . gettype($tmp);
                //echo "<br>setValue of " . $tmp->getName() . "(" . $tmp->getID() . ") to $value";
                if (!empty($tmp)) { $tmp->setValue($value); }
            }
        }
    }

    //$action_line = "http://www.mwpoe.com/old_test/" . $homefile;
    $action_line = "http://localhost/site1/" . $homefile;
    
    //echo "<hr>y size: " .count($y)
?>
<form action="<?php echo $action_line; ?>" method="get"><input type="hidden" name="fxs" value="<?php echo $_GET['fxs']; ?>">
<table border="1">
<?php
    foreach ($y as $key => $value) {
        echo "\n<tr>";
    	echo "\n\t<td><label for=\"$key\">Value for " . $value->getName() . "</td>";
    	echo "\n\t<td><input type=\"text\" name=\"$key\" value=\"".$value->printHTML()."\"></td>";
        //echo "\n\t<td><input type=\"text\" name=\"$key\" value=\"".get_class($value)."\"></td>";
    	echo "\n</tr>";
    }
?>
<tr>
    <td></td>
    <td><div style="text-align: center">
            <input type="submit" value="Go!"> | <input type="reset">
        </div></td>
</tr>
<tr>
    <td colspan="2"><?php echo $a->printHTML(); ?></td>
</tr>
</table>
</form>

<pre>
<?php //print_r($a); ?>
</pre>