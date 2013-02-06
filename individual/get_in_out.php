<?php
	include('../database_connector.php');
	
	date_default_timezone_set('UTC');

	$yesterday = date("Y-m-d",time() - 86400);
	$yesterday = $yesterday . "T23:59:59Z";
	
	$query = "update in_out set checkout='$yesterday' where checkin<'$yesterday' and checkout='';";
	$mysqli->query($query);
	
	$query = "select people.id, people.firstname, people.lastname, people.email from people inner join in_out on in_out.id=people.id where checkout = '';";
	$res = $mysqli->query($query);
	while ($row = $res->fetch_row()) {
		$string = $row[0] . ', ' . $row[3] . ', ' . $row[1] . ', ' . $row[2] . "\n";
		echo $string;
	}

?>
