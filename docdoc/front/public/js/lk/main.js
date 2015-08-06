
var drawCharts = function () {
	Highcharts.getOptions().colors = Highcharts.map(Highcharts.getOptions().colors, function (color) {
		return Highcharts.Color(color)
			.setOpacity(0.5)
			.get('rgba');
	});


	var chart2 = new Highcharts.Chart({

		chart: {
			renderTo: 'showmemagic',
			type: 'column'
		},
		plotOptions: {
			column: {
				groupPadding: 0.2,
				borderWidth: 1,
				shadow: false,
				grouping: true,
				dataLabels: {
					enabled: true,
					formatter: function () {
						return this.y + '%';
					}
				}
			},
			series: {
				fillOpacity: 1
			}
		},
		title: {
			text: 'Пациенты'
		},
		subtitle: {
			text: 'Конверсия в переведеных/записаных/дошедших'
		},
		xAxis: {
			categories: ['Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь']
		},
		yAxis: {
			title: {
				text: 'Процент от обратившихся'
			},
			max: 100,
			stackLabels: {
				enabled: true,
				formatter: function () {
					return this.total + "%";
				}
			},
			labels: {
				formatter: function () {
					return this.value + '%';
				}
			}

		},
		legend: {
			layout: 'vertical',
			backgroundColor: '#FFFFFF',
			align: 'left',
			verticalAlign: 'top',
			x: 170,
			y: 0,
			floating: true,
			shadow: true
		},
		series: [{
			type: 'column',
			name: 'Переведенных',
			data: [95, 92, 88, 94, 97],
			color: '#33cc33'
		}, {
			type: 'column',
			name: 'Записавшихся',
			data: [82, 88, 70, 79, 84],
			color: '#ffcc99'
		}, {
			type: 'column',
			name: 'Дошедших',
			data: [35, 24, 28, 22, 30],
			color: '#99ccff'
		}]
	});

	/*
	 options.series[0].data = allVisits;
	 options.series[1].data = newVisitors;

	 chart2 = new Highcharts.Chart(options);
	 */

	var chart3 = new Highcharts.Chart({
		chart: {
			renderTo: 'showmemagic2',
			type: 'line',
			marginRight: 130,
			marginBottom: 25
		},
		title: {
			text: 'Заявки',
			x: -20 //center
		},
		subtitle: {
			text: 'График обращений/переведенных/записанных/дошедших',
			x: -20
		},
		xAxis: {
			categories: ['Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь']
		},
		yAxis: {
			title: {
				text: 'Кол-во'
			},
			plotLines: [{
				value: 0,
				width: 1,
				color: '#808080'
			}]
		},
		legend: {
			layout: 'vertical',
			align: 'right',
			verticalAlign: 'top',
			x: -10,
			y: 100,
			borderWidth: 0
		},
		series: [{
			name: 'Всего обращений',
			data: [120, 110, 170, 210, 400]
		}, {
			name: 'Переведенных',
			data: [105, 90, 150, 180, 340]
		}, {
			name: 'Записанных',
			data: [100, 80, 130, 140, 310]
		}, {
			name: 'Дошедших',
			data: [90, 70, 100, 90, 280]
		}]
	});


	var chart4 = new Highcharts.Chart({
		chart: {
			renderTo: 'showmemagic3',
			type: 'bar',
			marginRight: 130,
			marginBottom: 25
		},
		title: {
			text: 'Заявки',
			x: -20 //center
		},
		subtitle: {
			text: 'Популярность специальностей',
			x: -20
		},
		xAxis: {
			categories: ['Массажист', 'Проктолог', 'Иммунолог', 'Гендальф', 'Василий']
		},
		yAxis: {
			title: {
				text: 'Кол-во'
			},
			plotLines: [{
				value: 0,
				width: 1,
				color: '#808080'
			}]
		},
		legend: {
			layout: 'vertical',
			align: 'right',
			verticalAlign: 'top',
			x: -10,
			y: 100,
			borderWidth: 0
		},
		series: [{
			name: 'Всего обращений',
			data: [120, 110, 170, 210, 400]
		}, {
			name: 'Переведенных',
			data: [105, 90, 150, 180, 340]
		}, {
			name: 'Записанных',
			data: [100, 80, 130, 140, 310]
		}, {
			name: 'Дошедших',
			data: [90, 70, 100, 90, 280]
		}]
	});
};
$(document).ready(function () {
	if ($('.highcharts').length > 0) {
		drawCharts();
	}
});


$(document).ready(function () {
	initDropDowns();
	tooltipsInit();
	initRecords();
});

/* tooltip */

var tooltip = {
	current: null,
	delay: 600,
	timer: null,
	setDialogs: function () {
		//Determine dialog positions
	}
}

var tooltipsInit = function (container) {

	container = container || 'body';

	$('.js-tooltip-tr', container).each(function () {
		var $me = $(this);
		if ($me.data("initDone") != "true") {
			$me.data("initDone", "true");
			$me.hover(function (event) {
				var titleText = $(this).attr('title');
				$(this)
					.data('tipText', titleText)
					.removeAttr('title');
				$('<p class="js-tooltip ui-bg-yellow"></p>')
					.text(titleText)
					.appendTo('body')
					.css('top', (event.pageY - 10) + 'px')
					.css('left', (event.pageX + 20) + 'px')
					.fadeIn('slow');

			}, function () {
				$(this).attr('title', $(this).data('tipText'));
				$('.js-tooltip').remove();
			}).mousemove(function (event) {
				$('.js-tooltip')
					.css('top', (event.pageY - 10) + 'px')
					.css('left', (event.pageX + 20) + 'px');
			});
		}
	});
}
/* tooltip end */

/* dropdown script */

function initDropDowns() {

	$('.b-dropdown').each(function (i) {
		var $el = $(this);
		//var jsDropdownData = $.parseJSON($el.children('.b-dropdown_data').text());

		// buildDropdown(jsDropdownData, $el );

	});


	$('.b-dropdown_list .b-dropdown_item').click(function () {

			var $clickedItem = $(this),
				$wrapper = $clickedItem.closest('.b-dropdown'),
				$currentItem = $(".b-dropdown_item__current", $wrapper);
			$currentItemText = $(".b-dropdown_item__text", $wrapper);

			//$currentItem.html($clickedItem.html());
			$currentItemText.html($clickedItem.text());
			$('.b-dropdown_item.s-current', $wrapper).removeClass('s-current');
			$clickedItem.addClass('s-current');

			$('.b-dropdown_list', $wrapper).hide();
			$wrapper.removeClass("s-open");

		}
	);

	$(".b-dropdown_form").each(function () {
		var $wrapper = $(this).closest('.b-dropdown');
		$('.b-dropdown_list .b-dropdown_item', $wrapper).click(function () {
			var $clickedItem = $(this);
			var $clickedItemValue = $clickedItem.attr("data-clinicid");
			$(".b-dropdown_input", $wrapper).val($clickedItemValue);
			$(".b-dropdown_form", $wrapper).submit();
		});
	});

	$('body').unbind('click.dropdown').bind('click.dropdown', function (evt) {
		$('.b-dropdown_list').hide();
		$(".b-dropdown").removeClass("s-open");
	});

	$('.b-dropdown_item__current').click(function (evt) {
			evt.stopPropagation();
			var $wrapper = $(this).closest('.b-dropdown');
			var $dropdownList = $('.b-dropdown_list', $wrapper);

			if (($dropdownList).is(":visible")) {
				$dropdownList.hide();
				$wrapper.removeClass("s-open");
			}
			else {
				$wrapper.addClass("s-open");
				$dropdownList.show();
			}

		}
	);

	// select by hashtag
	//if ( window.location.hash != '' ) {
	//    $('.b-dropdown_item').filter(function(){
	//        return $(this).data('anything')==window.location.hash.replace('#','');
	//    }).click();
	//}
}


function buildDropdown(data, $domElement) {
	var $liTemplate = $('.b-dropdown_list__li-template .b-dropdown_item', $domElement),
		$ul = $('.b-dropdown_list', $domElement),
		$newLi = null,
		first = true,
		$currentItem = $(".b-dropdown_item__text", $domElement);

	for (var i in data) {
		if (typeof( data[i]['cityname'] ) == 'undefined') {
			continue;
		}
		var cityname = data[i].cityname,

			$newLi = $liTemplate.clone();
		//$newLi.data("anything", data[i].anything);
		//$newLi.html(data[i].cityname);
		$newLi.attr("data-cityid", data[i].cityid);
		$newLi.html(data[i].cityname);

		//placing first item as default
		if (first) {
			$currentItem.html($newLi.html());
			$newLi.addClass('s-current');
		}

		$newLi.appendTo($ul);

		first = false;
	}
}


/* dropdown markup end */
/*
 $(document).ready(function(){
 if (!Modernizr.inputtypes.date) {
 $('input[type=date]').datepicker({
 dateFormat: 'dd-mm-yy',
 autoSize: true,
 numberOfMonths: 3,
 showButtonPanel: true
 });
 }
 });*/
$(document).ready(function () {

	$('.patients .datepicker').datepicker({
		dateFormat: 'dd.mm.yy',
		autoSize: true,
		numberOfMonths: 3,
		maxDate: 0,
		showButtonPanel: true
	});

	$('.reports .datepicker').datepicker({
		dateFormat: 'dd.mm.yy',
		autoSize: true,
		numberOfMonths: 3,
		maxDate: 0,
		showButtonPanel: true
	});

	$('.chart_form .monthpicker').datepicker({
		dateFormat: 'dd.mm.yy',
		autoSize: true,
		numberOfMonths: 3,
		maxDate: 0,
		showButtonPanel: true
	});

	$('.lk_input__date').datepicker({
		dateFormat: 'dd.mm.yy',
		autoSize: true,
		numberOfMonths: 1,
		minDate: 0,
		showButtonPanel: true
	});


	/*
	 $('.chart_form .monthpicker').monthpicker( {
	 changeMonth: true,
	 changeYear: true,
	 showButtonPanel: true,
	 dateFormat: 'MM yy',
	 onClose: function(dateText, inst) {
	 var month = $(" .ui-datepicker-month :selected").val();
	 var year = $(" .ui-datepicker-year :selected").val();
	 $(this).datepicker('setDate', new Date(year, month, 1));
	 }
	 });
	 */

	$.datepicker.regional['ru'] = {
		closeText: 'Закрыть',
		prevText: '&#x3c;Пред',
		nextText: 'След&#x3e;',
		currentText: 'Сегодня',
		monthNames: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь',
			'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
		monthNamesShort: ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн',
			'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'],
		dayNames: ['воскресенье', 'понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота'],
		dayNamesShort: ['вск', 'пнд', 'втр', 'срд', 'чтв', 'птн', 'сбт'],
		dayNamesMin: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
		dateFormat: 'dd.mm.yy',
		firstDay: 1,
		isRTL: false
	};
	$.datepicker.setDefaults($.datepicker.regional['ru']);


});

$(document).ready(function () {
	$('.filter_state__label').click(function () {
		var $label = $("#" + $(this).attr("for"));
		var $form = $(this).closest('.result_filters__form');
		if ($label.attr("checked") == "checked") {
			$label.prop('checked', false);
		}
		else {
			$label.attr("checked", "checked");
		}
		$form.submit();
	});
});

$(function () {

	$(".autocomplete_docspec").autocomplete({
		source: "/lk/service/getSpecList.php",
		minLength: 0,
		select: function (event, ui) {
			$(this).val(ui.item.label);
			$(this).closest("form").submit();
		}
	});

	$(".link_dropdown__docspec").click(function () {
		var $input = $(".autocomplete_docspec");

		$input.focus();

		wasOpen = $input.autocomplete("widget").is(":visible");
		if (wasOpen) {
			return;
		}

		$input.autocomplete("search", "");
	});

});


$(function () {

	$(".autocomplete_docname").autocomplete({
			source: function (request, response) {
				$.ajax({
					url: '/lk/service/getDoctorList.php',
					dataType: "json",
					data: {
						term: request.term,
						clinicId: $('.b-dropdown_list .b-dropdown_item.s-current').attr("data-clinicid")
					},
					success: function (data) {
						response(data);
					}
				});
			},
			minLength: 3,
			select: function (event, ui) {
				$(this).val(ui.item.label);
				$(this).closest("form").submit();
			}
		}
	);

	$(document).ready(function () {

		$(".reports_tabs").tabs();

		if ($(".filter_sort").length > 0) {
			$(".result_table__sort").click(function () {
				var sortBy = $(this).attr("data-sortby");
				var sortType = $(this).attr("data-sorttype");

				var $sortByInput = $(".filter_sort__input[name=sortBy]");
				var $sortTypeInput = $(".filter_sort__input[name=sortType]");

				if (sortBy == $sortByInput.val()) {

					if (sortType == "asc" && sortType == $sortTypeInput.val()) {
						sortType = "desc";

					}
					else if (sortType == "desc" && sortType == $sortTypeInput.val()) {
						sortType = "asc";
					}

					$sortTypeInput.val(sortType);

				}
				else if (sortBy != $sortByInput.val()) {
					$sortTypeInput.val(sortType);
				}

				$sortByInput.val(sortBy);
				$(".result_filters__form").submit();

			});
		}

	});

	$(document).ready(function () {

		if ($(".pager_item").length > 1) {
			$(".pager_link").click(function () {
				var pageStart = $(this).attr("data-pagerstart");
				$(".filter_pager__input[name=startPage]").val(pageStart);

				$(".result_filters__form").submit();

			});
		}

	});
});


$(document).ready(function () {
	$(".page_help").click(function () {
		$(this).children(".page_help__desc").fadeToggle();
		$(this).children(".page_help__desc").mouseout(function () {
			$(this).fadeOut()
		});
	});

	$(".result_filters__reset").click(function () {
		var $this = $(this);
		var $form = $this.closest("form");
		$("input[type=text], input[type=hidden], input[type=radio]", $form).val("");
		$form.submit();
		return false;
	});

	$(".filter_date__hotlinks-link").click(function () {
		$(".filter_date__hotlinks-link").removeClass('s-active');
		var $this = $(this);
		$this.addClass('s-active');
		var mothBegin = $this.attr("data-monthbegin");
		var monthEnd = $this.attr("data-monthend");

		var $crDateFromInput = $("input[name=crDateFrom]");
		var $crDateTillInput = $("input[name=crDateTill]");

		$crDateFromInput.val(mothBegin);
		$crDateTillInput.val(monthEnd);

	});
});

var initDateInputs = function () {

	/* we check here if date inputs values matches date hotlink values, and if so - make matched link as active (and vice versa) */
	var dateInputs = $('.filter_date__input');
	var $crDateFromInput = $("input[name=crDateFrom]");
	var $crDateTillInput = $("input[name=crDateTill]");

	var startDates = [];
	var endDates = [];
	$('.filter_date__hotlinks-link').each(function () {
		startDates.push($(this).attr('data-monthbegin'));
		endDates.push($(this).attr('data-monthend'));
	});

	dateInputs.on('change', function () {

		/* check if date start is in any quick link */
		if ($.inArray($crDateFromInput.val(), startDates) > -1) {
			/* check if date end is in that link */

			var i = $.inArray($crDateFromInput.val(), startDates);

			if ($crDateTillInput.val() == endDates[i]) {
				var $hotlinkPosition = ( $.inArray($crDateFromInput.val(), startDates) );

				$('.filter_date__hotlinks-link').removeClass("s-active");
				$('.filter_date__hotlinks-link:eq(' + $hotlinkPosition + ')').addClass("s-active");
			}
			else {
				/* otherwise make all hotlinks inactive */
				$('.filter_date__hotlinks-link').removeClass("s-active");

			}
		}
		else {
			/* otherwise make all hotlinks inactive */
			$('.filter_date__hotlinks-link').removeClass("s-active");

		}

	});

	/* repeating code */
	var flagFirstTime = false;
	if (flagFirstTime != true) {

		var dateInputs = $('.filter_date__input');
		var $crDateFromInput = $("input[name=crDateFrom]");
		var $crDateTillInput = $("input[name=crDateTill]");

		var startDates = [];
		var endDates = [];
		$('.filter_date__hotlinks-link').each(function () {
			startDates.push($(this).attr('data-monthbegin'));
			endDates.push($(this).attr('data-monthend'));
		});

		/* check if date start is in any quick link */
		if ($.inArray($crDateFromInput.val(), startDates) > -1) {
			/* check if date end is in that link */

			var i = $.inArray($crDateFromInput.val(), startDates);

			if ($crDateTillInput.val() == endDates[i]) {
				var $hotlinkPosition = ( $.inArray($crDateFromInput.val(), startDates) );

				$('.filter_date__hotlinks-link').removeClass("s-active");
				$('.filter_date__hotlinks-link:eq(' + $hotlinkPosition + ')').addClass("s-active");
			}
			else {
				/* otherwise make all hotlinks inactive */
				$('.filter_date__hotlinks-link').removeClass("s-active");

			}
		}
		else {
			/* otherwise make all hotlinks inactive */
			$('.filter_date__hotlinks-link').removeClass("s-active");

		}
		flagFirstTime = true;
	}

}

$(document).ready(function () {

	initDateInputs();

});


$(document).ready(function () {


	$(".lk_input__time").on("keyup", function () {
		var $this = $(this);
		var thisVal = $this.val();
		if (thisVal.length > 2) {
			var timeValue = $(this).val();
			var newTimeValue = timeValue.substring(0, 2);
			$(this).val(newTimeValue);
		}

		if (thisVal > 24 && $this.hasClass("hour")) {
			$(this).val(12);
		}

		if (thisVal > 59 && $this.hasClass("minute")) {
			$(this).val(0);
		}

	});

	$(".state_change__popup-close").on("click", function () {
		$(this).closest(".state_change__popup").fadeOut();
		$(".popup_overlay").fadeOut();
	});

	$(".state_change").click(function () {
		if (!$(this).hasClass("s-disabled")) {

			$(".error_generic").remove();
			var $clickedBtn = $(this);
			var $form = $clickedBtn.closest(".state_change__form");
			var reqAction = $clickedBtn.attr("data-reqaction");
			var $formBtns = $form.children(".state_change");
			var $inputAction = $form.children('input[name=reqAction]');


			if ($(".popup_overlay").length == 0) {
				var $popupOverlay = $('<div class="popup_overlay"></div>');
				$popupOverlay.appendTo("body");
			}
			else {
				var $popupOverlay = $(".popup_overlay");
			}

			$popupOverlay.fadeIn(500);
			$popupOverlay.on("click", function () {
				$popupOverlay.fadeOut(500);
				$(".state_change__popup").fadeOut(500);
				$clickedBtn.removeClass("s-disabled");
			});

			var $stateChangePopup = $('.state_change__popup', $form);
			var $declineComment = $(".state_change__declinecomment", $form);


			$stateChangePopup.show();

		}
		;
	});

	$(".state_change__save").click(function () {


		var $clickedBtn = $(this);
		if (!$clickedBtn.hasClass("s-disabled")) {
			var $form = $clickedBtn.closest(".state_change__form");
			var reqAction = $clickedBtn.attr("data-reqaction");

			var $formBtns = $form.children(".state_change");
			var $inputAction = $form.children('input[name=reqAction]');

			if ($clickedBtn.hasClass("state_change__save")) {
				$clickedBtn.addClass("s-disabled")
			}
			else {
				$formBtns.addClass("s-disabled");
			}


			$inputAction.val(reqAction);
			//alert ($form.attr("action")+"?"+$form.serialize());
			var reqStateChange = function () {

				$.ajax({
					type: "POST",
					url: $form.attr("action"),
					data: $form.serialize(), // serializes the form's elements.
					success: function (requestResponse) {
						var flagJsonSuccess;
						try {
							var dataObj = JSON.parse(requestResponse);
							flagJsonSuccess = true;
						}
						catch (e) {
							flagJsonSuccess = false;

							var $generic_error = $('<label class="error_generic">При отправке формы произошла ошибка, пожалуйста, проверьте введённые данные или просто позвоните нам: </label>');
							var phoneNumber = $(".data_phone").text();
							var $phoneNumber = $('<span style="white-space: nowrap;">' + phoneNumber + '</span>');
							$generic_error.append($phoneNumber);

							if ($(".error_generic").length == 0) {
								$generic_error.appendTo(".state_change__submit-ct");
							}
							else if ($(".error_generic").length > 0) {
								$(".error_generic").remove();
								$generic_error.appendTo($form);
							}
							$(".req_form__submit-btn").removeClass("s-disabled");
						}
						if (flagJsonSuccess == true) {
							var dataObj = JSON.parse(requestResponse);

							if (dataObj.status == "success") {
								if (dataObj.url != false && typeof dataObj.url != 'undefined') {
									window.location = dataObj.url;
								}
								else {
									/*
									 if ( reqAction == "approve" ) {
									 showMsgRequestApproved($form);
									 }
									 else if ( reqAction == "decline") {
									 showMsgRequestDeclined($form);
									 }
									 */
									reqChangeState($form, reqAction);
									window.location.reload();

									if ($(".error_generic").length > 0) {
										$(".error_generic").remove();
									}
								}

							}
							else if (dataObj.error != null && dataObj.error != 'undefined') {
								if (dataObj.error == 'Not authorized') {
									window.location = '/lk/auth';
								}
								var $generic_error = $('<label class="error_generic">' + dataObj.error + '</label>');
								if ($(".error_generic").length == 0) {
									$generic_error.appendTo(".state_change__submit-ct");
								}
								else if ($(".error_generic").length > 0) {
									$(".error_generic").remove();
									$generic_error.appendTo($form);
								}
								$(".req_form__submit-btn").removeClass("s-disabled");
							}
							else {
								/*
								 for ( var error in dataObj.error) {
								 var val = dataObj.error[error];
								 }
								 */
								if (requestResponse == "undefined") {
									showMsgSomethingwrongLk();
								}
								else {
									showMsgSomethingwrongLk();
								}


							}
						}

					},
					error: function () {
						return;
					}
				});
			};


			var $declineComment = $(".state_change__declinecomment", $form);
			if ($declineComment.val().length > 0 && $('input[name=request_action]').val() == "request_decline" || $('input[name=request_action]').val() == "request_move") {
				reqStateChange();
			}

			var $docChangeBtn = $(".docstate_change", $form);
			var docAction = $docChangeBtn.attr("data-docaction");
			if ($declineComment.val().length > 0 && docAction == "show" || $declineComment.val().length > 0 && docAction == "hide") {
				reqStateChange();
			}
		}
	});


	$(".state_change__action").on("click", function () {
		var $this = $(this);
		var $form = $this.closest(".state_change__form");

		var $thisRadio = $this.children().children("input[name=request_action]");
		$thisRadio.attr("checked", "checked");

		var selectedRadioVal = $thisRadio.val()

		var $saveBtn = $(".state_change__save", $form);

		if (selectedRadioVal == "request_decline" && $(".state_change__declinecomment", $form).val().length < 1) {
			$saveBtn.addClass("s-disabled");
		}
		if (selectedRadioVal == "request_move") {
			$saveBtn.removeClass("s-disabled");
		}

	});

	$(".state_change__declinecomment").each(function () {
		var $declineComment = $(this);
		var $form = $declineComment.closest(".state_change__form");
		var $saveBtn = $(".state_change__save", $form);

		$declineComment.on("keyup", function () {
			if ($declineComment.val().length > 0) {
				$saveBtn.removeClass("s-disabled");
			}
			else {
				$saveBtn.addClass("s-disabled");
			}
		});
	});


});


$(document).ready(function () {

	$(".docstate_change").click(function () {

		var $clickedBtn = $(this);
		if (!$clickedBtn.hasClass("s-disabled")) {
			var $form = $clickedBtn.closest(".state_change__form");
			var docAction = $clickedBtn.attr("data-docaction");

			// var $formBtns = $form.children(".docstate_change");
			var $inputAction = $form.children('input[name=action]');

			$clickedBtn.addClass("s-disabled");

			$inputAction.val(docAction);

			//alert($form.attr("action")+"?"+$form.serialize());
			var docStateChange = function () {
				$.help({
					type: "POST",
					url: $form.attr("action"),
					data: $form.serialize(), // serializes the form's elements.
					success: function (requestResponse) {
						var flagJsonSuccess;
						try {
							var dataObj = JSON.parse(requestResponse);
							flagJsonSuccess = true;
						}
						catch (e) {
							flagJsonSuccess = false;

							var $generic_error = $('<label class="error_generic">При отправке формы произошла ошибка, пожалуйста, проверьте введённые данные или просто позвоните нам: </label>');
							var phoneNumber = $(".data_phone").text();
							var $phoneNumber = $('<span style="white-space: nowrap;">' + phoneNumber + '</span>');
							$generic_error.append($phoneNumber);

							if ($(".error_generic").length == 0) {
								$generic_error.insertAfter($form);
							}
							else if ($(".error_generic").length > 0) {
								$(".error_generic").remove();
								$generic_error.appendTo($form);
							}
							$(".req_form__submit-btn").removeClass("s-disabled");
						}
						if (flagJsonSuccess == true) {
							var dataObj = JSON.parse(requestResponse);

							if (dataObj.status == "success") {
								if (dataObj.url != false && typeof dataObj.url != 'undefined') {
									window.location = dataObj.url;
								}
								else {

									$form.text("Заявка на смену статуса доктора отправлена и проходит модерацию.");
									/*
									 if ( reqAction == "approve" ) {
									 showMsgRequestApproved($form);
									 }
									 else if ( reqAction == "decline") {
									 showMsgRequestDeclined($form);
									 }

									 reqChangeState($form, reqAction);

									 if( $(".error_generic").length > 0) {
									 $(".error_generic").remove();
									 }
									 */
								}

							}
							else {
								/*
								 for ( var error in dataObj.error) {
								 var val = dataObj.error[error];
								 }
								 */
								if (requestResponse == "undefined") {
									showMsgSomethingwrongLk();
								}
								else {
									showMsgSomethingwrongLk();
								}


							}
						}

					},
					error: function () {
						return;
					}
				});
			};

			var $declineComment = $(".docstate_change__comment", $form);
			$declineComment.parent().removeClass("s-hidden");
			$declineComment.keyup(function () {
				if ($(this).val().length > 10) {
					$(".docstate_change", $form).removeClass("s-disabled");
				}
			});
			/*
			 if ( $declineComment.val().length > 10 ) {
			 docStateChange();
			 }
			 */
		}
	});
});

/* show form messages */
var showMsgSomethingwrong = function ($formBtn, $form) {
	if (!$formBtn || !$form) {
		return false;
	}
	else {
		var $generic_error = $('<label class="error_generic">При отправке формы произошла ошибка, пожалуйста, попробуйте еще раз или позвоните нам: </label>');
		var phoneNumber = $(".data_phone").text();
		var $phoneNumber = $('<span style="white-space: nowrap;">' + phoneNumber + '</span>');
		$generic_error.append($phoneNumber);

		if ($(".error_generic").length == 0) {
			$generic_error.insertAfter($form);
		}
		else if ($(".error_generic").length > 0) {
			$(".error_generic").remove();
			$generic_error.appendTo($form);
		}
		$formBtn.removeClass("s-disabled");
	}
};

var showMsgSomethingwrongLk = function ($formBtn, $form) {
	if (!$formBtn || !$form) {
		return false;
	}
	else {
		var $errorPlacement = $(".state_change__submit-ct");
		var $generic_error = $('<label class="error_generic">При отправке формы произошла ошибка, пожалуйста, попробуйте еще раз или позвоните нам: </label>');
		var phoneNumber = $(".data_phone").text();
		var $phoneNumber = $('<span style="white-space: nowrap;">' + phoneNumber + '</span>');
		$generic_error.append($phoneNumber);

		if ($(".error_generic").length == 0) {
			$generic_error.insertAfter($errorPlacement);
		}
		else if ($(".error_generic").length > 0) {
			$(".error_generic").remove();
			$generic_error.appendTo($form);
		}
		$formBtn.removeClass("s-disabled");
	}
};
var showMsgRequestApproved = function ($form) {
	//console.log("Все зашибись!");
	var successText = "Заявка подтверждена"
	$form.html(successText);
};

var showMsgRequestDeclined = function ($form) {
	//console.log("Все зашибись!");
	var successText = "Заявка отменена"
	//$form.html(successText);
	$(".state_change__decline, .state_change__declinecomment-txt", $form).remove();
	$(".state_change__approve", $form).text("Пациент пришел").removeClass("s-disabled");
};

var reqChangeState = function ($form, reqAction) {
	var $reqRow = $form.parents("tr");
	var $reqStateText = $reqRow.children(".status").children(".state");

	$reqRow.removeClass("s-expired");
	if (reqAction == "approve") {
		$reqStateText.text("Прием состоялся");
	}
	else if (reqAction == "decline") {
		$reqStateText.text("Отменён");
	}
};


$(document).ready(function () {

	$(".chart_form").submit(function () {
		var $form = $(this);

		$.ajax({
			type: "POST",
			url: $form.attr("action"),
			data: $form.serialize(), // serializes the form's elements.
			success: function (requestResponse) {
				var flagJsonSuccess;
				try {
					var dataObj = JSON.parse(requestResponse);
					flagJsonSuccess = true;
				}
				catch (e) {
					flagJsonSuccess = false;

					var $generic_error = $('<label class="error_generic">При отправке формы произошла ошибка, пожалуйста, проверьте введённые данные или просто позвоните нам: </label>');
					var phoneNumber = $(".data_phone").text();
					var $phoneNumber = $('<span style="white-space: nowrap;">' + phoneNumber + '</span>');
					$generic_error.append($phoneNumber);

					if ($(".error_generic").length == 0) {
						$generic_error.insertAfter($form);
					}
					else if ($(".error_generic").length > 0) {
						$(".error_generic").remove();
						$generic_error.appendTo($form);
					}
					$(".req_form__submit-btn").removeClass("s-disabled");
				}
				if (flagJsonSuccess == true) {
					var dataObj = JSON.parse(requestResponse);

					var chart2 = new Highcharts.Chart({

						chart: {
							renderTo: 'showmemagic',
							type: 'column'
						},
						plotOptions: {
							column: {
								groupPadding: 0.2,
								borderWidth: 1,
								shadow: true,
								grouping: true,
								dataLabels: {
									enabled: true,
									formatter: function () {
										return this.y + '%';
									}
								}
							}
						},
						title: {
							text: 'Пациенты'
						},
						subtitle: {
							text: 'Конверсия в переведеных/записаных/дошедших'
						},
						xAxis: {
							categories: dataObj.categories
						},
						yAxis: {
							title: {
								text: 'Процент от обратившихся'
							},
							max: 100,
							stackLabels: {
								enabled: true,
								formatter: function () {
									return this.total + "%";
								}
							},
							labels: {
								formatter: function () {
									return this.value + '%';
								}
							}

						},
						legend: {
							layout: 'vertical',
							backgroundColor: '#FFFFFF',
							align: 'left',
							verticalAlign: 'top',
							x: 170,
							y: 0,
							floating: true,
							shadow: true
						},
						series: dataObj.series
					});
				}

			},
			error: function () {
				return;
			}
		});
		return false;
	});


	$(".pasword_change__form").submit(function () {
		var $form = $(this);

		$.ajax({
			type: "POST",
			url: $form.attr("action"),
			data: $form.serialize(), // serializes the form's elements.
			success: function (requestResponse) {
				var flagJsonSuccess;
				try {
					var dataObj = JSON.parse(requestResponse);
					flagJsonSuccess = true;
				}
				catch (e) {
					flagJsonSuccess = false;

					var $generic_error = $('<label class="error_generic">При отправке формы произошла ошибка, пожалуйста, проверьте введённые данные или просто позвоните нам: </label>');
					var phoneNumber = $(".data_phone").text();
					var $phoneNumber = $('<span style="white-space: nowrap;">' + phoneNumber + '</span>');
					$generic_error.append($phoneNumber);

					if ($(".error_generic").length == 0) {
						$generic_error.insertAfter($form);
					}
					else if ($(".error_generic").length > 0) {
						$(".error_generic").remove();
						$generic_error.appendTo($form);
					}
					$(".pasword_change__submit").removeClass("s-disabled");
				}
				if (flagJsonSuccess == true) {
					var dataObj = JSON.parse(requestResponse);

					if (dataObj.status == "success") {
						if (dataObj.url != false && typeof dataObj.url != 'undefined') {
							window.location = dataObj.url;
						}
						else {

							$form.text("Пароль изменён.");

						}

					}
					else if (dataObj.status == "error") {
						$(".error", $form).remove();
						var $error = $('<span class="error"></span>').text(dataObj.error);
						$error.appendTo($form);
					}
					else {

						if (requestResponse == "undefined") {
							showMsgSomethingwrong();
						}
						else {
							showMsgSomethingwrong();
						}


					}


				}

			},
			error: function () {
				return;
			}
		});
		return false;
	});


});


$(".result_table__row.has-details").on("click", function () {

});


/* records block */

var initRecords = function () {
	$(".request_records-ct").each(function () {
		var $me = $(this);
		var $label = $(".request_records__label", $me);
		var $records = $(".request_records", $me);

		$label.on("click", function () {
			$records.toggleClass("s-open");
		});
	});
}

$(document).ready(function () {
	$(".help_form__submit").click(function () {


		var $clickedBtn = $(this);
		var $form = $(".help_form");
		if (!$clickedBtn.hasClass("s-disabled")) {

			$.ajax({
				type: "POST",
				url: $form.attr("action"),
				data: $form.serialize(), // serializes the form's elements.
				success: function (requestResponse) {
					var flagJsonSuccess;
					try {
						var dataObj = JSON.parse(requestResponse);
						flagJsonSuccess = true;
					}
					catch (e) {
						flagJsonSuccess = false;

						var $generic_error = $('<label class="error_generic">При отправке формы произошла ошибка, пожалуйста, проверьте введённые данные или просто позвоните нам: </label>');
						var phoneNumber = $(".data_phone").text();
						var $phoneNumber = $('<span style="white-space: nowrap;">' + phoneNumber + '</span>');
						$generic_error.append($phoneNumber);

						if ($(".error_generic").length == 0) {
							$generic_error.appendTo($form);
						}
						else if ($(".error_generic").length > 0) {
							$(".error_generic").remove();
							$generic_error.appendTo($form);
						}
						$clickedBtn.removeClass("s-disabled");
					}
					if (flagJsonSuccess == true) {
						var dataObj = JSON.parse(requestResponse);

						if (dataObj.status == "success") {
							if (dataObj.url != false && typeof dataObj.url != 'undefined') {
								window.location = dataObj.url;
							}
							else {
								$form.html('<span style="font-size:90%">Спасибо, ваш вопрос направлен в службу поддержки!');

								if ($(".error_generic").length > 0) {
									$(".error_generic").remove();
								}
							}

						}
						else if (dataObj.error != null && dataObj.error != 'undefined') {
							var $generic_error = $('<label class="error_generic">' + dataObj.error + '</label>');
							if ($(".error_generic").length == 0) {
								$generic_error.appendTo($form);
							}
							else if ($(".error_generic").length > 0) {
								$(".error_generic").remove();
								$generic_error.appendTo($form);
							}
							$clickedBtn.removeClass("s-disabled");
						}
						else {
							/*
							 for ( var error in dataObj.error) {
							 var val = dataObj.error[error];
							 }
							 */
							if (requestResponse == "undefined") {
								showMsgSomethingwrongLk();
							}
							else {
								showMsgSomethingwrongLk();
							}


						}
					}

				},
				error: function () {
					return;
				}
			});
		}
		;

	});
});
/* */

