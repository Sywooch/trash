/* popup */

var widgetPopup = function ($el) {
	var popup = {};
	popup.self = $el;

	popup.initEvents = function () {
		popup.close = function () {
			if (popup.self.hasClass("request")) {
				$(document).trigger("requestPopupCloseStart");
			} else {
				popup.self.fadeOut(150);
				popup.bg.fadeOut(150);
			}
		};
		popup.open = function () {
			popup.calculatePosition();
			popup.self.fadeIn(200);
			popup.bg.fadeIn(200);
			popup.self.css({
				'visibility': 'visible',
				'z-index': 4050
			});

			if ($el.data('noscroll')) {
				var $body = $('body');
				$body.data('scroll-position', $body.scrollTop());
				$body.css('top', -($body.scrollTop()) + 'px').addClass('noscroll');
			}
		};

		popup.closeBtn.click(popup.close);
		popup.bg.click(popup.close);

		popup.trigger.click(function (e) {
			popup.open();
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

		$('.input_metro_submit').click(popup.close);
	};

	popup.calculatePosition = function () {
		var popupMarginTop,
			popupMarginLeft,
			popupTop;

		if (popup.self.css('display', 'block').height() > $(window).height()) {
			popupMarginTop = 20 + 'px';
			popupTop = 0;
			popup.self.css("position", "absolute");

		}
		else {
			popupMarginTop = '-' + Math.round(popup.self.height() / 2) + 'px';
			popupTop = '50%';
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

		popup.id = popup.self.data('popupId');
		popup.trigger = $('.js-popup-tr[data-popup-id="' + popup.id + '"]');
		popup.trigger.data('popupObject', this);
		popup.self.forcedWidth = popup.trigger.data('popupWidth');
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
		popup.calculatePosition();
		popup.initEvents();
		popup.self.css('display', 'none');
	};

	$(function () {
		popup.build();
	});
};

$(function () {
	$('.js-popup').each(function () {
		widgetPopup($(this));
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
		});
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
			$('input[name=diagnostic]').val(ui.item.id);
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

	$(".search_input_geo").autocomplete({});

	$(".search_input_geo").focus(function () {
		$(this).autocomplete({
			close: function () {
				$(".search_form").submit();
			}
		});
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

	$('#geoValue').val(metroIds);


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

	$that = $(this);

	var specName = $that.text();

	if ($that.hasClass('spec_list_item')) {
		if (!$that.hasClass('spec_list_head')) {
			clickName = $that.parents('.column_group').find('.spec_list_head').text();
			specName = clickName + ' ' + $that.text();
		}
	} else {
		if ($that.hasClass('b-select_list__item') && $that.parents('.b-select_wrap').hasClass('b-select_spec__sub')) {
			clickName = $('.b-select_spec .b-select_current').text();
			specName = clickName + ' ' + $that.text();
		}
	}

	//$('.search_input_spec').val(specName);
	$('.search_input_spec').focus().val(specName).blur();
	$(".search_input_imit_spec").text(specName).trigger("update");
	//return false;
});
/* choose end */