load();

var roles = new Array();
var roles_current_page = new Array();
var roles_counter = new Array();
var roles_cycle_proc = new Array();
var roles_pages = new Array();
var current_role_page = new Array();
var signed_in = new Array();

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

function switch_roles(inrole) {
	for (i=0;i<roles.length;i++) {
		role = roles[i].trim();
		$('#role_' + role).removeClass("role_selected");
	}
	$("#role_" + inrole).addClass("role_selected");
	id = $("input#person_id").val();	
	$.post("../staff/staff_action.php", { "action": "role", "id": id, "role": inrole } );
//	$.post("staff/staff_action.php", { "action": "role", "id": id, "role": role } );
}

$(document).ready(function() {
        var hash = window.location.hash;
        if (hash) {
                reveal(hash);
        }
/*
	FIXME
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

	for (i=0;i<roles.length;i++) {
		role = roles[i].trim();
		append_roles_nav(role);
		//FIXME This doesn't seem to accept the multiple roles. Potential the same problem as the other fixme! 
		if (roles_pages[role] > 1) {
//			roles_cycle_proc[role] = window.setInterval(function() {cycle_role(role)},refresh_interval);
		}
	}

// CODE TO Remove pending fix above!	
	if (roles_pages["staff"] > 1) {
		roles_cycle_proc["staff"] = window.setInterval(function() {cycle_role("staff")},refresh_interval);
	}
	if (roles_pages["visitor"] > 1) {
		roles_cycle_proc["visitor"] = window.setInterval(function() {cycle_role("visitor")},refresh_interval);
	}
	if (roles_pages["startup"] > 1) {
		roles_cycle_proc["startup"] = window.setInterval(function() {cycle_role("startup")},refresh_interval);
	}
//	console.log("proc visitor = " + roles_cycle_proc["visitor"] + " <end> ");
//	console.log("proc staff = " + roles_cycle_proc["staff"] + " <end> ");
}


function signin_person(id,firstname,lastname,email,role) {
	// The place to sort staff (optional)
	append_person(id,firstname,lastname,email,role);
}

function append_person(id,firstname,lastname,email,role) {
	if (signed_in[email]) {
		return;
	} 
	role = role.trim();
	roles_current_page[role] = 0;
	current_node = role + "_" + roles_current_page[roles[i]];
	//Cycle process id
	roles_cycle_proc[roles[i]] = undefined;
	if (roles_counter[role] == 0 || (roles_counter[role] % peoplepp) == 0) {
		current_node = role + "_" + roles_pages[role];
		if (roles_pages[role] < 1) {
			$('#' + role).append('<div id="' + current_node + '" class="people"></div>');
		} else {
			$('#' + role).append('<div id="' + current_node + '" style="display: none;" class="people allonsite"></div>');
		}
		roles_pages[role]++;
	}
	current_node = role + "_" + (roles_pages[role] - 1);
	$('#' + current_node).append('<div class="person" style="display: inline-block;"><a href="individual/?id='+id+'"><img class="people_pic" src="photo.php?id='+id+'"/></a>'+firstname + " " + lastname + '</div>');	
	roles_counter[role]++;
	signed_in[email] = true;
}

function append_roles_nav(role) {
	role = role.trim();
	if (roles_pages[role] < 2) {
		return;
	} else {
		$('#'+role).append('<div id="'+role+'_pages" align="center" class="pages_nav"></div>');
		for (i=0;i<roles_pages[role];i++) {
			show = i + 1;
			if (i == 0) {
				$('#'+role+'_pages').append('<a id="'+role+'_pages_'+ i +'" onclick="show_role_page(' + i + ',\''+role+'\');" style="font-weight: bold;">' + show + '</a> | ');
			} else {
				$('#'+role+'_pages').append('<a id="'+role+'_pages_'+ i +'" onclick="show_role_page(' + i + ',\''+role+'\');">' + show + '</a> | ');
			}
		}
		$('#'+role+'_pages').append('<a onclick="reset_interval(\'' + role + '\');">&gt;&gt;</a>');
	}	
}

function cycle_role(role) {
	var next_page = current_role_page[role] + 1;
	if (next_page == roles_pages[role]) {
		next_page = 0;
	}
	$('#'+role+'_' + current_role_page[role]).fadeOut(function() {
		$('#'+role+'_pages_' + next_page).css('font-weight','bold');
		$('#'+role+'_pages_' + current_role_page[role]).css('font-weight','');
		$('#' + role + '_' + next_page).fadeIn(function() {
			current_role_page[role] = next_page;
		});
	});
}

function show_role_page(page,role) {
	role = role.trim();
	clearInterval(roles_cycle_proc[role]);
	roles_cycle_proc[role] = undefined;
	for (i=0;i<roles_counter[role];i++) {
		$('#' + role + '_pages_' + i).css('font-weight','');
		if ($('#' + role + '_' + i).css("opacity") == 1) {
			$('#' + role + '_' + i).fadeOut(function() {
				$('#' + role + '_pages_' + page).css('font-weight','bold');
				$('#' + role + '_' + page).fadeIn(function() {
					current_role_page[role] = page;	
				});
			});
		}	
	}
}

function reset_interval(role) {
	if (roles_cycle_proc[role] == undefined) {
		roles_cycle_proc[role] = window.setInterval(function() {cycle_role(role)},refresh_interval);
	}
}



