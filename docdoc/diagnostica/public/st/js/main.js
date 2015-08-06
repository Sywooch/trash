/* dropdown markup */

$(function(){
	initDropDowns();
    $("#diagnostic-type .b-select_list__item").click(function(){
        var id = $(this).data('spec-id');
        textSelectItem = $(this).text();
        $("#diagnostic").val(id);
        $("#diagnostic-type .b-select_list__act").removeClass("b-select_list__act");
        $(this).addClass("b-select_list__act");    
        
        var selectedItem = diagnostics[id];
        output = '';
        for(subItem in selectedItem.childs){
            output += '<div class="js-specselect b-select_list__item" data-spec-id="'+subItem+'">'+selectedItem.childs[subItem].name+'</div>'; 
        }
        $("#diagnostic-subtype .b-select_list").html(output);
        if(output == ''){
            $("#diagnostic-subtype-btn").addClass('blocked').removeClass("selected");
            msg = 'нет вариантов';
        } else {
            $("#diagnostic-subtype-btn").removeClass('blocked');
            $("#diagnostic-subtype-btn").addClass("selected");
            msg = 'выберите из списка';
        }
        
        $//("#diagnostic-type").slideUp(300, function(){
            $("#diagnostic-type .b-select_current").text(textSelectItem);
            $("#diagnostic-subtype .b-select_current").text(msg);
        //});
        
        	//();
    });
	bindDiagnosticSubType();

    $('#showDesc').click(function(){
        $('.short-description').css("display","none");
        $('.full-description').css("display","block"); 
    });
    
    $('#hideDesc').click(function(){
        $('.full-description').css("display","none"); 
        $('.short-description').css("display","block");
    });

	// Показывает номер телефона для непроплаченных клиник
	$(".show-clinic-phone").on("click", function () {
		var id = $(this).data("id");
		var name = $(this).data("name");

		$(this).parent().text($(this).data("phone"));

		$(document).trigger('UnpaidDiagShowNumber',
			{id: id, name: name}
		);

		return false;
	});
});

/* dropdown script */
    
function initDropDowns() {

	$('.b-dropdown_list .b-dropdown_item').click(function (){
		var $clickedItem = $(this),
			$wrapper = $clickedItem.closest('.b-dropdown'),
			$currentItem = $(".b-dropdown_item__current", $wrapper ),
			$currentItemText = $(".b-dropdown_item__text", $wrapper );

		//$currentItem.html($clickedItem.html());
		$currentItemText.html($clickedItem.text());
		$('.b-dropdown_item.s-current', $wrapper ).removeClass('s-current');
		$clickedItem.addClass('s-current');

		$('.b-dropdown_list', $wrapper ).hide();
		$wrapper.removeClass("s-open");

	});

	$('body').unbind('click.dropdown').bind( 'click.dropdown', function(evt) {
		$('.b-dropdown_list').hide();
		$(".b-dropdown").removeClass("s-open");
	});

	$(".b-dropdown_form").each( function() {
		var $wrapper = $(this).closest('.b-dropdown');
		$('.b-dropdown_list .b-dropdown_item', $wrapper).click( function(){
			var $clickedItem = $(this);
			var $clickedItemValue = $clickedItem.attr("data-cityid");
			$(".b-dropdown_input", $wrapper).val( $clickedItemValue );
			$(".b-dropdown_form", $wrapper).submit();
		});
	});

	$('.b-dropdown_item__current').click(function (evt){
		evt.stopPropagation();
		var $wrapper = $(this).closest('.b-dropdown');
		var $dropdownList = $('.b-dropdown_list', $wrapper );

		if (($dropdownList).is(":visible")) {
			$dropdownList.hide();
			$wrapper.removeClass("s-open");
		}
		else {
			$wrapper.addClass("s-open");
			$dropdownList.show();
		}

	});
}

function bindDiagnosticSubType(){
    $("#diagnostic-subtype .b-select_list__item").click(function(){
        var id = $(this).data('spec-id');
        textSelectItem = $(this).text();
        $("#diagnostic").val(id);
        $("#diagnostic-subtype .b-select_list__act").removeClass("b-select_list__act");
        $(this).addClass("b-select_list__act");
        //$("#diagnostic-subtype").slideUp(300, function(){
        	$("#diagnostic-subtype .b-select_current").text(textSelectItem);
        //});
    });
}
/* dropdown script END */

/* =js-slidedown */
var slidedownInit = function($trigger){
	$trigger.each(function(){
		var $this = $(this);
		var $trigger = $(this);
		var $container = $this.next(".js-slidedown-ct");
		$container.slideUp(0).css("position", "relative").css("opacity", "1").addClass("s-closed");

		$trigger.click(function(){
			if ($container.hasClass("s-open")) {
				$container.removeClass("s-open").addClass("s-closed").slideUp(0);
			}
			else {
				$container.removeClass("s-closed").addClass("s-open").slideDown(0);
			}
		});
	});
};
/* slidedown end */

/* =js-tabs */
var tabsInit = function(){

	$(".js-tabs").each(function(){
		var $this = $(this);
		var $tabsControls = $(".js-tabs-control", $this);
		var $tabs = $(".js-tabs-tab", $this);

		var i = 0;
		$tabs.each(function(){
			$(this).attr("data-tabindex", i);
			if( i == 0) { $(this).addClass("s-open").show() }
			i++	;
		});

		var i = 0;
		$tabsControls.each(function(){
			$(this).attr("data-tabindex", i);
			i++	;

			$(this).click(function(){
				var $clickedTab = $(this);
				if ( $clickedTab.hasClass("s-active") ) { return false }
				else {
					$tabsControls.removeClass("s-active");
					$clickedTab.addClass("s-active");
					$tabs.removeClass("s-open").hide().each(function(){
						if ( $(this).attr("data-tabindex") == $clickedTab.attr("data-tabindex") ) {
							$(this).addClass("s-open").show();
						}
					});
				}
			});
		});
	});
};
/* js-tabs end */

/* link-departure */
$(".filter_input_checkbox").change(function(){
	var goto = $(this).closest(".link-departure").attr("href");
	window.location = goto;
	return false;
});
/* link-departure end */

/* link-showall */
var initShowall = function(){
	$(".js-showall").click(function(){
		var $clickedItem = $(this);
		var getFrom = $clickedItem.attr("href");
		var $target = $clickedItem.parent().parent(".reviews");

		$.ajax({
			url: getFrom,
			context: document.body
		}).done(function(data) {
				$target.append(data);
				$clickedItem.remove();
				starsReviewsInit();
				tooltipsInit();
			});
		return false;
	});
}
/* link-showall end */

/* phone mask */
$(function() {
	$.mask.definitions['~'] = "[+-]";$(".js-mask-phone").mask("+7 ?(999) 999-99-99");
	$.mask.definitions['~'] = "[+-]";$(".js-mask-phone-request").mask("?(999) 999-99-99");
});
/* phone mask end */

/* tooltip */

var tooltip = {
	current: null,
	delay: 600,
	timer: null,
	setDialogs: function () {
		//Determine dialog positions
	}
}

var tooltipsInit = function(container) {

	container = container || 'body';

	$('.js-tooltip-tr', container).each(function(){
		var $me = $(this);
		if ( $me.data("initDone") != "true" ) {
			$me.data("initDone", "true");
			$me.hover(function(event) {
				var titleText = $(this).attr('title');
				$(this)
					.data('tipText', titleText)
					.removeAttr('title');
				$('<p class="js-tooltip ui-bg-yellow"></p>')
					.text(titleText)
					.appendTo('body');
				if ($(this).hasClass('tooltip-popup')) {
					$('.js-tooltip').css('position', 'fixed');
				}
				$('.js-tooltip').css('top', (event.pageY - 10) + 'px')
					.css('left', (event.pageX + 20) + 'px')
					.fadeIn('slow');

			}, function() {
				$(this).attr('title', $(this).data('tipText'));
				$('.js-tooltip').remove();
			}).mousemove(function(event) {
					$('.js-tooltip')
						.css('top', (event.pageY - 10) + 'px')
						.css('left', (event.pageX + 20) + 'px');
				});
		}
	});
}

/* tooltip end */

/* metro controls position */
var metroControlsInit = function() {

	if ( $("#metroControls").length > 0 ) {
		var $metrocontrols = $("#metroControls"),
			$metropopup = $('.popup[data-popup-id="js-popup-geo"]'),
			$window    = $(window),
			offsetControls,
			offsetPopup;

		var metroControlsPosition = function() {

			offsetControls = $metrocontrols.css("position","fixed").offset();
			offsetPopup = $metropopup.offset();

			//console.log(offsetControls.top + $metrocontrols.height());
			//console.log(offsetPopup.top+$metropopup.height());
			if ((offsetControls.top + $metrocontrols.height()) > ($metropopup.height()) + $metrocontrols.height() + 30) {
				$metrocontrols.css("position", "relative");
				$metropopup.css("padding-bottom", 0)
				//console.log("уехало");

			} else {
				//console.log("все на месте");
				$metrocontrols.css("position", "fixed");
				$metropopup.css("padding-bottom", ($metrocontrols.height()+20))
			}
		}

		$window.scroll(function(){
			metroControlsPosition();
		});
		$(".js-tabs-control", $(".popup")).click(function(){
			metroControlsPosition();
		});
	}
};
/* metro controls position END */


/* js-formsubmit */
var relatedFormSubmit = function($el) {
	var me = $el;
	var relatedForm = me.data("relatedForm");
	if (relatedForm) {
		var $relatedForm =  $("." + relatedForm);
		if ($relatedForm) {
			$relatedForm.submit();
		}
	}
	else {
		console.log("nothing to submit");
	}
}
/* js-formsubmit END */


/* =js-callmeback */
var callmebackInit = function(){
	if ( $(".js-callmeback").length > 0 ) {
		var callmeback = {}
		callmeback.$self = $(".js-callmeback");
		callmeback.$trigger = $(".js-callmeback-tr", callmeback.$self);
		callmeback.$form = $(".callback_form", callmeback.$self );
		callmeback.$input = $(".callback_input", callmeback.$self)

		callmeback.$trigger.click(function(){
			callmeback.$form.removeClass("s-hidden");
			callmeback.$input.focus();
		});
	}
}
/* js-callmeback */


/* js-howto */

var howtoInit = function(){
	$(".js-howto-tr").each(function(){
		var howto = {}
		howto.$trigger = $(this);
		howto.id = howto.$trigger.data("howto-id-target");
		howto.$content = $('[data-howto-id=' + howto.id + ']');
		howto.$content.defaultHeight = howto.$content.height();
		howto.$content.removeClass("js-init-height");
		howto.$trigger.defaultText = howto.$trigger.text();
		howto.$trigger.altText = howto.$trigger.data("altText");

		howto.$trigger.click(function(){
			var txt = howto.$content.hasClass('s-open') ? howto.$trigger.defaultText : howto.$trigger.altText ;
			howto.$trigger.text(txt);
			howto.$content.toggleClass("s-open");
			howto.$content.slideToggle();
		});
	});
}

/* js-howto END */

/* workaround for wrong position of autocomplete when resizing window */
$(window).resize(function() {
	$(".ui-autocomplete").css('display', 'none');
});
/* workaround for wrong position of autocomplete when resizing window end */

/* js-autocomplete-trigger */

//data-autocomplete-id="autocomplete-spec"
$(".js-autocomplete-trigger").each(function(){
	var self = $(this);
	self.relatedInput = self.data("autocomplete-id");
	self.$relatedInput = $('input[data-autocomplete-id=' + self.relatedInput + ']');

	self.$relatedInput.focus(function(){
		$(this).autocomplete('search', '');
	});

	self.click(function(){
		self.$relatedInput.focus();
	});
});

/* js-autocomplete-trigger END */

/* form validation */

var requestValidate = function($form){
	$form.validate({
		rules: {
			requestName: {
				required: true
				//onlyLetters: true
				//maxlength: 70
			},
			requestPhone: {
				required: true
				//minlength: 18,
				//phoneNumberBegins: true
			},
			requestComments: {
				//maxlength: 1000
			}
		},
		messages: {
			requestName: {
				required: "Пожалуйста, введите Ваше имя"
				//maxlength: "Не более 70 символов"
			},
			requestPhone: {
				required: "Укажите полный номер"
				//digits: "Номер телефона должен содержать только цифры",
				//minlength: "Укажите полный номер"
			},
			requestComments : {
				//maxlength: "Не более 1000 символов"
			}
		},
		submitHandler: function(){
			if (!$form[0]['formType']) {
				var input = document.createElement('input');
				input.type = 'hidden';
				input.name = 'formType';
				$form[0].appendChild(input);
			}

			var url = $form.attr("action"); // the script where you handle the form input.

			$.ajax({
				type: "POST",
				url: url,
				data: $form.serialize(), // serializes the form's elements.
				beforeSend: function() {
					// Для блокировка повторного нажатия на "Отправить"
					if ($form.data("submiting") != undefined) {
						return false;
					}

					$form.data("submiting", "1");
				},
				error: function() {
					showMsgSomethingwrong($('.req_form__submit-btn'), $form);
				},
				success: function(dataObj) {
					if (dataObj) {
						if (dataObj.status == "success") {

							$(document).trigger("requestCreated", [$form[0], dataObj.req_id, dataObj]);

							if (dataObj.url != false && typeof dataObj.url != 'undefined') {
								//таймаут нужен, чтобы успели отработать события до перезагрузки страницы
								setTimeout(
									function() { window.location = dataObj.url; },
									500);
							}
							else {
								if ( $form.hasClass("callback_form")){
									var $callbackThanks = $('<div class="callback_thanks">Спасибо, мы вам перезвоним!</div>');
									$callbackThanks.appendTo(".callback");
									$(".callback_input").blur();
								}
								else {
									showMsgRequestSent();
								}

								if( $(".error_generic").length > 0) {
									$(".error_generic").remove();
								}
							}

						}
						else {
							showMsgFieldsValidation($form, dataObj);
						}
					}
					//снимаем блокировку по таймауту, чтобы окно успело скрыться
					setTimeout(function () {
						$form.data("submiting", null);
					},1000);
				},
				error: function() {
					$form.data("submiting", null);
					return;
				}
			});
		}
	});
};
/* form validation END */

/* show form messages */
var showMsgFieldsValidation = function($form, dataObj) {
	$.each(dataObj.errors, function(field, errors) {
		var input = $form[0]['requestForm[' + field + ']'];
		if (input) {
			$(input).addClass('error');
			$('span.error', input.parentNode).remove();
			$('<span class="error">' + errors.join('. ') + '</span>').insertAfter(input);
		}
	});

	$('input.error', $form).one('change.error', function(e) {
		console.log('input.change');
		$('span.error', this.parentNode).remove();
	});
};

var showMsgSomethingwrong = function($formBtn, $form){
	if ( !$formBtn || !$form ) {
		return false;
	}
	else {
		var $generic_error = $('<label class="error_generic">При отправке формы произошла ошибка, пожалуйста, проверьте введённые данные или просто позвоните нам.</label>') ;
		var $phoneNumber = $('<span style="white-space: nowrap;">' + $(".header_contacts__phone").text() + '</span>');
		$generic_error.append($phoneNumber);
		if ( $(".error_generic").length == 0 ) {
			$generic_error.insertAfter($form);
		}
		$formBtn.removeClass("s-disabled");
	}
};

var showMsgRequestSent = function(){
	var successText = "Ваша заявка о записи в клинику отправлена. Наши консультанты свяжутся с вами в течение 15 минут ежедневно с 9:00 до 21:00 и запишут Вас на прием.";

	if ($(".req_form", ".js-popup")){
		var $form = $(".req_form", ".js-popup");
		var $popup = $(".req_form").parent(".js-popup");
		if ($(".js-request-success").length > 0) {
			var $successText = $(".js-request-success");
			$successText.show();
		}
		else {
			var $successText = $('<p class="mvn js-request-success">' + successText + '</p>');
			$successText.appendTo($popup);
		}
		$form.slideUp().addClass("s-closed");
	}
	else {
		$(".req_form").html(successText);
	}
};
/* show form messages END */

/* custom select */
function selectCustomShow(parent) {
	parent.find('.b-select_list').slideDown(1, function() {parent.addClass('b-select_list__open');});
}
function selectCustomHide() {
	$('.b-select_list__open .b-select_list').hide();
	$('.b-select_list__open').removeClass('b-select_list__open');
}
function selectCustomVal(parent, el) {
	parent.find('.b-select_list__act').removeClass('b-select_list__act');
	el.addClass('b-select_list__act');
	parent.find('.b-select_current').text(el.text());
	selectCustomHide();
}
/* custom select END */

function showRequestPopup($clinicCard, isCallOpen, extra) {
	var $button = $('.js-request-popup', $clinicCard);

	if (!$button.length) return false;

	var params = {
		clinic: {
			id: $button.data("clinic-id"),
			name: $button.data("clinic-name"),
			address: $button.data("clinic-address"),
			discountOnline: $button.data("clinic-discount-online"),
			metro: []
		},
		contactPhone: $button.data("clinic-phone"),
		dateAdmission: null
	};

	if (extra && extra.diagnosticsId) {
		params.defaultDiagnosticId = extra.diagnosticsId;
		params.skipStepDiagnostic = extra.skipStepDiagnostic;
	} else if ($("#diagnosticsId").length && $("#diagnosticsId").data("has-parent")) {
		params.skipStepDiagnostic = true;
	}

	$('.metro_item', $clinicCard).each(function () {
		params.clinic.metro.push({
			title: $(this).text(),
			dist: $(this).data('dist'),
			lineId: $(this).data('line-id')
		});
	});

	$(document).trigger('requestPopupOpen', params);

	if (isCallOpen) {
		$button.data('popupObject').open();
	}

	return true;
}


/* window ready events */
$().ready(function() {

	/* request forms validation */
	$(".req_form").each(function(){
			requestValidate($(this));
		}
	);
	/* request forms validation */

	$('body').click(function() {
		selectCustomHide();
	});

	$('.b-select_current,.b-select_arr').click(function() {
		selectCustomShow($(this).parents('.b-select_wrap'));
	});
	$('.b-select_list__item').click(function() {
		selectCustomVal($(this).parents('.b-select_wrap'), $(this));
	});

	/* slidedown init */
	slidedownInit($(".js-slidedown-tr"));
	/* slidedown init end */

	/* tabs init */
	tabsInit();
	/* tabs init END */

	/* showall init */
	initShowall();
	/* showall init END */

	/* tooltips init */
	tooltipsInit();
	/* tooltips init END */

	/* metro controls init */
	metroControlsInit();
	/* metro controls init END */

	/* callmeback init */
	callmebackInit();
	/* callmeback init END */

	/* howto init */
	howtoInit();
	/* howto init END */

	$(".search_input_imit_spec").dotdotdot({height: 32, tolerance: 32, wrap: 'letter'});

	$(".price_tbl_show_btn").click(function() {
		$(".price_tbl_hidden[data-price-id="+$(this).data('id')+"]").slideToggle(300);
		$(this).toggleClass("price_tbl_show_btn_open");
	});

	$('.js-request-popup').click(function (e) {
		showRequestPopup($(this).closest('.clinic_card'));
	});

	$('.js-request-link').click(function() {
		showRequestPopup($(this).closest('.clinic_card'), true, {
			diagnosticsId: $(this).data('diagnosticId'),
			skipStepDiagnostic: true
		});
	});

	if ($('.nearest_clinics_slider').length && $('.nearest_clinics_item').length > 3) {
		var widthItemSliderNearest = $('.nearest_clinics_slider').data('itemwidth') ? $('.nearest_clinics_slider').data('itemwidth') : 289;
		$('.nearest_clinics_slider').bxSlider({
			minSlides: 1,
			maxSlides: 3,
			moveSlides: 1,
			slideMargin: 0,
			pager: false,
			useCSS: false,
			slideWidth: widthItemSliderNearest
		});
	}
});
/* window ready events END */
