$( document ).ready(function() {

	// For small screens:
	
	if ($(window).width() < 800) {
		// Hide menus, add buttons to make menus visible
		$("#show-menu").show();
		$("#main-menu").hide();
		$("#user-menu").hide();
		$("#portal-menu").hide();

		// Hide expandable text
		$('.expand').wrapInner('<a href="" class="toggle-text"></a>');
		$('.expand').nextUntil('.expand').hide();
	}

	// Link som viser menyen dersom den er skjult
	$("#show-menu-link").click(function(event) {
		$("#main-menu").toggle();
		$("#portal-menu").toggle();

		event.preventDefault();
	});

	// Make expandable text toggle on and off when clicked
	$(".expand .toggle-text").click(function(event) {
		event.preventDefault();
		$(this).parent().nextUntil('.expand').toggle();
	});


	// Link som viser brukarmenyen dersom den er skjult
	$("#show-user-menu-link").click(function(event) {
		$("#user-menu").toggle();
		event.preventDefault();
	});

	// Ved resizing av vinduet må vi sjekke om vi passerer stor-liten-grensa
	// og fikse menyen...
	$(window).resize(function(event) {
		if ($(window).width() < 800) {
			$("#main-menu").hide();
			$("#show-menu").show();
			$("#user-menu").hide();
			$("#portal-menu").hide();

			// Hide expandable text
			$('.expand').wrapInner('<a href="" class="toggle-text"></a>');
			$('.expand').nextUntil('.expand').hide();
		}
		else if ($(window).width() >= 800) {
			$("#main-menu").show();
			$("#show-menu").hide();
			$("#user-menu").show();
			$("#portal-menu").show();

			// Hide expandable text
			$('.expand > .toggle-text').contents().unwrap();
			$('.expand').nextUntil('.expand').show();
		}
	});

	// THIS IS A TEST
	$('input[type="checkbox"]').change(function() {
		// Do stuff
	});

 });