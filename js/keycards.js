
$.ajaxSetup ({
    // Disable caching of AJAX responses
    	cache: false
});

//$(document).ready(function() {
//	$("#add_card").click(function() {
//		read_card_1();
//	});
//});

function read_card_1() {
	var id = "";
	$("#add_card").hide(function() { 
		$("#new_card").show(function() {
			read_card_2(id,"person");
		});
	}); 
}

function read_member_card_1(id) {
	$('#person_id').val(id);
	$("#add_card_" + id).hide(function() { 
		$("#new_card_" + id).show(function() {
			read_card_2(id,"member");
		});
	}); 
}

function read_card_2(id,type) {
	read_card = false;
	cont = true;
	date_object = new Date();
	start = date_object.getTime();
	while (read_card == false && cont == true) {
		read_card = read_file();
		now = new Date().getTime();
		if ((now - start) > 10000) {
			cont = false;
		}
	}
	if (read_card) {
		theResource = "../keycard.txt";
		$.get(theResource, function(data) {
			$('#new_card_'+id).html("Registering Card: " + data);
			if (register_card(type,data)) {
				$('#new_card'+id).html("SUCCESS Registered: " + data);
				$("#add_card"+id).show();
			} else {
				$('#new_card'+id).html("Failed to register card (either try again or contact tech team for help)");			
			}
		});
	} else {
		$('#new_card'+id).html("No card recognised");
		$("#add_card"+id).show();
	}
}

function register_card(type,keycard_id) {
	ret = false;
	console.log("Registering: " + keycard_id);
	person_id = $('#person_id').val();
	console.log("Person: " + person_id);
	$.ajax({
		type: "POST",
		url: "../staff/staff_action.php",
		data: { "action": "associate_keycard_"+ type, "person_id": person_id, "keycard_id": keycard_id },
		success: function(data) { 
				ret = true; 
			},
		error: function (data) {
				ret = false; 
			},		
		async: false
	});
	return ret;
}

function read_file() {
	var now = Date.now().add({seconds :-10});
	theResource = "../keycard.txt";
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
		error: function(res,code,xhr) {
		},
		async: false
	});
	return ret;
}
