<?php
	include('../branding.php');
	get_header();
	get_branding("Badge Printing");
?>
	<script src="http://code.jquery.com/jquery-1.8.3.min.js"></script>
	<div width="80%" style="border: 2px solid black; border-radius: 10px;" align="center">
		<h3>Format = Firstname Lastname, Company</h3>
		<textarea rows="25" style="width: 90%;"></textarea>
		<br/>
		<button name="submit" id="submit" style="padding: 0.5em 1em 0.5em 1em;">Print</button>
		<br/>
		<br/>
	</div>
<?php
	get_footers();
?>
