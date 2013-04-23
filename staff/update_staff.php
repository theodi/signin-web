<?php

 /*
  * update_staff.php
  * ================
  * This script scrapes the staff data from theodi.org websites people page.
  * It's not clever or pretty, hence why this is a self contained file to be removed when structured data is available.
  * We don't even need to parse the dom since drupal gereates the same crap every time.
  * TODO: Replace this file with a more generic way of importing staff data from an open data source
  * 
  * Synopsis: run from the command line:
  *    # php update_staff.php
  *
  */

error_reporting(E_ALL ^ E_NOTICE);

require('../functions.php');
require('../database_connector.php');

$source = "http://www.theodi.org/team";
$domain = "theodi.org";
 
# This file does not have to already exist
$dest_file = "staff.csv";

# THIS PATH DOES HAVE TO EXIST
$images_path = "../stock";

$contents = file_get_contents($source);

$split = explode("foaf:Image",$contents);

for ($i=1;$i<count($split);$i++) {
	$people[] = get_person($split[$i], $domain);
}

$people = remove_duplicates($people);

write_data($people,$dest_file,$images_path,$domain);

function get_person($data, $domain) {

	# Get the image using substring
	$image_url = $data;
	$image_url = substr($image_url,strpos($image_url,'src="')+strlen('src="'),strlen($image_url));
	$image_url = substr($image_url,0,strpos($image_url,'"'));

	# Get the persons name by getting the span tag and then stripping the tags :)
 
	$name = $data;
	$name = explode("<span",$name);
	$name = $name[1];
	$name = explode("</span>",$name);
	$name = $name[0];
	$name = "<span" . $name . "</span>";

	$email = $name;

	$name = trim(strip_tags($name));

	$name = normalise_name($name);

	$parts = explode(" ",$name,2);
	$forname = $parts[0];
	$surname = $parts[1];
	
	$email = substr($email,strpos($email,'href="')+strlen('href="'),strlen($email));
	$email = substr($email,0,strpos($email,'"'));
	$email = substr($email,strrpos($email,'/')+1,strlen($email));
	$email = str_replace("-",".",$email) . "@" . $domain;

	$person["name"] = $name;
	$person["forname"] = $forname;
	$person["surname"] = $surname;
	$person["email"] = $email;
	$person["image_url"] = $image_url;

	return($person);
	
}

function normalise_name($name) {

	$titles = array('Sir ','Dame ','Dr ','Doctor ','Professor ','Prof ');

	for ($i=0;$i<count($titles);$i++) {
		$name = str_replace($titles[$i],"",$name);
	}

	return $name;
}

function remove_duplicates($people) {
	$done = array();
	for($i=0;$i<count($people);$i++) {
		$email = $people[$i]["email"];
		if ($email == "") {
			continue;
		}
		if ($done[$email]) {
			continue;
		}
		$done[$email] = true;
		$out[] = $people[$i];
	}
	return $out;
}

function write_data($people,$file,$image_path,$domain) {

	$base_url = "http://www." . $domain;
	
	$handle = fopen($file,"w");
	
	if (!$handle) {
		echo "\nFatal: Could not open $file for writing\n";
		return;
	}

	for($i=0;$i<count($people);$i++) {
		$person = $people[$i];
		$line = $person["forname"] . ", " . $person["surname"] . ", " . $person["email"] . "\n";
		fwrite($handle,$line);

		$image_file = $image_path . "/" . $person["email"] . ".jpg";
		$image = file_get_contents($base_url . $person["image_url"]);

		$img_handle = fopen($image_file,"w");

		if (!$img_handle) {
			echo "\nWarning: Could not update image for " . $person["name"] . " at " . $image_file . "\n";
		} else {
			fwrite($img_handle,$image);
			fclose($img_handle);
		}
	
	}

	fclose($handle);

	add_staff_to_database($people);

}

?>
