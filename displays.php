<?php
	include('branding.php');
	get_header();
	get_branding("Today's Visitors");
	$categories['visitor']['id'] = 'visitor';
	$categories['visitor']['name'] = 'All On-Site Visitors';
	$categories['staff']['id'] = 'staff';
	$categories['staff']['name'] = 'ODI Staff / Associates';
	$categories['startup']['id'] = 'startups';
	$categories['startup']['name'] = 'Start-Up Members';
?>
	<script src="http://code.jquery.com/jquery-1.8.3.min.js"></script>
<script type="text/javascript">
	page = "display";
</script>
        <script src="js/people.js"></script>
<style>
.pages_nav {
	display: none;
}
body {
	width: 1300px;
}
</style>
<?php
	foreach($categories as $key => $values) {
		$div_id = $values['id'];
		$title = $values['name'];
		echo "\t" . '<div id="'.$div_id.'" class="peoplebox display_'.$div_id.'">' . "\n";
		echo "\t\t" . '<div class="titlediv" align="center">' . "\n";
		echo "\t\t\t" . '<h1 class="boxtitle">' . $title . '</h1>' . "\n";
		echo "\t\t" . '</div>' . "\n";
		echo "\t" . '</div>' . "\n";
	}
	get_footers();
?>
