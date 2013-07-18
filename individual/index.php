<?php
	include('../branding.php');
	include('../functions.php');
	get_header();
	get_branding("Ops Menu");

	$roles[] = 'staff';
	$roles[] = 'startup';
	$roles[] = 'visitor';

if (file_exists('layout.css')) {
	echo '<link rel="stylesheet" type="text/css" href="layout.css">';
} else {
	echo '<link rel="stylesheet" type="text/css" href="../layout.css">';
}
?>
	<script src="http://code.jquery.com/jquery-1.8.3.min.js"></script>
	<script type="text/javascript">
		page = "individual";
	</script>
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
	$query = "select role from people_roles where person_id='$id' order by valid_from desc limit 1";
	$res = $mysqli->query($query);
	$row2 = $res->fetch_row();
	$role = $row2[0];
	if ($role == "") {
		$role = "visitor";
	}
?>
	<div style="width: 100%; line-height: 50px; margin-top: 2em;" align="center">
<?php
	echo '<span style="font-size: 6em;">';
	echo $row[0];
	echo " " . $row[1] . '</span><br/>';
	if (file_exists('../staff/stock/'.trim($row[2]).'.jpg')) {
		echo '<a href="?id='.$key.'"><img class="people_pic" style="width: 300px; height: 300px;" src="../staff/stock/'.trim($row[2]).'.jpg"/></a><br/>';
	} else {
		echo '<a href="?id='.$key.'"><img class="people_pic" style="width: 300px; height: 300px;" src="../photo.php?id='.$id.'"/></a><br/>';
	}
	echo '<input type="hidden" id="person_id" value="'.$key.'"></input>';
	echo '<section id="ops" class="ops">';
	echo '<section id="in_out" class="ops_box_left ops_box ops_box_top">';
	echo '<h2>Check In/Out</h2>';
	if (signed_in($key)) {
		echo '<button style="font-size: 1.5em; height: 1.6em;" value="'.$key.'" class="checkout" id="checkout_'.$key.'">Check Out</button>';
		echo '<button style="font-size: 1.5em; height: 1.6em; display: none;" value="'.$key.'" class="checkin" id="checkin_'.$key.'">Check In</button>';
	} else {
		echo '<button style="font-size: 1.5em; height: 1.6em;" value="'.$key.'" class="checkin" id="checkin_'.$key.'">Check In</button>';
		echo '<button style="font-size: 1.5em; height: 1.6em; display: none;" value="'.$key.'" class="checkout" id="checkout_'.$key.'">Check Out</button>';
	}
	echo '</section>';
?>
	<section id="keycards" class="ops_box_right ops_box ops_box_top">
		<h2>Associate RFID Card</h2>
		<div id="add_card">
			<button style="font-size: 1.5em; height: 1.6em" id="associate_button" onclick="read_card_1();">Associate New Card</button>
		</div>
		<div style="font-size: 1.5em; display: none;" id="new_card">
			Please put card on the reader
		</div>
	</section>
	<section id="roles" class="ops_box ops_box_left">
		<h2>Role</h2>
		<table id="roles" class="roles">
			<tr id="role_type" class="role_type"><td>Staff</td><td>Start-Up</td><td>Visitor</td></tr>
			<tr id="role_select" class="role_select">
<?php
	for ($i=0;$i<count($roles);$i++) {
		echo '<td id="role_'.$roles[$i].'" ';
		echo 'class="';
		if ($role == $roles[$i]) {
			echo 'role_selected ';
		}
		if (($i % 2) == 0) {
			echo 'role_select_middle';
		}
		echo '"></td>';
	}
?>
			</tr>			
		</table>
	</section>
	<section id="desk" class="ops_box ops_box_right">
		<h2>Desk Assignment</h2>
	</section>
	</section>
	<script>
	
$(document).ready(function() {
	$('body').css('margin','0');
	$('body').css('min-width','800px');
});	
</script>
	</div>
