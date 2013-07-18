<?php
	include('branding.php');
	get_header();
	get_branding("Today's Visitors");
	$categories['visitor']['id'] = 'visitor';
	$categories['visitor']['name'] = 'All On-Site Visitors';
	$categories['startup']['id'] = 'startup';
	$categories['startup']['name'] = 'Members / Start-Ups';
	$categories['staff']['id'] = 'staff';
	$categories['staff']['name'] = 'ODI Staff / Associates';
	$categories['staff']['link'] = 'staff/';
?>

	<script src="http://code.jquery.com/jquery-1.8.3.min.js"></script>
<script type="text/javascript">
	page = "normal";
</script>
        <script src="js/people.js"></script>
	<div align="center">
	<nav id="categories">
	<ul>
<?php
	$count = 0;
	foreach($categories as $key => $values) {
		$div_id = $values['id'] . "_nav";
		$title = $values['name'];
		$link = $values['link'];
		if ($count == 0) {
			echo "\t\t" . '<li id="'.$div_id.'" class="selected">';
		} else {
			echo "\t\t" . '<li id="'.$div_id.'">';
		} 
		echo $title .'</li>' . "\n";
		$count++;
	}
?>
	</ul>
	</nav>
<?php
	$count = 0;
	foreach($categories as $key => $values) {
		$div_id = $values['id'];
		$title = $values['name'];
		$link = $values['link'];
		if ($count == 0) {
			echo "\t" . '<div id="'.$div_id.'" class="peoplebox normalbox">' . "\n";
		} else {
			echo "\t" . '<div id="'.$div_id.'" class="peoplebox normalbox" style="display: none;">' . "\n";
		}
		echo "\t\t" . '<div class="titlediv" align="center">' . "\n";
		echo "\t\t\t" . '<h1 class="boxtitle">';
		if ($link != "") {
			echo '<a href="' . $link .'">' . $title .'</a></h1>' . "\n";
		} else {
			echo $title .'</h1>' . "\n";
		}
		echo "\t\t" . '</div>' . "\n";
		echo "\t" . '</div>' . "\n";
		$count++;
	}
?>
</div>
<?php
	get_footers();
?>
