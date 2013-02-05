<?php
	include('../branding.php');
	include('../functions.php');
	get_header();
	get_branding("Staff In/Out");
?>
	<script src="http://code.jquery.com/jquery-1.8.3.min.js"></script>
        <script src="../js/people.js"></script>
        <script src="../js/staff_status.js"></script>
	<div class="ssbox">
		<div class="titlediv" align="center">
			<h1 class="boxtitle">ODI Staff / Associates</h1>
		</div>
<?php
	$handle = fopen('staff.csv','r');
	while ($line = fgets($handle)) {
		$parts = explode(",",$line);
		$key_string = trim($parts[0]) . trim($parts[1]) . trim($parts[2]);
        	$key = md5($key_string);
		echo '<div id="' . $key . '" class="person" style="opacity: 1;">';
		echo '<a href="../individual/?id='.$key.'"><img class="people_pic" src="stock/'.trim($parts[2]).'.jpg"/></a>';
		echo $parts[0] . ' ' . $parts[1];
		echo '<br/>';
		if (signed_in($key)) {
			echo '<button value="'.$key.'" class="checkout" id="checkout_'.$key.'">Check Out</button>';
			echo '<button style="display: none;" value="'.$key.'" class="checkin" id="checkin_'.$key.'">Check In</button>';
		} else {
			echo '<button value="'.$key.'" class="checkin" id="checkin_'.$key.'">Check In</button>';
			echo '<button style="display: none;" value="'.$key.'" class="checkout" id="checkout_'.$key.'">Check Out</button>';
		}
		echo '</div>';
	}	
?>
	</div>
<?php
	get_footers();
?>
