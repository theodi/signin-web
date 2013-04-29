<?php

	$id = $_POST['id'];
	$action = $_POST['action'];
	
	require('../functions.php');
	
	if ($action == "checkin") {
		sign_in($id);				
	}
	if ($action == "checkout") {
		sign_out($id);				
	}
	if ($action == "keycard") {
		register_keycard($id);
	}
	if ($action == "associate_keycard") {
		$person_id = $_POST['person_id'];
		$keycard_id = $_POST['keycard_id'];
		associate_keycard($person_id,$keycard_id);
	}

?>
