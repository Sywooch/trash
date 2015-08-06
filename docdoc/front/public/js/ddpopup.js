/* popup */

var widgetPopup = function ($el, props) {
	var popup = {};
	popup.self = $el;
	if (props != undefined) {
		for (each in props) {
			popup[each] = props[each];
		}
	}

	popup.initEvents = function () {

		popup.close = function () {
			popup.self.fadeOut(150);
			popup.bg.fadeOut(150);
			$('body').removeClass('noscroll');
		}

		popup.open = function () {
            popup.calculatePosition();
			popup.self.fadeIn(150);
			popup.bg.fadeIn(150);
			popup.self.css({
				'visibility': 'visible',
				'z-index': 4050
			});
			window.parent.postMessage(document.body.scrollHeight, "*");
		};

		popup.closeBtn.click(
			popup.close
		);

		popup.bg.click(
			popup.close
		);

		$(document).on('click', '.js-popup-tr[data-popup-id="' + popup.id + '"]', function (e) {
			popup.open();

			$(document).trigger("popupOpened", [e.target, popup.self]);

			e.preventDefault();
		});

		/* pressing ESC button will close popup */
		$(document).keydown(function (e) {
			if ((e.which == 27) && (popup.self.is(':visible'))) {
				popup.close();
			}
		});

		$(window).resize(function () {
            if (popup.self.is(":visible")) {
                popup.calculatePosition();
            }
		});

		$('.input_metro_submit').click(
			popup.close
		);
	};

	popup.calculatePosition = function () {
		var popupMarginTop,
			popupMarginLeft,
			popupTop;

		if (popup.self.css('display', 'block').height() > $(window).height()) {
			popupMarginTop = 20 + 'px';
			popupTop = 0;
			popup.self.css("position", "absolute");
		} else {
			popupMarginTop = '-' + Math.round(popup.self.height() / 2) + 'px';
			popupTop = window.isFrame ? (Math.round(popup.self.height() / 2) + 20) + 'px' : '50%';
			popup.self.css("position", "fixed");
		}

		popupMarginLeft = '-' + Math.round(popup.self.forcedWidth / 2) + 'px';

		popup.self.css({
			width: popup.self.forcedWidth,
			'margin-top': popupMarginTop,
			'margin-left': popupMarginLeft,
			'top': popupTop
		});
	};

	popup.build = function () {

		var $body = $('body');

		popup.id = !popup.id ? popup.self.data('popupId') : popup.id;
		popup.trigger = $('.js-popup-tr[data-popup-id="' + popup.id + '"]');
		popup.self.forcedWidth = popup.width ? popup.width : popup.trigger.data('popupWidth');
		popup.callback = popup.self.data('callback');

		var $popupClose = $('<div class="popup_close">x</div>');
		$popupClose.appendTo(popup.self);
		popup.closeBtn = $popupClose;

		if ($('.popup_bg').length == 0) {
			var $popupBg = $('<div class="popup_bg"></div>');
			$popupBg.appendTo($body);
		}

		popup.bg = $('.popup_bg');

		if (popup.callback) {

		}

		// init
		// popup.calculatePosition();
		popup.initEvents();
	};

	popup.build();
	$(document).on("closePopup", function() {popup.close()});

	return popup;
};

$(function () {
	$('.js-popup').each(function () {
		var $el = $(this);
		widgetPopup($el);
	});
});

/* popup end */



/* *******************************************
 **************** popup related ***************
 ******************************************* */

/* specselect */
$(document).ready(function () {

	$("body").delegate(".js-specselect", "click", function () {
		var me = $(this);
		var selectedId = me.data("specId");
		$(".js-choose-input-spec").val(selectedId);
		$(".popup").fadeOut();
		$(".popup_bg").fadeOut();

		relatedFormSubmit(me);
		return false;

	});

	var specList = $('.xml-data-speclist');
	if (specList.length > 0) {
		specList = specList.text();
		specList = JSON.parse(specList);
		specList = $.map(specList, function (item) {
			return {
				value: item.name /*id*/,
				label: item.name,
				id: item.id
			};
		})
		/*.sort(function(a, b){
		 if (a.label < b.label) return -1;
		 if (a.label > b.label) return 1;
		 return 0;
		 });*/
	}


	$(".search_input_spec").autocomplete({
		//source: specList,

		source: function (request, response) {
			var term = $.ui.autocomplete.escapeRegex(request.term)
				, startsWithMatcher = new RegExp("^" + term, "i")
				, startsWith = $.grep(specList, function (value) {
					return startsWithMatcher.test(value.label || value.value || value);
				})
				, containsMatcher = new RegExp(term, "i")
				, contains = $.grep(specList, function (value) {
					return $.inArray(value, startsWith) < 0 &&
						containsMatcher.test(value.label || value.value || value);
				});

			response(startsWith.concat(contains));
		},
		minLength: 0,
		select: function (event, ui) {
			$('input[name=spec]').val(ui.item.id);
			$(this).val(ui.item.value);
			return false;
		}
	});
	//$( ".mainpage .search_input_spec").autocomplete('search');
	$(".mainpage .search_input_spec").focus(function () {
		$(this).autocomplete('search', '')
	});

});
/* specselect end */



/* stationselect */
$(document).ready(function () {

	/*
	 $("body").delegate(".js-stationselect", "click", function(){
	 var me = $(this);
	 var selectedId = me.data("stationsId");
	 selectedId = selectedId.split(',');
	 console.log(selectedId);


	 $(".js-choose-input-geo").val(selectedId);
	 me.toggleClass("s-active");

	 });
	 */

	var specList = $('.xml-data-speclist');
	if (specList.length > 0) {
		var specList = specList.text();
		specList = JSON.parse(specList);
		specList = $.map(specList, function (item) {
			return {
				value: item.name,
				label: item.name,
				id: item.id
			};
		});
	}
	/*.sort(function(a, b){
	 if (a.label < b.label) return -1;
	 if (a.label > b.label) return 1;
	 return 0;
	 });*/

	$(".search_input_spec").autocomplete({
		//source: specList,

		source: function (request, response) {
			var term = $.ui.autocomplete.escapeRegex(request.term)
				, startsWithMatcher = new RegExp("^" + term, "i")
				, startsWith = $.grep(specList, function (value) {
					return startsWithMatcher.test(value.label || value.value || value);
				})
				, containsMatcher = new RegExp(term, "i")
				, contains = $.grep(specList, function (value) {
					return $.inArray(value, startsWith) < 0 &&
						containsMatcher.test(value.label || value.value || value);
				});

			response(startsWith.concat(contains));
		},
		minLength: 0,

		focus: function (event, ui) {

			$('input[name=spec]').val(ui.item.id);
			$(this).val(ui.item.value);
			return false;
		},
		select: function (event, ui) {

			$('input[name=spec]').val(ui.item.id);
			$(this).val(ui.item.value);
			return false;
		}
		/*
		 select: function( event, ui ) {
		 $('input[name=spec]').val( ui.item.id );
		 $(this).val( ui.item.value );
		 return false;
		 }*/
	});
	//$( ".mainpage .search_input_spec").autocomplete('search');
	$(".mainpage .search_input_spec").focus(function () {
		$(this).autocomplete('search', '')
	});

});
/* stationselect end */

/* choose */
$(".input_metro_submit").click(function () {

	var me = $(this);
	var metroIds = [];
	$('input[name="metroId"]').each(function () {
		//alert($(this).val());
		metroIds.push($(this).val());
	});

	var metroCount = metroIds.length;
	if (metroCount > 1) {
		$('.search_list_metro.s-dynamic').css({
			"width": 295,
			"margin-left": -295,
			"position": "absolute"
		});
	}


	metroIds = metroIds.toString();

	$('#stations').val(metroIds);


	var metroNames = '';
	$('.metro_selected dt').each(function () {
		metroNames = metroNames + $(this).text() + ', ';
	});
	metroNames = metroNames.slice(0, -2);

	$('.search_input_geo').focus().val(metroNames).blur();

	relatedFormSubmit(me);
	//return false;
});

$(".js-specselect").click(function () {

	var specName = $(this).text();

	//$('.search_input_spec').val(specName);
	$('.search_input_spec').focus().val(specName).blur();
	$(".search_input_imit_spec").text(specName);
	//return false;
});
/* choose end */


$(document).on('popupOpened', function(e, btn, popup) {
    onPopupOpen(btn, popup);
    e.preventDefault();
});

function onPopupOpen(btn, popup) {

    //обрабатываю только окна на запись
    if (!popup.hasClass("request")) {
        return;
    }

    //доктора
    var doctor_id = $(btn).data("doctorId");
    $("input[name=doctor]", popup).val(doctor_id);
    $(".js-request-popup-doctor", popup).text($(btn).data("doctorName"));

    //цены
    var price = $(btn).data("doctor-price");
    $("input[name=price]", popup).val(price);
    var special_price = $(btn).data("doctor-special-price");
    $("input[name=special_price]", popup).val(special_price);

    //клиники
    var clinic_id  = $(btn).data("clinicId");
    $("input[name=clinic]", popup).val(clinic_id);
    $(".js-request-popup-clinic", popup).text($(btn).data("clinicName"))

    $("input[type=submit]", popup).attr("data-stat-trigger", $(btn).data("stat"));

    var phoneText = $(btn).nextAll("div").find(".request_tel_number").text();

    if (phoneText == '') {
        phoneText = $(".header_contact_phone").text();
    }

	if (phoneText == '') {
		$(".js-request-tel-header", popup).hide();
	} else {
		$(".js-request-tel-header", popup).show();
		$(".js-request-tel", popup).text(phoneText);
	}

    if (clinic_id > 0) {
        $(".js-request-tel", popup).removeClass("comagic_phone").addClass("clinic_phone_" + clinic_id);
    }

    if ($(".req_form").hasClass("s-closed")) {
        $(".req_form").removeClass("s-closed").show();
        $(".js-request-success").hide();
    }

    $("[name=requestName]", popup).focus();

    $(document).trigger(
        'requestPopupReady',
        {
            doctor_id: doctor_id,
            clinic_id: clinic_id,
            price: price,
            special_price: special_price
        }
    );

};

$(document).ready(function() {
	$(".schedule_doctor_slider a, input.request-online").click(function() {
		var doctor = $(this).data('doctor');
		var btn = $("#btn_"+doctor);

		if ($(btn).hasClass("request-online")) {
			var clinic = $(this).data('clinic');
			var srcElement = this;
			var dt = $(this).data('date');
			$.get("/request/form/?clinic="+clinic+"&doctor="+doctor+"&date="+dt, function(data){
				var c = $("#schedule-popup");
				c.html(data);
				var p = widgetPopup(c, {id : "schedule-popup", width:735});
				setTimeout(function() {BookingSlider(srcElement)}, 0);
				p.open();
			});
		} else {
			$(btn).click();
		}

		return false;
	});
});

/* *******************************************
 **************** popup related end **********
 ******************************************* */
