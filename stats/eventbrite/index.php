<?php
// load the API Client library
include "Eventbrite.php";
include "api_keys.php";

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

print_r($events);

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
