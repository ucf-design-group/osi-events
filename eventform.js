
jQuery(function() {
	jQuery( "#oe-form-startdate" ).datepicker({ dateFormat: "yy-mm-dd" });
	jQuery( "#oe-form-enddate" ).datepicker({ dateFormat: "yy-mm-dd" });
});

// Javascript for oe META Box

var dateCheck = new RegExp('^20[0-9]{2}-(0[1-9]|1[012])-(0[1-9]|[12][0-9]|3[01])$');
var timeCheck = new RegExp('^((1[012]|[1-9])(:[0-5][0-9])?)$');
var colonCheck = new RegExp(':');
var getHour = new RegExp('^[0-9]{1,2}');
var getMinutes = new RegExp('[0-9]{1,2}$');

function startDateCheck() {

	var startDate = document.getElementById('oe-form-startdate');
	var endDate = document.getElementById('oe-form-enddate');

	if (!dateCheck.test(startDate.value)) {
		startDate.style.backgroundColor = '#FF9999';
	}
	else {
		startDate.style.backgroundColor = '#FFFFFF';
		endDate.value = startDate.value;
	}
}

function startTimeCheck() {

	var startTime = document.getElementById('oe-form-starttime');
	var endTime = document.getElementById('oe-form-endtime');

	if (!timeCheck.test(startTime.value)) {
		startTime.style.backgroundColor = '#FF9999';
	}
	else {
		startTime.style.backgroundColor = '#FFFFFF';

		var minutes;

		if (colonCheck.test(startTime.value)) {
			minutes = getMinutes.exec(startTime.value);
		}
		else {
			minutes = "00";
		}

		startTime.value = hour + ":" + minutes;
	}
}

function endUpdate() {

	/*var startDate = document.getElementById('oe-form-startdate');
	var startTime = document.getElementById('oe-form-starttime');
	var startAmPm = document.getElementById('oe-form-startampm');
	var endDate = document.getElementById('oe-form-enddate');
	var endTime = document.getElementById('oe-form-endtime');
	var endAmPm = document.getElementById('oe-form-endampm');
	
	var minutes;
	var newEndHour;

	var hour = getHour.exec(startTime.value);

	if (hour == "12") {
		newEndHour = "1";
	}
	else {
		newEndHour = parseInt(hour,10) + 1;
	}*/
}

function endDateCheck() {

	var endDate = document.getElementById('oe-form-enddate');

	if (!dateCheck.test(endDate.value)) {
		endDate.style.backgroundColor = '#FF9999';
	}
	else {
		endDate.style.backgroundColor = '#FFFFFF';
	}
}

function endCheckBox() {

	var checkBox = document.getElementById('oe-form-endcheck');
	var endDate = document.getElementById('oe-form-enddate');
	var endTime = document.getElementById('oe-form-endtime');
	var endAmPm = document.getElementById('oe-form-endampm');

	if (checkBox.checked) {
		endDate.disabled = false;
		endDate.style.backgroundColor = '#FFFFFF';
		
		endTime.disabled = false;
		endTime.style.backgroundColor = '#FFFFFF';
		
		endAmPm.disabled = false;
		endAmPm.style.backgroundColor = '#FFFFFF';
	}
	else {
		endDate.disabled = true;
		endDate.style.backgroundColor = '#EEEEEE';
		
		endTime.disabled = true;
		endTime.style.backgroundColor = '#EEEEEE';
		
		endAmPm.disabled = true;
		endAmPm.style.backgroundColor = '#EEEEEE';
	}
}