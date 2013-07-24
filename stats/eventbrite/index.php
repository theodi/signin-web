<?php

Error_reporting(E_ALL ^ E_NOTICE);

// load the API Client library
include "Eventbrite.php";
include "api_keys.php";
include "../../database_connector.php";

$eb_client = new Eventbrite( $authentication_tokens );

// For more information about the features that are available through the Eventbrite API, see http://developer.eventbrite.com/doc/
$events = $eb_client->user_list_events();

$array = objectToArray($events);
$array = $array["events"];

$events = array();

$count = 0;
for($i=0;$i<count($array);$i++) {
        $event = $array[$i]["event"];
        if (trim($event["status"]) == "Completed") {
                $events[$count]["id"] = $event["id"];
                $events[$count]["start_date"] = substr($event["start_date"],0,10);
                $count++;
        }
}

for ($i=0;$i<count($events);$i++) {
        $id = $events[$i]["id"];
        try{
        // For more information about the functions that are available through the Eventbrite API, see http://developer.eventbrite.com/doc/
            $attendees = $eb_client->event_list_attendees( array('id'=>$id) );
            $events[$i] = process_attendees($events[$i],$attendees);
        } catch ( Exception $e ) {
            // Be sure to plan for potential error cases
            // so that your application can respond appropriately

            //var_dump($e);
            $attendees = array();
        }

}

for ($i=0;$i<count($events);$i++) {
	$event = $events[$i];
	update_datebase($event);
}

function update_datebase($event) {
	global $mysqli;
	if (count($event["attendees"]) < 1) {
		return;
	}
	$attendees = $event["attendees"];
	for($i=0;$i<count($attendees);$i++) {
		$array = $attendees[$i];
		$key_string = $array["firstname"] . $array["lastname"] . $array["email"];
		$key = md5($key_string);
		$query = 'select * from people where id="'.$key.'";';
		$res = $mysqli->query($query) or die($mysqli->error);
		if ($res->num_rows < 1) {
			$query = 'insert into people set id="'.$key.'", firstname="'.$array["firstname"].'", lastname="'.$array["lastname"].'", email="'.$array["email"].'";';	
			$res = $mysqli->query($query) or die($mysqli->error);
		}
		$event_id = $event["id"];
		$checkin = $event["start_date"] . "T00:00:00";
		$query = 'select * from eventbrite_attendees where person_id="'.$key.'" and event_id="'.$event_id.'";';
		$res = $mysqli->query($query) or die($mysqli->error);
		if ($res->num_rows < 1) {
			$query = 'insert into eventbrite_attendees set person_id="'.$key.'", event_id="'.$event_id.'", checkin="'.$checkin.'";';	
			$res = $mysqli->query($query) or die($mysqli->error);
			
		}	
	}
}

function process_attendees($event,$attendees) {
	$attendees = objectToArray($attendees);
	$attendees = $attendees["attendees"];
	for ($i=0;$i<count($attendees);$i++) {
		$attendee = $attendees[$i]["attendee"];
		$person["firstname"] = $attendee["first_name"];
		$person["lastname"] = $attendee["last_name"];
		$person["email"] = $attendee["email"];
		$event["attendees"][] = $person;
	}
	return $event;
}

function objectToArray($d) {
	if (is_object($d)) {
		// Gets the properties of the given object
		// with get_object_vars function
		$d = get_object_vars($d);
	}

	if (is_array($d)) {
		/*
		 * Return array converted to object
		 * Using __FUNCTION__ (Magic constant)
		 * for recursive call
		 */
		return array_map(__FUNCTION__, $d);
	}
	else {
		// Return array
		return $d;
	}
}

?>
