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
