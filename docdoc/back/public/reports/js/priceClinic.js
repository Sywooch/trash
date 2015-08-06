function exportData () {
	var clinicIds = [];
	var selectedClinics = $(".clinicSelector:checked");
	$.each(selectedClinics, function() {
		clinicIds.push($(this).attr('data-id'));
	});
	$("#clinicIds").val(clinicIds.join(','));
	document.forms['data'].action = "/reports/service/exportPriceClinicServices.htm";
	document.forms['data'].submit();
}

$(document).ready(function() {
	$(".allSelect").click(function(){
		var clinicSelector = $(".clinicSelector");
		if ($(this).attr('checked')) {
			clinicSelector.attr('checked', true);
		} else {
			clinicSelector.removeAttr('checked');
		}
	});
});