<?php
	include('../branding.php');
	include('../functions.php');
	get_header();
	get_branding("Members");

if (file_exists('layout.css')) {
	echo '<link rel="stylesheet" type="text/css" href="layout.css">';
} else {
	echo '<link rel="stylesheet" type="text/css" href="../layout.css">';
}
?>
	<script src="http://code.jquery.com/jquery-1.8.3.min.js"></script>
	<script type="text/javascript">
		page = "individual";
	</script>
        <script src="../js/people.js"></script>
        <script src="../js/staff_status.js"></script>
	<script src="../js/keycards.js"></script>
	<script src="../js/date.js"></script>
	<style>
.member {
	padding-left: 3em;
	padding-top: 1em;
	padding-bottom: 1em;
	border-bottom: 1px solid gray;
}
.member_info {
	width: 40%;
	text-align: center;
	display: inline-block;
}
.card {
	font-size: 0.8em;
	text-align: center;
	width: 40%;
	display: inline-block;
}
.member_logo {
	max-height: 40px;
}
.member_name {
	font-size: 1.4em;
}
	</style>
	<input type="hidden" name="person_id" id="person_id" value=""/>
<?php

	$json = file_get_contents("http://directory.theodi.org/members.json");
	$members = json_decode($json,true);
	$members = $members["memberships"];

	for($i=0;$i<count($members);$i++) {
		$member = $members[$i];
		$membership_id = $member["membershipId"];
		$name = $member["member"]["name"];
		$logo = $member["member"]["logo"][0]["contentUrl"];
		draw_member($membership_id,$name,$logo);
	}

function draw_member($id,$name,$logo) {
	echo '<section class="member" id="'.$id.'">';
	echo '<section class="member_info">';
	echo '<img class="member_logo" src="'.$logo.'"/><br/>';
	echo '<item class="member_name">'.$name.'</item>';
	echo '</section>';
	echo '<section id="keycards" class="card">
		<div id="add_card_'.$id.'">
			<button style="font-size: 1.5em; height: 1.6em" id="associate" onclick="read_member_card_1(\''.$id.'\');">Associate NFC Card</button>
		</div>
		<div style="font-size: 1.5em; display: none;" id="new_card_'.$id.'">
			Please put card on the reader
		</div>
	</section>';
	echo '</section>';
}

?>
