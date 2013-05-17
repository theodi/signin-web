<?php
	include('branding.php');
	get_header();
	get_branding("Today's Visitors");
	$categories['visitor']['id'] = 'allonsite';
	$categories['visitor']['name'] = 'All On-Site Visitors';
#	$categories['startup']['id'] = 'startups';
#	$categories['startup']['name'] = 'Start-Up Members';
	$categories['staff']['id'] = 'staff';
	$categories['staff']['name'] = 'ODI Staff / Associates';
?>
	<script src="http://code.jquery.com/jquery-1.8.3.min.js"></script>
        <script src="js/people.js"></script>
<?php
	foreach($categories as $key => $values) {
		$div_id = $values['id'];
		$title = $values['name'];
		echo "\t" . '<div id="'.$div_id.'" class="peoplebox display_'.$div_id.'">' . "\n";
		echo "\t\t" . '<div class="titlediv" align="center">' . "\n";
		echo "\t\t\t" . '<h1 class="boxtitle">' . $title . '</h1>' . "\n";
		echo "\t\t" . '</div>' . "\n";
		if ($key == "staff") {
			preload_staff();
		}
		echo "\t" . '</div>' . "\n";
	}
/*
	<div id="allonsite" class="peoplebox allonsite">
		<div class="titlediv" align="center">
			<h1 class="boxtitle">All On-Site Visitors</h1>
		</div>
	</div>
	<div id="staff" class="peoplebox staffdiv">
		<div class="titlediv" align="center">
			<h1 class="boxtitle">ODI Staff / Associates</h1>
		</div>
*/
?>
<?php

function preload_staff() {
	$handle = fopen('staff/staff.csv','r');
	while ($line = fgets($handle)) {
		$parts = explode(",",$line);
		$key_string = trim($parts[0]) . trim($parts[1]) . trim($parts[2]);
        	$key = md5($key_string);
		echo '<div id="' . $key . '" class="person">';
		echo '<img class="people_pic" src="staff/stock/'.trim($parts[2]).'.jpg"/>';
		echo $parts[0] . ' ' . $parts[1];
		echo '</div>';
	}	
}
	get_footers();
?>