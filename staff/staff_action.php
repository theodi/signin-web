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
$handle = fopen('/tmp/logfile.txt','w');
	
		$id = $_POST['keycard_id'];
fwrite($handle,"Got keycard ID $id\n");
		
		$statusCode = register_keycard($id);
fwrite($handle,"Got Status Code $statusCode\n");

		$status_codes = array (
			200 => 'OK',
			// Signed In
		        201 => 'Created',
			// New Reocrd
			202 => 'Accepted',
			203 => 'Non-Authoritative Information',
			// Signed Out
			204 => 'No Content',
			205 => 'Reset Content',
			206 => 'Partial Content',
			400 => 'Bad Request',
			404 => 'Not Found',
			// Something went wrong
			500 => 'Internal Server Error'
		);
		if ($statusCode == null) {
			$statusCode = 500;
		}

		$status_string = $statusCode . ' ' . $status_codes[$statusCode];
		header($_SERVER['SERVER_PROTOCOL'] . ' ' . $status_string, true, $statusCode);
fclose($handle);
	
	}

	if ($action == "associate_keycard") {
		$person_id = $_POST['person_id'];
		$keycard_id = $_POST['keycard_id'];
		if (associate_keycard($person_id,$keycard_id)) {
			echo "Successfully Associated!";
		} else {
			header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
			echo "Something went wrong, moan at the techies!";
		}
	}
	
	if ($action == "role") {
		$role = $_POST['role'];
		return update_role($id,$role);
	}	


?>
