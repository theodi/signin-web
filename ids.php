<?php
	require('functions.php');

	$handle = fopen('staff.csv','r');
	while ($line = fgets($handle)) {
		$parts = explode(",",$line);
		$firstname = trim($parts[0]);
		$lastname = trim($parts[1]);
		$email = trim($parts[2]);
		$company = "The Open Data Institute";
		$key_string = trim($parts[0]) . trim($parts[1]) . trim($parts[2]);
        	$key = md5($key_string);
		$query = "select * from people where id='$key';";
		$res = $mysqli->query($query);
		if ($res->num_rows < 1) {
			$query = "insert into people set id='$key',firstname='$firstname',email='$email',lastname='$lastname',company='$company';";
			$res = $mysqli->query($query);
		}
	}
?>
