$(document).ready(function() {
	$(function(){
		$.datepicker.setDefaults($.extend($.datepicker.regional["ru"]));
		$("#crDateShFrom").datepicker( {
			changeMonth : true,
			changeYear: true,
			duration : "fast",
			maxDate : "+1y",
			showButtonPanel: true
		});
		$("#crDateShTill").datepicker( {
			changeMonth : true,
			changeYear: true,
			duration : "fast",
			maxDate : "+1y",

			showButtonPanel: true
		});
	});
})

