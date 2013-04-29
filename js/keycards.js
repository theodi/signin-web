$(document).ready(function() {
	$("#add_card").click(function() {
		var read_card = "Failed to read card.";
		$("#new_card").html('Please put card on reader'); 
		while (read_file() == false) {
		}
		theResource = "/signin/keycard.txt";
		$.get(theResource, function(data) {
			$('#new_card').html("Registering Card: " + data);
			var state = register_card(data);
			
		});
	});
	
});

function register_card(keycard_id) {
	console.log("Registering: " + keycard_id);
	person_id = $('#person_id').val();
	$.post("../staff/staff_action.php", { "action": "associate_keycard", "person_id": person_id, "keycard_id": keycard_id } );
}

function read_file() {
	var now = Date.now().add({seconds :-10});
	theResource = "/signin/keycard.txt";
	var ret = true;
	$.ajax({
		url:theResource,
		type:"head",
		success:function(res,code,xhr) {
			var last_modified = xhr.getResponseHeader("Last-Modified");
			last_modified = new Date(last_modified);
			if (last_modified > now) {
				ret = true;
			} else {
				ret = false;
			}
		},
		async: false
	});
	return ret;
}
