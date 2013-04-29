<?php

$last_md5 = "dsahjdkas";
$dir = "/Users/odidisplay/Documents/odi-sign-in";

date_default_timezone_set('UTC');

include('database_connector.php');
include('functions.php');

while(true) {
	$cmd = "cat $dir/* | md5 2>/dev/null";
	$md5 = system($cmd);
	if ($md5 != $last_md5) {
		$last_md5 = $md5;
		$people = process_files($dir);
	}
	//print_r($people);
	update_database($people,$mysqli);
	//Process Keycard signins
	keycard_processor();
	//create_html($people);
	sleep(3);
}

function print_name_badge($person,$mysqli) {
	$weekago = date("Y-m-d",time() - 518400);
        $weekago = $weekago . "T23:59:59Z";
	
	$twentymins = date("Y-m-d",time() - 1200);

	$key = $person["key"];
	$firstname = $person["firstname"];
	$lastname = $person["lastname"];
	$company = $person["company"];
	$email = $person["email"];
	echo "Printing name badge for $firstname $lastname\n";
	
	if (strpos($email,"@theodi.org") !== false) {
		return;
	}

        $query = "select * from in_out where id='$key' and checkin>'$weekago' and badge_printed>0;";
	$res = $mysqli->query($query);

	if ($res->num_rows > 0) {
		return true;
	}
	
	$query = "update in_out set badge_printed=1 where id='$key';";
	$res = $mysqli->query($query);

	$ret = print_label($firstname . " " . $lastname, $company);

}

function print_label($name,$company) {
	$file_name = "label_" . time() . ".applescript";
	
	$content = file_get_contents('template.applescript');
	
	$content = str_replace('the_name',$name,$content);
	$content = str_replace('the_company',$company,$content);

	$handle = fopen($file_name,"w");
	fwrite($handle,$content);
	fclose($handle);

	exec('osascript ' . $file_name . ' &');

	unlink($file_name);

	return true;
}

function update_database($people,$mysqli) {
	for ($i=0;$i<count($people);$i++) {

		$key = $people[$i]["key"];
		$query = 'select * from people where id="'.$key.'";';
		$res = $mysqli->query($query) or die($query);

		if ($res->num_rows > 0) {
			//UPDATE IF NEWER RECORD
			$last_update = get_last_update($key,$mysqli);
			$checkin = $people[$i]["checkin"];
			$checkout = $people[$i]["checkout"];
			if ($checkin > $last_update || $checkout > $last_update) {
				update_person($people[$i],$mysqli);
			}
		} else {
			create_person($people[$i],$mysqli);
		}
		update_in_out($people[$i],$mysqli);
	} 
}

function update_in_out($person,$mysqli) {
	$key = $person["key"];
	$checkin = $person["checkin"];
	$checkout = $person["checkout"];
	
	$query = 'select * from in_out where checkin="'.$checkin.'" and checkout="'.$checkout.'" and id="'.$key.'";';
	$res = $mysqli->query($query) or die($query);
	
	if ($res->num_rows > 0) {
		return;
	}

	if ($checkout == "") {
		$query = 'select * from in_out where checkin="'.$checkin.'" and id="'.$key.'";';
		$res = $mysqli->query($query) or die($query);
	}
	
	if ($res->num_rows > 0) {
		return;
	}
	
	if ($checkout != "") {
		$query = 'select * from in_out where checkin="'.$checkin.'" and id="'.$key.'";';
		$res = $mysqli->query($query) or die($query);
	
		if ($res->num_rows > 0) {
			echo "updating in_out \n";
			$query = 'update in_out set checkout="'.$checkout.'" where checkin="'.$checkin.'" and id="'.$key.'";';
			$res = $mysqli->query($query) or die($query);
			return;
		} 
	}

	echo "inserting in_out \n";
	$query = 'insert into in_out set checkin="'.$checkin.'", checkout="'.$checkout.'", id="'.$key.'";';
	$res = $mysqli->query($query) or die($query);
	print_name_badge($person,$mysqli);
	return;

}

function update_person($persson,$mysqli) {
	echo "UPDATE\n";
}

function create_person($person,$mysqli) {
	
	$firstname = $person["firstname"];
	$lastname = $person["lastname"];
	$email = $person["email"];
	$company = $person["company"];
	$key = $person["key"];
	$photo = $person["photo"];
	
	$query = 'insert into people set id="'.$key.'", firstname="'.$firstname.'", lastname="'.$lastname.'", email="'.$email.'", company="'.$company.'", photo="'.$photo.'";';	
	$res = $mysqli->query($query) or die($mysqli->error);
	
	return $res;

}

function get_last_update($key,$mysqli) {
	$latest = 0;
	$query = 'select checkin, checkout from in_out where id="'.$key.'";';
	$res = $mysqli->query($query) or die($query);
	while ($row = $res->fetch_row()) {
		if ($row[0] != "" && $row[0] > $latest) {
			$latest = $row[0];
		} 
		if ($row[1] != "" && $row[1] > $latest) {
			$latest = $row[1];
		} 
	}
	return $latest;
}

function process_files($dir) {
	$people = array();
	if ($handle = opendir($dir)) {
		while (false !== ($entry = readdir($handle))) {
			if ($entry != "." && $entry != ".." && substr($entry,-6) == ".1.cdt" && substr($entry,0,3) != "rec") {
				$new_people = process_file($dir,$entry);
				if ($new_people) { 
					$people = array_merge($new_people,$people);
				}	
			}
		}
		closedir($handle);
	}
	for ($i=0;$i<count($people);$i++) {
		if ($people[$i]["firstname"] != "") {
			$out[] = $people[$i];
		}
	}
	$people = $out;
	return $people;
}

function process_file($dir,$file) {
	
	$key = substr($file,0,strpos($file,"."));
	
	exec("cp $dir/$file $file");
	exec("unzip $file");
	unlink($file);
	exec("mv contents $key.plist");
	exec("plutil -convert xml1 $key.plist");

	$file = "$key.plist";
	$file2 = "$key.xml";

	$write_handle = fopen($file2,"w");
	
	$handle = fopen($file,"r");
	$active = false;
	$count = 0;
	if (!$handle) {
		echo "ERROR\n";
		fclose($write_handle);
		return;
	}
	while (!feof($handle)) {
		$line = fgets($handle);
		$count++;
		if (trim($line) == "<dict>" || $count > 2) {
			$active = true;
		}
		if ($active) {
			fwrite($write_handle,$line);
		}
	}
	fclose($handle);
	fclose($write_handle);

	unlink($file);
	
	require_once('bxmlio.inc.php');

	$root = bxmlio_load($file2);
	$dict = bxmlio_find($root,"dict");

	$person = "";
	$person = process_node($dict["children"]);
	
	unlink($file2);
	
#	update_transaction_number($person["kvStr"]);

	$person = simplify_person($person,$key);
	return $person;

	if ($person) { 
		$people = array_merge($person,$people);
	}
	
	return $people;
}	

function update_transaction_number($string) {
	
	$number = 0;
	$file = ".last_transaction_number";
	if (file_exists($file)) {
		$handle = fopen($file,"r");
		$number = trim(fgets($handle));
		fclose($handle);	
	}
	
	$input_number = substr($string,strrpos($string,":")+1,strlen($string));
	
	if ($input_number > $number) {
		$handle = fopen($file,"w");
	        fwrite($handle,$input_number);
        	fclose($handle);
	}
	
	return;

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

function create_html($people) {
	exec('rm -fR images');
	mkdir('images');
	exec('cp header.html index.html');
	$handle = fopen("index.html","a+");
	$table = '<table style="width: 100%;">';
	fwrite($handle,$table);
	fwrite($handle,get_html_headers());
	for ($i=0;$i<count($people);$i++) {
		$person = $people[$i];
		store_image($person);
		fwrite($handle,get_person_display($person));
	}
	fwrite($handle,'</table>');
	exec('cat footer.html >> index.html');
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
