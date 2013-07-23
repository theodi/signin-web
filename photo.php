<?php

	include('database_connector.php');
	include('functions.php');
	$id = $_GET["id"];

	$query = "select photo,email from people where id='$id';";
	$res = $mysqli->query($query);
	$row = $res->fetch_row();
	if (file_exists('stock/'.$row[1].'.jpg')) {
		header("Location: stock/".$row[1].'.jpg');
	} elseif ($row[0] != "") {
		header('Content-type: image/jpeg');
		echo base64_decode($row[0]);
	} elseif (is_member($id)) {
		$member_id = get_member_id($id);
		$member = get_member_details($member_id);
		$url = $member["member"]["logo"][0]["thumbnail"]["contentUrl"];
		if ($url) {
			$pic = file_get_contents($url);
			echo $pic;
		} else {
			header('Content-type: image/png');
			get_image($member["member"]["name"]);
		}
	} else {
		$pic = file_get_contents('blank/person.png');
		header('Content-type: image/png');
		echo $pic;
	}

	function get_image($string) {
		$font  = 4;
		$width  = imagefontwidth($font) * strlen($string);
		$height = imagefontheight($font);

		$image = imagecreatetruecolor ($width,$height);
		$white = imagecolorallocate ($image,255,255,255);
		$black = imagecolorallocate ($image,0,0,0);
		imagefill($image,0,0,$white);

		imagestring ($image,$font,0,0,$string,$black);

		imagepng ($image);
		echo $image;
		imagedestroy($image);
	}

	function get_member_id($person_id) {
		global $mysqli;
		$query = 'select member_keycards.member_id from member_keycards inner join people_keycards on people_keycards.keycard_id=member_keycards.keycard_id where people_keycards.person_id="'.$person_id.'";';
		$res = $mysqli->query($query);	
		$row = $res->fetch_row();
		return $row[0];
	}

	function get_member_details($id) {
		$json = file_get_contents("http://directory.theodi.org/members.json");
        	$members = json_decode($json,true);
        	$members = $members["memberships"];
      		for($i=0;$i<count($members);$i++) {
                	$member = $members[$i];
                	$membership_id = $member["membershipId"];
			if ($membership_id == $id) {
				return $member;
			}
        	}
	}
?>
