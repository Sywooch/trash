$(document).ready(function () {
		$(".api_clinic-autocomplete").autocomplete({
			minLength: 2,
			source: "/2.0/ApiClinic/autocomplete/",
			select: function (event, ui) {
				$("#clinicId").val(ui.item.id);
			}
		});
		if ($.fn.mask !== undefined) {
			$(".js-mask-phone").mask("+7 ?(999) 999-99-99");
		}
	}
);
