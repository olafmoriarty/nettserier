$( document ).ready(function() {

	var menySynlig = true;
	var menyStor = true;

	// For small screens:
	
	if ($(window).width() < 800) {
		// Hide menus, add buttons to make menus visible
		$("#main-menu").removeClass("main-menu").addClass("invisible");
		$("#show-menu").removeClass("invisible").addClass("show-menu");
		$("#user-menu").addClass("invisible");
		menySynlig = false;
		menyStor = false;
		userMenuVisible = false;

		// Hide expandable text
		$('.expand').wrapInner('<a href="" class="toggle-text"></a>');
		$('.expand').nextUntil('.expand').hide();
	}

	// Link som viser menyen dersom den er skjult
	$("#show-menu-link").click(function(event) {
		if (!menySynlig) {
			$("#main-menu").removeClass("invisible").addClass("main-menu");
			menySynlig = true;
		}
		else {
			$("#main-menu").removeClass("main-menu").addClass("invisible");
			menySynlig = false;
		}

		event.preventDefault();
	});

	// Make expandable text toggle on and off when clicked
	$(".expand .toggle-text").click(function(event) {
		event.preventDefault();
		$(this).parent().nextUntil('.expand').toggle();
	});


	// Link som viser brukarmenyen dersom den er skjult
	$("#show-user-menu-link").click(function(event) {
		if (!userMenuVisible) {
			$("#user-menu").removeClass("invisible");
			userMenuVisible = true;
		}
		else {
			$("#user-menu").addClass("invisible");
			userMenuVisible = false;
		}

		event.preventDefault();
	});

	// Ved resizing av vinduet må vi sjekke om vi passerer stor-liten-grensa
	// og fikse menyen...
	$(window).resize(function(event) {
		if (menyStor && $(window).width() < 800) {
			$("#main-menu").removeClass("main-menu").addClass("invisible");
			$("#show-menu").removeClass("invisible").addClass("show-menu");
			menyStor = false;
		}
		else if (!menyStor && $(window).width() >= 800) {
			$("#main-menu").removeClass("invisible").addClass("main-menu");
			$("#show-menu").removeClass("show-menu").addClass("invisible");
			menyStor = true;
		}
	});

 });
