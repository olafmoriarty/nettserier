$( document ).ready(function() {

	var menySynlig = true;
	var menyStor = true;

	// For små skjermer: Skjul menyen ved lasting av sida
	
	if ($(window).width() < 800) {
		$("#main-menu").removeClass("main-menu").addClass("invisible");
		$("#show-menu").removeClass("invisible").addClass("show-menu");
		menySynlig = false;
		menyStor = false;
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

	// Ved resizing av vinduet må vi sjekke om vi passerer stor-liten-grensa
	// og fikse menyen...
	$(window).resize(function(event) {
		if (menyStor && $(window).width() < 800) {
			$("#main-menu").removeClass("main-menu").addClass("invisible3");
//			$("#main-menu").hide();
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
