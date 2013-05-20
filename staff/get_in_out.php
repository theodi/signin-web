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
		$query = 'select role from people_roles where person_id="' . $row[0] .'" order by valid_from desc limit 1;';
		$res2 = $mysqli->query($query);
		if ($res2) {
			$row2 = $res2->fetch_row();
			$role = $row2[0];
		} else {
			$role = "visitor";
		}
		$string = $row[0] . ', ' . $row[3] . ', ' . $row[1] . ', ' . $row[2] . ', ' . $role . "\n";
		echo $string;
	}

?>
