<?php
    // Use the includer for debugging and autoloader for actual use
    // The autoloader will allow sloppy errors to slip through unhandled
    if (true)
    {
        $path='../';
//        $docroot=$_SERVER['DOCUMENT_ROOT'] . implode('/',array_slice(explode('/',$_SERVER['PHP_SELF']),0,-2)) . '/';
//        $docroot=$_SERVER['DOCUMENT_ROOT'] . implode('/',array_slice(explode('/',$_SERVER['PHP_SELF']),0,-2)) . '/';
        $docroot = getcwd() . '\\';
        include($docroot . 'includes\autoloader.php'); //'library/autoload/autoloader.php'
        autoloader::init();
    } else {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includer.php';
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
            $x = 1;
            $a = new MPformula($x);
            echo "<hr><hr>Begin Evaluate<br>$x : ".$a->evaluate();

            $y = $a->getValueMembers();
//            foreach ($y as $key => $value) {
//                echo "<br>Object[".$value->getID()."] using key '$key' evaluates to ".$value->evaluate();
//            }
            echo "<br>Object[3] is_dirty = ". $y[3]->getDirty();
            $y[3]->updateValue(1984);
            echo "<br>Object[3] valued updated to ". $y[3]->evaluate();
            echo "<br>Object[3] is_dirty = ". $y[3]->getDirty();

            $y[4]->updateValue(1983);
            echo "<br>Object[3] valued updated to ". $y[3]->evaluate();

//            foreach ($y as $key => $value) {
//                echo "<br>Object[".$value->getID()."] using key '$key' evaluates to ".$value->evaluate();
//            }

            $a->evaluate();
            echo "<br>After Set Evaluate: ".$a->printHTML();
            $a->evaluate(true);
            echo "<br>Now EvaluateForced: ".$a->printHTML();
        ?>
    </body>
</html>