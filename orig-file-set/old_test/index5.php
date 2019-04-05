<?php
	include './modules/header.php';
?>

<!doctype html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Test 5 (Begin orthogonality)</title>
    </head>
    <style type="text/css">
		#rnd_container {
			background: #DFD5B9;
			margin:1px;
			width: 300px;
		}

    	.rnd_top, .rnd_bottom {display:block; background:#DFD5B9; font-size:1px;}
    	.rnd_b1, .rnd_b2, .rnd_b3, .rnd_b4 {display:block; overflow:hidden;}
    	.rnd_b1, .rnd_b2, .rnd_b3 {height:1px;}
    	.rnd_b2, .rnd_b3, .rnd_b4 {background:#FFFFFF; border-left:1px solid #CCCCCC; border-right:1px solid #CCCCCC;}
    	.rnd_b1 {margin:0 5px; background:#CCCCCC;}
    	.rnd_b2 {margin:0 3px; border-width:0 2px;}
    	.rnd_b3 {margin:0 2px;}
    	.rnd_b4 {height:2px; margin:0 1px;}

    	.rnd_content {
    		display:block;
    		border:0 solid #CCCCCC;
    		border-width:0 1px;
    		padding: 4px;
    		background:#FFFFFF;
    		color:#000000;
		}

		.weblist {
			position: absolute;
			top: 0px;
			right: 0px;
		}
	</style>

    <body>
    	<div id="rnd_container">
			<b class="rnd_top"><b class="rnd_b1"></b><b class="rnd_b2"></b><b class="rnd_b3"></b><b class="rnd_b4"></b></b>
			<div class="rnd_content">
				<?php include './modules/table1.php'; ?>
			</div>
			<b class="rnd_bottom"><b class="rnd_b4"></b><b class="rnd_b3"></b><b class="rnd_b2"></b><b class="rnd_b1"></b></b>
		</div>

    	<div id="rnd_container" class="weblist">
			<b class="rnd_top"><b class="rnd_b1"></b><b class="rnd_b2"></b><b class="rnd_b3"></b><b class="rnd_b4"></b></b>
			<div class="rnd_content">
				<?php include './modules/fxs_list.php'; ?>
			</div>
			<b class="rnd_bottom"><b class="rnd_b4"></b><b class="rnd_b3"></b><b class="rnd_b2"></b><b class="rnd_b1"></b></b>
		</div>
    </body>
</html>