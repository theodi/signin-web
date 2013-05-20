load();

var roles = new Array();
var roles_current_page = new Array();
var roles_counter = new Array();
var roles_cycle_proc = new Array();
var roles_pages = new Array();
var current_role_page = new Array();

roles[0] = "staff";
roles[1] = "startup";
roles[2] = "visitor";

//people per page
peoplepp = 24;

//starting pages
for (i=0;i<roles.length;i++) {
	//Current page to append people to
	roles_current_page[roles[i]] = 0;
	//Roles counter
	roles_counter[roles[i]] = 0;
	//Roles Pages
	roles_pages[roles[i]] = 0;
	//Cycle process id
	roles_cycle_proc[roles[i]] = undefined;
	//role pages
	current_role_page[roles[i]] = 0;
}

var refresh_interval = 10000;

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

function reveal(id) {
	hide_all();
	$(id).show();
	$(id+"_nav").addClass("selected");     
	window.location.hash = (id);
}
function hide_all() {
	for (i=0;i<roles.length;i++) {
		role = roles[i].trim();
		$('#'+role).hide();
		$('#'+role+'_nav').removeClass("selected");
	}
}

function switch_roles(role) {
	for (i=0;i<roles.length;i++) {
		role = roles[i].trim();
		$('#'+role+'_nav').removeClass("role_selected");
	}
	$("#role_" + role).addClass("role_selected");
	id = $("input#person_id").val();	
	$.post("staff/staff_action.php", { "action": "role", "id": id, "role": role } );
	$.post("../staff/staff_action.php", { "action": "role", "id": id, "role": role } );
}

$(document).ready(function() {
//        var hash = window.location.hash;
//        if (hash) {
//                reveal(hash);
//        }
/*
	for (i=0;i<roles.length;i++) {
        	role = roles[i].trim();
		console.log("external + " + role);
		$("#"+role+"_nav").click(function () {
			reveal("#"+role);
        	});
        	$("#role_"+role).click(function () {
                	switch_roles(role);
        	});
	}
*/	
		$("#staff_nav").click(function () {
			reveal("#staff");
        	});
        	$("#role_staff").click(function () {
                	switch_roles("staff");
        	});
		$("#visitor_nav").click(function () {
			reveal("#visitor");
        	});
        	$("#role_visitor").click(function () {
                	switch_roles("visitor");
        	});
		$("#startup_nav").click(function () {
			reveal("#startup");
        	});
        	$("#role_startup").click(function () {
                	switch_roles("startup");
        	});

});

function process_data(allText) {

        var allTextLines = allText.split(/\r\n|\n/);

	for (var i=0;i<allTextLines.length-1;i++) {
        	var entry = allTextLines[i].split(',');
		var id = entry[0];
		var email = entry[1];
		var firstname = entry[2];
		var lastname = entry[3];
		var role = entry[4];
		signin_person(id,firstname,lastname,email,role);
	}

//	for (i=0;i<roles.length;i++) {
//		role = roles[i].trim();
//		append_roles_nav(role);
//		roles_cycle_proc[role] = window.setInterval(function() {cycle_role(role)},refresh_interval);
//	}	
}


function signin_person(id,firstname,lastname,email,role) {
	if (role == "staff") {
		if ($('#'+id).length > 0) {
			$('#'+id).css('display','inline-block')
		} else {
			append_person(id,firstname,lastname,email,role);
		}
	} else {
		append_person(id,firstname,lastname,email,role);
	}
}

function append_person(id,firstname,lastname,email,role) {
	role = role.trim();
	roles_current_page[role] = 0;
	current_node = role + "_" + roles_current_page[roles[i]];
	//Cycle process id
	roles_cycle_proc[roles[i]] = undefined;
	current_node = role + "_" + roles_current_page[role];
	if (roles_counter[role] == 0 || (roles_counter[role] % peoplepp) == 0) {
		current_node = role + "_" + roles_current_page[role];
		if (roles_current_page[role] < 1) {
			$('#' + role).append('<div id="' + current_node + '" class="people"></div>');
		} else {
			roles_pages[role]++;
			$('#' + role).append('<div id="' + current_node + '" style="display: none;" class="people allonsite"></div>');
			roles_current_page[role]++;
		}
		roles_counter[role]++;
	}
	$('#' + current_node).append('<div class="person" style="display: inline-block;"><a href="individual/?id='+id+'"><img class="people_pic" src="photo.php?id='+id+'"/></a>'+firstname + " " + lastname + '</div>');	
}

function append_roles_nav(role) {
	if (roles_pages[role] < 1) {
		return;
	} else {
		$('#'+role).append('<div id="'+role+'_nav" align="center" class="'+role+'_nav"></div>');
		for (i=0;i<=roles_pages[role];i++) {
			show = i + 1;
			if (i == 0) {
				$('#'+role+'_nav').append('<a id="'+role+'_nav_'+ i +'" onclick="show_role_page(' + i + ',' + role +');" style="font-weight: bold;">' + show + '</a> | ');
			} else {
				$('#'+role+'_nav').append('<a id="'+role+'_nav_'+ i +'" onclick="show_role_page(' + i + ',' + role +');">' + show + '</a> | ');
			}
		}
		$('#'+role+'_nav').append('<a onclick="reset_interval(' + role + ');">&gt;&gt;</a>');
	}	
}

function cycle_role(role) {
	var next_page = roles_current_page[role] + 1;
	if (next_page > roles_pages[role]) {
		next_page = 0;
	}
	$('#'+role+'_' + current_role_page[role]).fadeOut(function() {
		$('#'+role+'_nav_' + next_page).css('font-weight','bold');
		$('#'+role+'_nav_' + current_role_page[role]).css('font-weight','');
		$('#' + role + '_' + next_page).fadeIn(function() {
			current_role_page[role] = next_page;
		});
	});
}

function show_role_page(page,role) {
	clearInterval(roles_cycle_proc[role]);
	roles_cycle_proc[role] = undefined;
	for (i=0;i<roles_counter[role];i++) {
		if ($('#' + role + '_' + i).css("opacity") == 1) {
			$('#' + role + '_' + i).fadeOut(function() {
				$('#' + role + '_nav_' + page).css('font-weight','bold');
				$('#' + role + '_nav_' + i).css('font-weight','');
				$('#' + role + '_' + page).fadeIn(function() {
					current_role_page[role] = page;	
				});
			});
		}	
	}
}

function reset_interval(role) {
	if (role_cycle_proc[role] == undefined) {
		role_cycle_proc[role] = window.setInterval(function() {cycle_role(role)},refresh_interval);
	}
}



