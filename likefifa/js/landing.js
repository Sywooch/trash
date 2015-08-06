$(function() {
	var generateCounter = function() {
		var startHours = 376284,
		startPeople = 300,
		peoplePerHour = 1,
		hours = parseInt((new Date()).getTime() / 1000 / 60 / 60) - startHours,

		people = (startPeople + hours * peoplePerHour).toString(),
		peopleFormatted = '', i;

		if (people.length < 4) {
			for (i = 0; i < (4 - people.length); i++) {
				people = '0'+people;
			}
		}

		for (i = 0; i < people.length; i++) {
			peopleFormatted += '<span>' + people.charAt(i) + '</span>';
		}
		$('.count-people').html(peopleFormatted);
	};

//	generateCounter();

	$('#form-landing-reg').ready(function() {
		document.getElementById("LfMaster_fullName").focus();
	});
	
	$('#form-landing-reg').click(function() {
		$(this).closest('form').submit();
	});
});