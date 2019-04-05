<?php
	$get_empty = empty($_GET);
	if ($get_empty) {
		header('Location: ./index4.php?fxs=1');
	} else if ($_GET['fxs'] == 0) {
		header('Location: ./index4.php?fxs=1');
	}
    // Use the includer for debugging and autoloader for actual use
    // The autoloader will allow sloppy errors to slip through unhandled
    if (true)
    {
        $path='../';
        $docroot=$_SERVER['DOCUMENT_ROOT'] . implode('/',array_slice(explode('/',$_SERVER['PHP_SELF']),0,-2)) . '/';
        require($docroot . 'includes/autoloader.php'); //'library/autoload/autoloader.php'
        autoloader::init();
    } else {
        require $_SERVER['DOCUMENT_ROOT'].'/calc_engine/includes/includer.php';
    }
    error_reporting(E_ALL );
?>
<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->
<!doctype html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <?php
            $x = $_GET['fxs'];
            $a = new MPformula($x);
            $y = $a->getValueMembers();

//            $get_empty = empty($_GET);

            if (!$get_empty) {
                foreach ($_GET as $key => $value) {
                    $tmp = $a->getByID($key);
					echo "<br>$tmp";
					if (!empty($tmp)) { $tmp->updateValue($value); }
                }
            }
        ?>

        <form action="http://www.mwpoe.com/old_test/index4.php" method="get">
		<input type="hidden" name="fxs" value="<?php echo $_GET['fxs']; ?>">
        <table border="1">
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
		<p>You can try with arguments <a href="http://www.mwpoe.com/old_test/index4.php?fxs=1">fxs=1</a>
			and <a href="http://www.mwpoe.com/old_test/index4.php?fxs=2">fxs=2</a>
			and <a href="http://www.mwpoe.com/old_test/index4.php?fxs=3">fxs=3</a> !! Get crazy with it!</p>
		<p>Doc link over at: <a href="https://sites.google.com/site/poemike/home">Google Sites</a></p>
    </body>
</html>