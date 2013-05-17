<?php

	include('database_connector.php');
	
	date_default_timezone_set('UTC');

	$yesterday = date("Y-m-d",time() - 86400);
	$yesterday = $yesterday . "T23:59:59Z";
	
	$query = "update in_out set checkout='$yesterday' where checkin<'$yesterday' and checkout='';";
	$mysqli->query($query);

function signed_in($id) {
	global $mysqli;
	$query = "select * from in_out where id='$id' and checkout='';";
	$res = $mysqli->query($query);
	if ($res->num_rows > 0) {
		return true;
	} 
	return false;
}

function sign_in($id) {
	global $mysqli;
	$query = "select * from in_out where id='$id' and checkout='';";
	$res = $mysqli->query($query);
	if ($res->num_rows > 0) {
		return false;
	} 
	$date = date("Y-m-d",time());
	$time = date("H:i:s",time());
	$date_string = $date . 'T' . $time . 'Z';
	$query = "insert into in_out set id='$id', checkin='$date_string', checkout='';";
	$res = $mysqli->query($query);
	return $res; 
}
function sign_out($id) {
	global $mysqli;
	$query = "select * from in_out where id='$id' and checkout='';";
	$res = $mysqli->query($query);
	if ($res->num_rows < 1) {
		return false;
	} 
	$date = date("Y-m-d",time());
	$time = date("H:i:s",time());
	$date_string = $date . 'T' . $time . 'Z';
	$query = "update in_out set checkout='$date_string' where id='$id' and checkout='';";
	$res = $mysqli->query($query);
	return $res; 
}
function update_role($id,$role) {
	global $mysqli;
	$query = 'insert into people_roles set person_id="'.$id.'", role="'.$role.'";'; 
	$res = $mysqli->query($query);
	return $res; 
}

function add_staff_to_database($staff) {
	global $mysqli;
	for ($i=0;$i<count($staff);$i++) {
		$person = $staff[$i];
		$company = "The Open Data Institute";
		$key_string = trim($person["forname"]) . trim($person["surname"]) . trim($person["email"]);
        	$key = md5($key_string);
		$query = "select * from people where id='$key';";
		$res = $mysqli->query($query);
		if ($res->num_rows < 1) {
			$query = "insert into people set id='$key',firstname='".$person["forname"]."',email='".$person["email"]."',lastname='".$person["surname"]."',company='$company';";
			$res = $mysqli->query($query);
		}
	}
}

function associate_keycard($person_id,$keycard_id) {
	global $mysqli;

	$keycard_id = trim($keycard_id);

	$query = 'delete from people_keycards where keycard_id="'.$keycard_id.'";';
	$res = $mysqli->query($query);
	$query = 'insert into people_keycards set keycard_id="'.$keycard_id.'",person_id="'.$person_id.'";';
	$res = $mysqli->query($query);

	update_keycard_cache();

	return ($res);
}

global $keycards_last_update;
function keycard_processor() {
	global $mysqli;
	global $keycards_last_update;
	
	$file = "keycard.txt";
	$cache_file = "_keycard.txt";
	$last_modified = filemtime($file);
	clearstatcache();
	$cache_last_modified = file_get_contents($cache_file);
	if ($keycards_last_update == "" || $keycards_last_update < $cache_last_modified) {
		$keycards_last_update = $cache_last_modified;	
	}

	if ($keycards_last_update != $last_modified) {
		$keycard_id = file_get_contents($file);
		$query = 'select person_id from people_keycards where keycard_id="'.$keycard_id.'";';
		$res = $mysqli->query($query);
		$row = $res->fetch_row();
		$id = $row[0];
		if ($id) {
			if (signed_in($id)) {
				sign_out($id);
			} else {
				sign_in($id);
			}
		} else {
			echo "Keycard not known!\n";
		}
		update_keycard_cache();
	} 
}

function update_keycard_cache() {
	global $keycards_last_update;
	$cache_file = "_keycard.txt";
	
	$file = "keycard.txt";
	$last_modified = filemtime($file);		

	$keycards_last_update = $last_modified;	
	$handle = fopen($cache_file,"w");
	fwrite($handle,$last_modified);
	fclose($handle);	
}	

function register_keycard($keycard_id) {
	global $mysqli;
	$keycard_id = trim($keycard_id);
	$query = 'select person_id from people_keycards where keycard_id="'.$keycard_id.'";';
	$res = $mysqli->query($query);
	$row = $res->fetch_row();
	$id = $row[0];
	if ($id) {
		if (signed_in($id)) {
			if (sign_out($id)) { return 204; } else { return 500; }
		} else {
			if (sign_in($id)) { return 201; } else { return 500; }
		}
	} else {
		$file = '../keycard.txt';	
		$handle = fopen($file,"w");
		if (fwrite($handle,$keycard_id) !== false) { return 202; } else { return 500; }
		fclose($handle);
	}
}
