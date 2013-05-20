<?php
	include('branding.php');
	get_header();
	get_branding("Today's Visitors");
	$categories['visitor']['id'] = 'visitor';
	$categories['visitor']['name'] = 'All On-Site Visitors';
	$categories['startup']['id'] = 'startup';
	$categories['startup']['name'] = 'Start-Up Members';
	$categories['staff']['id'] = 'staff';
	$categories['staff']['name'] = 'ODI Staff / Associates';
?>
	<script src="http://code.jquery.com/jquery-1.8.3.min.js"></script>
        <script src="js/people.js"></script>
	<div align="center">
	<nav id="categories">
	<ul>
<?php
	$count = 0;
	foreach($categories as $key => $values) {
		$div_id = $values['id'] . "_nav";
		$title = $values['name'];
		if ($count == 0) {
			echo "\t\t" . '<li id="'.$div_id.'" class="selected">' . $title .'</li>' . "\n";
		} else {
			echo "\t\t" . '<li id="'.$div_id.'">' . $title .'</li>' . "\n";
		}
		$count++;
	}
?>
	</ul>
	</nav>
<?php
	$count = 0;
	foreach($categories as $key => $values) {
		$div_id = $values['id'];
		$title = $values['name'];
		if ($count == 0) {
			echo "\t" . '<div id="'.$div_id.'" class="peoplebox normalbox">' . "\n";
		} else {
			echo "\t" . '<div id="'.$div_id.'" class="peoplebox normalbox" style="display: none;">' . "\n";
		}
		echo "\t\t" . '<div class="titlediv" align="center">' . "\n";
		echo "\t\t\t" . '<h1 class="boxtitle">' . $title . '</h1>' . "\n";
		echo "\t\t" . '</div>' . "\n";
		if ($key == "staff") {
			preload_staff();
		}
		echo "\t" . '</div>' . "\n";
		$count++;
	}
?>
</div>
<?php

function preload_staff() {
	$handle = fopen('staff/staff.csv','r');
	while ($line = fgets($handle)) {
		$parts = explode(",",$line);
		$key_string = trim($parts[0]) . trim($parts[1]) . trim($parts[2]);
        	$key = md5($key_string);
		echo '<div id="' . $key . '" class="person">';
		echo '<a href="individual/?id='.$key.'"><img class="people_pic" src="staff/stock/'.trim($parts[2]).'.jpg"/></a>';
		echo $parts[0] . ' ' . $parts[1];
		echo '</div>';
	}	
}
	get_footers();
?>
