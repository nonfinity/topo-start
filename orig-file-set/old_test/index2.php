<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->
<!doctype html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<link rel="STYLESHEET" type="text/css" href="index2.css">
        <title></title>
	</head>

    <body>
		<div id="testing123"></div>
		<div class="MPobjectBox" id="MPobjectBox">
			<div class="MPobjectLeftBox" id="MPobjectLeftBox">
				<div class="MPobjectLeftBoxArrows MPhidden" id="MPobjectLeftBoxArrows">
					<img src="objIcons/arrow_up.png" class="up">
					<img src="objIcons/arrow_down.png" class="down">
				</div>
				<div class="MPobjectLeftBoxInput MPhidden" id="MPobjectLeftBoxInput">
					<input type="Text" class="MPobjectLeftBoxInputBox" id="MPobjectLeftBoxInputBox">
				</div>
				<!-- either number input or up arrow and down arrow images here -->
			</div>

			<input class="MPobjectName" id="MPobjectName" value="Object Name" disabled onblur="MPobjectNameBlur();">

			<img src="objIcons/pencil.png" class="MPobjectNameEditIcon" id="MPobjectNameEditIcon" onclick="pencilClick();">

			<select class="MPcombo" id="MPcombo" title="Select Object Type" onchange="MPcomboChange();">
				<option class="MPcomboDefault" id="MPcomboDefault" value="default" selected>Select Object Type</option>
				<option class="MPcomboItem" value="MPvalue">Single Value</option>
				<option class="MPcomboItem" value="MPadd">Addition</option>
				<option class="MPcomboItem" value="MPminus">Subtraction</option>
				<option class="MPcomboItem" value="MPdivide">Division</option>
				<option class="MPcomboItem" value="MPmultiply">Multiplication</option>
			</select>

			<div class="MPobjectStatusBar" id="MPobjectStatusBar">&nbsp;
				<!-- some images here -->
			</div>
		</div>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.0/jquery.min.js" type="text/javascript"></script>
	<script src="index2.js" type="text/javascript"></script>
    </body>
</html>

<?php
	function LeftBoxPopulate($combo_value) {
		if ($combo_value == 'Single Value') {
			echo 'jerky herky';
		} else {
			echo "More<br>Less";
		}
	}
?>