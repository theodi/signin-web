<?php
        error_reporting(E_ALL ^ E_NOTICE);

        include('../database_connector.php');

        date_default_timezone_set('UTC');

        $range[] = "2012";
        for ($j=2013;$j<date("Y")+1;$j++) {
                for($i=1;$i<13;$i++) {
                        $date = mktime(0,0,0,$i,1,$j);
                        $range[] = date("Y-m",$date);
                }
        }
        header('Content-type: text/csv');
        header('Content-disposition: filename="signin_stats.csv"');
        echo "Period,Unique Visitors in Period (signin),Unique Visitors in Period (Eventbrite), Unique Visitors in Period (Combined),New Visitors in Period (Combined)\n";
        $emails = array();
        for($i=0;$i<count($range);$i++) {
                $signin_count = 0;
                $eventbrite_count = 0;
                $combined_count = 0;
		$period_emails = array();
                $new = 0;
                $query = "select distinct(email) from people inner join in_out on in_out.id=people.id where in_out.checkin like '".$range[$i]."%' and email not like '%theodi.org';";
                $res = $mysqli->query($query);
                while ($row = $res->fetch_row()) {
                        if (!$emails[$row[0]]) {
                                $new++;
                                $emails[$row[0]] = true;
				$period_emails[$row[0]] = true;
                        }
                        $signin_count++;
                }
                
		$combined_count = $signin_count;
                
		$query = "select distinct(email) from people inner join eventbrite_attendees on eventbrite_attendees.person_id=people.id where eventbrite_attendees.checkin like '".$range[$i]."%' and email not like '%theodi.org';";
                $res = $mysqli->query($query);
                while ($row = $res->fetch_row()) {
                        if (!$emails[$row[0]]) {
                                $new++;
                                $emails[$row[0]] = true;
                        }
                        $eventbrite_count++;
			if (!$period_emails[$row[0]]) {
				$combined_count++;
				$period_emails[$row[0]] = true;
			}
                }

                echo $range[$i] . "," . $signin_count . "," . $eventbrite_count . "," . $combined_count . "," . $new . "\n";

        }
?>
