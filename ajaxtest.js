function retrieveEvents(key, start, end) {

	jQuery.ajax({
		type: "GET",
		data: {key: "1234", start: "0", end: "200000000000000000"},
		dataType: "html",
		url: "retriever.php",

		beforeSend: function() {
		},
		error: function(jqXHR, textStatus, errorThrown) {
			alert("Uh oh.");
		},

		success: parseXML
	})
}

function parseXML(xml) {

	var events = new Array();
	
	jQuery(xml).find("event").each(function() {

		var singleEvent = new Array();

		singleEvent['title'] = jQuery(this).find("title").text();
		singleEvent['description'] = jQuery(this).find("description").text();
		singleEvent['start'] = jQuery(this).find("start").text();
		singleEvent['end'] = jQuery(this).find("end").text();
		singleEvent['contact'] = jQuery(this).find("contact").text();
		singleEvent['url'] = jQuery(this).find("url").text();
		singleEvent['notes'] = jQuery(this).find("notes").text();

		singleEvent['start'] = parseInt(singleEvent['start']);

		if (singleEvent['end'] == "none")
			singleEvent['end'] = -1;
		else
			singleEvent['end'] = parseInt(singleEvent['end']);

		events.push(singleEvent);

		/*var string = "Title: " + jQuery(this).find("title").text() + "<br />"
			+ "Description: " + jQuery(this).find("description").text() + "<br />"
			+ "Start UTC: " + jQuery(this).find("start").text() + "<br />"
			+ "End UTC: " + jQuery(this).find("end").text() + "<br />"
			+ "Contact: " + jQuery(this).find("contact").text() + "<br />"
			+ "URL: " + jQuery(this).find("url").text() + "<br />"
			+ "Notes: " + jQuery(this).find("notes").text() + "<br /><br />";

		jQuery("#here").append(string);*/
	});

	/*for (var i = 0; i < events.length; i++) {
		for (value in events[i]) {
			jQuery("#here").append(value + " => " + events[i][value] + "<br>");
		}
		jQuery("#here").append("<br>");
	}*/

	addEvents2(events);
}

function addEvents(events) {  // Current working: add all events occurring before those on the page, and then remove them from the list

	var here = jQuery("#here");
	var onPage = new Array();

	here.find(".event").each(function() {
		var eventInfo = new Array();
		eventInfo['start'] = parseInt(jQuery(this).attr("id"));
		eventInfo['title'] = jQuery(this).find(".title").text();
		onPage.push(eventInfo);
	});

	var eventsOnPageRemaining = onPage.length;
	for (var i = 0; i < eventsOnPageRemaining; i++) {
		var evt = onPage[i];
		for (var j = 0; j < events.length; j++) {
			var newEvt = events[0];
			//alert(newEvt['start'] + " " + newEvt['title'] + " " + evt['start'] + " " + evt['title']);
			if (newEvt['start'] < evt['start'] && newEvt['title'] != evt['title']) {
				jQuery("#" + evt['start']).before('<div class="event" id="' + newEvt['start'] + '"><div class="title">' + newEvt['title'] + '</div></div>');
				events.splice(0, 1);
			}
		}
	}

	var eventsRemaining = events.length;
	for (var i = 0; i < eventsRemaining; i++) {
		var newEvt = events[0];
		//alert(events[0]['start']);
		jQuery("#here").append('<div class="event" id="' + newEvt['start'] + '"><div class="title">' + newEvt['title'] + '</div></div>');
		events.splice(0, 1);
	}
}

function addEvents2(events) {   // Possible alternative: look for the place to put each event, one by one.

	var here = jQuery("#here");

	for (int i = 0; i < events.length; i++) {
		var start = events[i]['start'];
		alert(start);
	}
}