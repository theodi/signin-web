<?php
	include('../branding.php');
	include('../functions.php');
	get_header();
	get_branding("Staff In/Out");
?>
	<script src="http://code.jquery.com/jquery-1.8.3.min.js"></script>
        <script src="../js/people.js"></script>
        <script src="../js/staff_status.js"></script>
	<script src="../js/keycards.js"></script>
	<script src="../js/date.js"></script>
<?php

	$id = $_GET['id'];
	if (!$id) {
		exit();
	}

	$query = "select firstname, lastname, email, company from people where id='$id';";
	$res = $mysqli->query($query);
	if (!$res) {
		exit();
	}
	$row = $res->fetch_row();
	$key = $id;
?>
	<div style="width: 100%; line-height: 50px; margin-top: 2em;" align="center">
<?php
	echo '<span style="font-size: 8em;">';
	echo $row[0];
	echo " " . $row[1] . '</span><br/><br/>';
	echo '<a href="?id='.$key.'"><img class="people_pic" style="width: 400px; height: 400px;" src="../staff/stock/'.trim($row[2]).'.jpg"/></a><br/>';
	echo '<input type="hidden" id="person_id" value="'.$key.'"></input>';
	if (signed_in($key)) {
		echo '<button style="font-size: 3em; height: 1.2em;" value="'.$key.'" class="checkout" id="checkout_'.$key.'">Check Out</button>';
		echo '<button style="font-size: 3em; height: 1.2em; display: none;" value="'.$key.'" class="checkin" id="checkin_'.$key.'">Check In</button>';
	} else {
		echo '<button style="font-size: 3em; height: 1.2em;" value="'.$key.'" class="checkin" id="checkin_'.$key.'">Check In</button>';
		echo '<button style="font-size: 3em; height: 1.2em; display: none;" value="'.$key.'" class="checkout" id="checkout_'.$key.'">Check Out</button>';
	}
?>
	<section id="keycards">
		<div id="add_card">
			<button style="font-size: 2em; height: 1.2em" id="associate_button">Associate RFID Card</button>
		</div>
		<div style="font-size: 2em;" id="new_card">
		</div>
	</section>
	<script>
	
$(document).ready(function() {
	$('body').css('margin','0');
	$('body').css('min-width','800px');
});	
</script>
	</div>
