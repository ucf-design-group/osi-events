var vucf = "http://osi.ucf.edu/testing/vucf/";

function retrieveEvents(key, start, end, from) {

	var urlext = "wp-content/plugins/osi-events/retriever.php";

	jQuery.ajax({
		type: "GET",
		data: {key: key, start: start, end: end},
		dataType: "html",
		url: window[from] + urlext,

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

	addEventsTwo(events);
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

function addEventsTwo(events) {  // Alternative method

	var here = jQuery("#here");

	for (var k = 0; k < events.length; k++) { // There may be a problem with this iterator variable... any variable other than i doesn't work, and explicitly declaring anything breaks it.

		var start = events[k]['start'];
		var done = 0;
		//alert(start);
		
		jQuery(here).find(".event").each(function() {
			if (jQuery(this).attr('id') > start && done == 0) {
				jQuery(this).before('<div class="event" id="' + events[k]['start'] + '"><div class="title">' + events[k]['title'] + '</div></div>');
				done = 1;
			}
		});

		if (done == 0) {
			jQuery(here).append('<div class="event" id="' + events[k]['start'] + '"><div class="title">' + events[k]['title'] + '</div></div>');
			done = 1;
		}
	}
}