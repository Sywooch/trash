var onlineDiagnModal = null;

/**
 * отправка события видимости кнопки
 */
function checkButtonsViewable()
{
	if (!onlineDiagnModal) {
		return;
	}

	$(onlineDiagnModal).each(function() {
		if ($(this).data('view-checked') == 1) {
			return;
		}

		var o = $(this).offset();
		var scrollTop = window.pageYOffset || document.documentElement.scrollTop;

		//верх виджета ушел под скролл
		if (o.top < scrollTop + document.documentElement.clientHeight) {
			if (typeof ga !== 'undefined') {
				$(this).data('view-checked', 1);
				ga('send', 'event', 'Form button', 'view');
			} else  {
				setTimeout(checkButtonsViewable, 300);
			}
		}
	});
}

$.bindGA = function(){
	
	$("#url-short-form").click(function(){
		ga('send', 'event', 'klinika', 'click', 'perehod na sait iz kratkoi anketi');
	});
	
	$("#url-full-form").click(function(){
		ga('send', 'event', 'klinika', 'click', 'full');
	});
	
	$("#logo-full-form").click(function(){
		ga('send', 'event', 'klinika', 'click', 'image');
	});
	
	$("#clinic-full-form").click(function(){
		ga('send', 'event', 'klinika', 'click', 'name');
	});


}

$(document).ready(function(){
	$.bindGA();
	onlineDiagnModal = $(".online-diagnostics-open-modal");
	checkButtonsViewable();
});

$(window).bind('scroll', function() {
	checkButtonsViewable();
});