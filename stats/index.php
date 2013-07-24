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
        echo "Period,Unique Visitors in Period,New Visitors in Period\n";
        $emails = array();
        for($i=0;$i<count($range);$i++) {
                $count = 0;
                $new = 0;
                $query = "select distinct(email) from people inner join in_out on in_out.id=people.id where in_out.checkin like '".$range[$i]."%' and email not like '%theodi.org';";
                $res = $mysqli->query($query);
                while ($row = $res->fetch_row()) {
                        if (!$emails[$row[0]]) {
                                $new++;
                                $emails[$row[0]] = true;
                        }
                        $count++;
                }
                echo $range[$i] . "," . $count . "," . $new . "\n";

        }
?>
