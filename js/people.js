load();

visitor_per_page = 24;
staff_per_page = 16;
visitor_counter = 0;
staff_counter = 0;

visitor_pages = 0;
current_visitor_page = 0;
current_visitor_node = "allonsite";

refresh_interval = 10000;
visitor_cycle_proc = undefined;

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

	append_visitor_pages();
	visitor_cycle_proc = window.setInterval(function() {cycle_visitors()},refresh_interval);
	
}

function signin_person(id,firstname,lastname,email) {
	if (email.indexOf('@theodi.org') > 0) {
		$('#'+id).css('display','inline-block')
	} else {
		if (visitor_counter == 0 || (visitor_counter % visitor_per_page) == 0) {
			page = visitor_counter / visitor_per_page;
			current_visitor_node = "allonsite" + page;
			if (page < 1) {
				$('#allonsite').append('<div id="' + current_visitor_node + '" class="people"></div>');
			} else {
				visitor_pages++;
				$('#allonsite').append('<div id="' + current_visitor_node + '" style="display: none;" class="people allonsite"></div>');
			}
		}
		$('#' + current_visitor_node).append('<div class="person" style="display: inline-block;"><a href="individual/?id='+id+'"><img class="people_pic" src="photo.php?id='+id+'"/></a>'+firstname + " " + lastname + '</div>');
		visitor_counter++;
	}
}

function append_visitor_pages() {
	if (visitor_pages < 1) {
		return;
	} else {
		$('#allonsite').append('<div id="visitor_nav" align="center" class="visitor_nav"></div>');
		for (i=0;i<=visitor_pages;i++) {
			show = i + 1;
			if (i == 0) {
				$('#visitor_nav').append('<a id="visitor_nav_'+ i +'" onclick="show_visitor_page(' + i + ');" style="font-weight: bold;">' + show + '</a> | ');
			} else {
				$('#visitor_nav').append('<a id="visitor_nav_'+ i +'" onclick="show_visitor_page(' + i + ');">' + show + '</a> | ');
			}
		}
		$('#visitor_nav').append('<a onclick="reset_interval();">&gt;&gt;</a>');
	}
	
}

function cycle_visitors() {
	var next_page = current_visitor_page + 1;
	if (next_page > visitor_pages) {
		next_page = 0;
	}
	$('#allonsite' + current_visitor_page).fadeOut(function() {
		$('#visitor_nav_' + next_page).css('font-weight','bold');
		$('#visitor_nav_' + current_visitor_page).css('font-weight','');
		$('#allonsite' + next_page).fadeIn(function() {
			current_visitor_page = next_page;
		});
	});
}

function show_visitor_page(page) {
	clearInterval(visitor_cycle_proc);
	visitor_cycle_proc = undefined;
	for (i=0;i<visitor_pages;i++) {
		if ($('#allonsite' + i).css("opacity") == 1) {
			$('#allonsite' + i).fadeOut(function() {
				$('#visitor_nav_' + page).css('font-weight','bold');
				$('#visitor_nav_' + i).css('font-weight','');
				$('#allonsite' + page).fadeIn(function() {
					current_visitor_page = page;	
				});
			});
		}	
	}
}

function reset_interval() {
	if (visitor_cycle_proc == undefined) {
		visitor_cycle_proc = window.setInterval(function() {cycle_visitors()},refresh_interval);
	}
}
