<?php
	include './modules/header.php';
?>

<!doctype html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>WB Method Test 1 (Refactored)</title>
        <link rel=StyleSheet href="main.css" type="text/css">
    </head>
    <body>
    	<!-- This is the box for the main function -->
    	<div id="rnd_container">
			<b class="rnd_top"><b class="rnd_b1"></b><b class="rnd_b2"></b><b class="rnd_b3"></b><b class="rnd_b4"></b></b>
			<div class="rnd_content">
				<?php include './modules/table1.php'; ?>
			</div>
			<b class="rnd_bottom"><b class="rnd_b4"></b><b class="rnd_b3"></b><b class="rnd_b2"></b><b class="rnd_b1"></b></b>
		</div>

		<!-- This is the box for list of functions available to choose -->
    	<div id="rnd_container" class="weblist">
			<b class="rnd_top"><b class="rnd_b1"></b><b class="rnd_b2"></b><b class="rnd_b3"></b><b class="rnd_b4"></b></b>
			<div class="rnd_content">
				<?php include './modules/fxs_list.php'; ?>
			</div>
			<b class="rnd_bottom"><b class="rnd_b4"></b><b class="rnd_b3"></b><b class="rnd_b2"></b><b class="rnd_b1"></b></b>
		</div>

		<!-- This is the box for list of .php files (as tests) -->
		<div id="rnd_container" class="filelist">
			<b class="rnd_top"><b class="rnd_b1"></b><b class="rnd_b2"></b><b class="rnd_b3"></b><b class="rnd_b4"></b></b>
			<div class="rnd_content">
				<?php include './modules/test_list.php'; ?>
			</div>
			<b class="rnd_bottom"><b class="rnd_b4"></b><b class="rnd_b3"></b><b class="rnd_b2"></b><b class="rnd_b1"></b></b>
		</div>

    </body>
</html>