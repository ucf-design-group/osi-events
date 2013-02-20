function retrieveEvents() {

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
	
	jQuery(xml).find("event").each(function() {

		var string = "Title: " + jQuery(this).find("title").text() + "<br />"
			+ "Description: " + jQuery(this).find("description").text() + "<br />"
			+ "Start UTC: " + jQuery(this).find("start").text() + "<br />"
			+ "End UTC: " + jQuery(this).find("end").text() + "<br />"
			+ "Contact: " + jQuery(this).find("contact").text() + "<br />"
			+ "URL: " + jQuery(this).find("url").text() + "<br />"
			+ "Notes: " + jQuery(this).find("notes").text() + "<br /><br />";

		jQuery("#here").append(string);
	});

}