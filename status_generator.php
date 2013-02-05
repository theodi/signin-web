<?php

include('database_connector.php');

$last_md5 = "dsahjdkas";
$dir = "/Users/Davetaz/odi-sign-in";

date_default_timezone_set('UTC');

$people_in = get_in_office($mysqli);
create_html($people_in);

function get_in_office($mysqli) {
	
	$today = date("Y-m-d");
	$query = "select people.id, firstname, lastname, photo, in_out.checkin, in_out.checkout, email, company from people inner join in_out on in_out.id=people.id where in_out.checkin like '".$today."%' and in_out.checkout = ''";
	$res = $mysqli->query($query) or die($query);
	$count = 0;
	while ($row = $res->fetch_row()) {
		$people[$count]["key"] = $row[0];
		$people[$count]["firstname"] = $row[1];
		$people[$count]["lastname"] = $row[2];
		$people[$count]["photo"] = $row[3];
		$people[$count]["checkin"] = $row[4];
		$people[$count]["checkout"] = $row[5];
		$people[$count]["email"] = $row[6];
		$people[$count]["company"] = $row[7];
		$count++;
	}

	return $people;
}

function create_html($people) {
	exec('rm -fR images');
	mkdir('images');
	exec('cp header.html index.html');
	$handle = fopen("index.html","a+");
	fwrite($handle,get_layout());
	exec('cat footer.html >> index.html');
	fclose($handle);
}

function get_layout() {
	$string = '<div align="center">';
	$string .= '<div style="width: 60%; height: 300px; border: 1px solid black; border-radius: 10px;">dsadasdas</div>';
	$string .= '<div style="float: right; width: 30px; border: 1px solid black; border-radius: 10px;">fdgsahdjsadsa</div>';
	return $string;
}

function store_image($person) {
	if ($person["photo"] == "") {
		return;
	}
	$path = "images/" . $person["key"] . ".jpeg";
	$handle = fopen($path,"w");
	fwrite($handle,base64_decode($person["photo"]));
	fclose($handle);
}

function get_html_headers() {
	$string = "<tr>\n";
	$string .= "\t" . '<th>Pic</th>' . "\n";
	$string .= "\t" . '<th>Name</th>' . "\n";
	$string .= "\t" . '<th>EMail</th>' . "\n";
	$string .= "\t" . '<th>Company</th>' . "\n";
	$string .= "\t" . '<th>Time In</th>' . "\n";
	$string .= "\t" . '<th>Time Out</th>' . "\n";
	$string .= "\t" . '<th>Options</th>' . "\n";
	$string .= "</tr>";
	return $string;
}

function get_person_display($person) {
	$string = '<tr style="height: 64px; padding: 0.2em;">' . "\n";
	$string .= "\t" . '<td width="64px">';
	$image_file = 'images/' . $person["key"] . '.jpeg';
	if (file_exists($image_file)) {
		$string .= '<img style="width: 64px;" src="images/' . $person["key"] . '.jpeg"/>';
	} 
	$string .= '</td>' . "\n";
	$string .= "\t" . '<td>' . $person["firstname"] . " " . $person["lastname"] . '</td>' . "\n";
	$string .= "\t" . '<td>' . $person["email"] . '</td>' . "\n";
	$string .= "\t" . '<td>' . $person["company"] . '</td>' . "\n";
	$string .= "\t" . '<td>' . $person["checkin"] . '</td>' . "\n";
	$string .= "\t" . '<td>' . $person["checkout"] . '</td>' . "\n";
	$string .= "</tr>";
	return $string;
	
}

function simplify_person($array,$key) {
	
	$person["key"] = $key;

	$single_person = false;

	for($i=0;$i<count($array["updated"])+1;$i++) {
		if (isset($array["updated"][$i]["photo"])) {
			$single_person = true;
		}		
	}
	for($i=0;$i<count($array["inserted"])+1;$i++) {
		if (isset($array["inserted"][$i]["photo"])) {
			$single_person = true;
		}		
	}

	if ($single_person) {
		$person[] = get_single_person($array,$key);
		return $person;
	} else {
		return get_people($array,$key);
	}
}

function get_people($array,$key) {
	
	$person = false;

	for($i=0;$i<count($array["updated"])+1;$i++) {
		$empty = array();
		$temp_array = get_person_from_array($array["updated"][$i],$empty);
		if (isset($temp_array["firstname"])) {
			$person[] = $temp_array;
		}
	}
	
	for($i=0;$i<count($array["inserted"])+1;$i++) {
		$empty = array();
		$temp_array = get_person_from_array($array["inserted"][$i],$empty);
		if (isset($temp_array["firstname"])) {
			$person[] = $temp_array;
		}
	}

	return $person;
	
}

function get_single_person($array,$key) {

	for($i=0;$i<count($array["updated"])+1;$i++) {
		if (isset($array["updated"][$i]["photo"])) {
			$person["photo"] = $array["updated"][$i]["photo"];
		} elseif (isset($array["updated"][$i]["firstname"])) {
			$person = get_person_from_array($array["updated"][$i],$person);
		}
	}
	
	for($i=0;$i<count($array["inserted"])+1;$i++) {
		if (isset($array["inserted"][$i]["photo"])) {
			$person["photo"] = $array["inserted"][$i]["photo"];
		} elseif (isset($array["inserted"][$i]["firstname"])) {
			$person = get_person_from_array($array["inserted"][$i],$person);
		}
	}

	if (isset($person["firstname"])) {	
		return $person;
	} else {
		return false;
	}

}

function get_person_from_array($array,$person) {	

	$person["checkin"] = $array["checkin"];
	$person["checkout"] = $array["checkout"];
	$person["company"] = $array["company"];
	$person["data"] = $array["data"];
	$person["email"] = $array["email"];
	$person["firstname"] = $array["firstname"];
	$person["lastname"] = $array["lastname"];

	$key_string = $array["firstname"] . $array["lastname"] . $array["email"];
	$key = md5($key_string);
	
	$person["key"] = $key;

	return $person;

}

	
function process_node($dict) {

	$array = "";
	$current_key = "";
	$current_value = "";
	$last_key = "";

	for($i=0;$i<count($dict);$i++) {
		$node = $dict[$i];
		$value = $node["cdata"];
		if ($node["name"] == "key") {
			$array[$value] = "";
			#$current_count = count($array[$value]) -1;
			$current_key = $value;
		} elseif ($node["name"] == "array" || $node["name"] == "dict") {
			$to_append = process_node($node["children"]);
			$array[$current_key] = $to_append;
		} else { 
			if ($value != "") {
				if ( $last_key == "key" ) {
					$array[$current_key] = $value;
				} else {
					$array[] = $value;
				}
			}
		}
		$last_key = $node["name"];
				
	}

	return $array;

}

?>
