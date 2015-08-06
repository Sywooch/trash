$(document).ready(function () {
	var $autocomplete = $(".api_doctor-autocomplete");
	$autocomplete.autocomplete({
		minLength: 2,
		source: "/2.0/ApiDoctor/autocomplete/?clinicId=" + $autocomplete.data("clinicid"),
		select: function (event, ui) {
			$("#doctorId").val(ui.item.id);
			$("#clinicId").val(ui.item.clinicId);
		}
	});
});
