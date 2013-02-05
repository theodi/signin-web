<?php

	include('database_connector.php');

	$id = $_GET["id"];

	$query = "select photo from people where id='$id';";

	$res = $mysqli->query($query);
	
	
	$row = $res->fetch_row();
	if ($row[0] != "") {	
		header('Content-type: image/jpeg');
		echo base64_decode($row[0]);
	} else {
		$pic = file_get_contents('blank/person.png');
		header('Content-type: image/png');
		echo $pic;
	}

?>
