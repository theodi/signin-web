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

?>
