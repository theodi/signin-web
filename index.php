<?php
	include('branding.php');
	get_header();
	get_branding("Today's Visitors");
?>
	<script src="http://code.jquery.com/jquery-1.8.3.min.js"></script>
        <script src="js/people.js"></script>
	<div id="allonsite" class="peoplebox allonsite">
		<div class="titlediv" align="center">
			<h1 class="boxtitle">All On-Site Visitors</h1>
		</div>
	</div>
	<div class="peoplebox staffdiv">
		<div class="titlediv" align="center">
			<h1 class="boxtitle">ODI Staff / Associates</h1>
		</div>
<?php
	$handle = fopen('staff.csv','r');
	while ($line = fgets($handle)) {
		$parts = explode(",",$line);
		$key_string = trim($parts[0]) . trim($parts[1]) . trim($parts[2]);
        	$key = md5($key_string);
		echo '<div id="' . $key . '" class="person">';
		echo '<img class="people_pic" src="stock/'.trim($parts[2]).'.jpg"/>';
		echo $parts[0] . ' ' . $parts[1];
		echo '</div>';
	}	
?>
	</div>
<?php
	get_footers();
?>
