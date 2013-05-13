load();

$.ajaxSetup ({
    // Disable caching of AJAX responses
    cache: false
});

function load() {
	
	$.get('../staff/get_in_out.php', function(data) {	
		process_data(data);
	})
	.fail(function() { 
		$.get('staff/get_in_out.php', function(data) {
                	process_data(data);
	        })
	});
}
		
function process_data(allText) {

        var allTextLines = allText.split(/\r\n|\n/);

	for (var i=0;i<allTextLines.length-1;i++) {
        	var entry = allTextLines[i].split(',');
		var id = entry[0];
		var email = entry[1];
		var firstname = entry[2];
		var lastname = entry[3];
		signin_person(id,firstname,lastname,email);
	}
}

function signin_person(id,firstname,lastname,email) {
	if (email.indexOf('@theodi.org') > 0) {
		$('#'+id).css('display','inline-block')
	} else {
		$('#allonsite').append('<div class="person" style="display: inline-block;"><a href="individual/?id='+id+'"><img class="people_pic" src="photo.php?id='+id+'"/></a>'+firstname + " " + lastname + '</div>');
	}
}
