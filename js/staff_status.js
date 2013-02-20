$(document).ready(function() {
		$(".checkin").click(function() {
			var id = $(this).val();
			$.post("../staff/staff_action.php", { "action": "checkin", "id": id } );
			var opposite = "checkout_" + id;
			$(this).hide();	
			$("#"+opposite).show();
		});
		$(".checkout").click(function() {
			var id = $(this).val();
			$.post("../staff/staff_action.php", { "action": "checkout", "id": id } );
			var opposite = "checkin_" + id;
			$(this).hide();	
			$("#"+opposite).show();
		});
});
