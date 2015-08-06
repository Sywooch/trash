/* dropdown markup */

$(function(){
	initDropDowns();
});

/* dropdown script */

function initDropDowns() {

	$('.b-dropdown_list .b-dropdown_item').click( function (){

			var $clickedItem = $(this),
				$wrapper = $clickedItem.closest('.b-dropdown'),
				$currentItem = $(".b-dropdown_item__current", $wrapper );
			$currentItemText = $(".b-dropdown_item__text", $wrapper );

			//$currentItem.html($clickedItem.html());
			$currentItemText.html($clickedItem.text());
			$('.b-dropdown_item.s-current', $wrapper ).removeClass('s-current');
			$clickedItem.addClass('s-current');

			$('.b-dropdown_list', $wrapper ).hide();
			$wrapper.removeClass("s-open");

		}
	);

	$(".b-dropdown_form").each( function() {
		var $wrapper = $(this).closest('.b-dropdown');
		$('.b-dropdown_list .b-dropdown_item', $wrapper).click( function(){
			var $clickedItem = $(this);
			var $clickedItemValue = $clickedItem.attr("data-cityid");
			$(".b-dropdown_input", $wrapper).val( $clickedItemValue );
			$(".b-dropdown_form", $wrapper).submit();
		});
	});

	$('body').unbind('click.dropdown').bind( 'click.dropdown', function(evt) {
		$('.b-dropdown_list').hide();
		$(".b-dropdown").removeClass("s-open");
	});

	$('.b-dropdown_item__current').click( function (evt){
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

		}
	);
}
/* dropdown script END */

/* form validation */

var requestValidate = function($form){
	$form.validate({
		rules: {
			requestName: {
				required: true,
				onlyLetters: true,
				maxlength: 70
				//minlength: 2,
			},
			requestPhone: {
				required: true,
				//digits: true,
				minlength: 18
				//maxlength: 18,
			},
			requestComments: {
				maxlength: 1000
			}
		},
		messages: {
			requestName: {
				required: "Пожалуйста, введите Ваше имя",
				maxlength: "Не более 70 символов"
				//minlength: "Имя должно состоять более, чем из 1 буквы"
			},
			requestPhone: {
				//required: "Пожалуйста, введите номер вашего телефона",
				required: "Укажите полный номер",
				digits: "Номер телефона должен содержать только цифры",
				minlength: "Укажите полный номер"
				//maxlength: "Номер телефона не может содержать больше 11 цифр"
			},
			requestComments : {
				maxlength: "Не более 1000 символов"
			}
		},
		submitHandler: function(){
			if (!$form[0]['formType']) {
				var input = document.createElement('input');
				input.type = 'hidden';
				input.name = 'formType';
				$form[0].appendChild(input);
			}

			// ga test events
			if ( DD.ga && !$form.hasClass("callback_form")) {
				var $submit = $("[type=submit]", $form);
				var requestTrigger = $submit.data("stat-trigger");
				switch (requestTrigger) {
					case "btnCardShortDoctor":
						//запись успешна из краткой анкеты врача
						DD.ga.eventsList.requestBtnSendCardShortDoctor();
						$form[0]['formType'].value = 'ShortForm';
						//console.log("запись успешна из краткой анкеты врача");
						break

					case "btnCardFullDoctor":
						//запись пуспешна из полной анкеты врача
						DD.ga.eventsList.requestBtnSendCardFullDoctor();
						$form[0]['formType'].value = 'FullForm';
						//console.log("запись пуспешна из полной анкеты врача");
						break

					case "btnCardShortClinic":
						//запись успешна из краткой анкеты клиники
						DD.ga.eventsList.requestBtnSendCardShortClinic();
						$form[0]['formType'].value = 'ShortForm';
						//console.log("запись успешна из краткой анкеты клиники");
						break

					case "btnCardFullClinic":
						//запись успешна из полной анкеты клиники
						DD.ga.eventsList.requestBtnSendCardFullClinic();
						$form[0]['formType'].value = 'FullForm';
						//console.log("запись успешна из полной анкеты клиники");
						break

					case "btnSendSelectDoctor":
						//запись из формы подбора врача
						DD.ga.eventsList.requestBtnSendSelectDoctor();
						$form[0]['formType'].value = 'FullForm';
						break

					default:
						//что-то пошло не так
						DD.ga.eventsList.errorEvent();
						//console.log("что-то пошло не так" + " case: " + requestTrigger);
						break
				}
			}
			// ga test events END

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
				success: function(requestResponse)
				{
					var flagJsonSuccess;
					try
					{
						flagJsonSuccess = true;
					}
					catch(e) {
						flagJsonSuccess = false;

						var $generic_error = $('<label class="error_generic">При отправке формы произошла ошибка, пожалуйста, проверьте введённые данные или просто позвоните нам: </label>') ;
						var phoneNumber = $(".header_contacts__phone").text();
						var $phoneNumber = $('<span style="white-space: nowrap;">' + phoneNumber + '</span>');
						$generic_error.append($phoneNumber);

						if ( $(".error_generic").length == 0 ) {
							$generic_error.insertAfter($form);
						}
						$(".req_form__submit-btn").removeClass("s-disabled");
					}
					if (flagJsonSuccess == true) {

						// ga-event
						if ( DD.ga && !$form.hasClass("callback_form")) {
							var $submit = $("[type=submit]", $form);
							var requestTrigger = $submit.data("stat-trigger");
							switch (requestTrigger) {
								case "btnCardShortDoctor":
									//запись успешна из краткой анкеты врача
									DD.ga.eventsList.requestSuccessCardShortDoctor();
									//console.log("запись успешна из краткой анкеты врача");
									break

								case "btnCardFullDoctor":
									//запись пуспешна из полной анкеты врача
									DD.ga.eventsList.requestSuccessCardFullDoctor();
									//console.log("запись пуспешна из полной анкеты врача");
									break

								case "btnCardShortClinic":
									//запись успешна из краткой анкеты клиники
									DD.ga.eventsList.requestSuccessCardShortClinic();
									//console.log("запись успешна из краткой анкеты клиники");
									break

								case "btnCardFullClinic":
									//запись успешна из полной анкеты клиники
									DD.ga.eventsList.requestSuccessCardFullClinic();
									//console.log("запись успешна из полной анкеты клиники");
									break

								case "btnSendSelectDoctor":
									//запись успешна из полной анкеты клиники
									DD.ga.eventsList.requestSuccessSelectDoctor();
									//console.log("запись успешна из страницы подбора врача");
									break

								default:
									//что-то пошло не так
									DD.ga.eventsList.errorEvent();
									//console.log("что-то пошло не так" + " case: " + requestTrigger);
									break
							}
						}

						if ( DD.ga && $form.hasClass("callback_form")) {
							DD.ga.eventsList.requestSuccessCallmeback();
						}

						// ga-event END

						var dataObj = requestResponse;

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
									showMsgRequestSent(dataObj);
								}

								if( $(".error_generic").length > 0) {
									$(".error_generic").remove();
								}
							}

						}
						else {
							if ( requestResponse == "undefined") {
								showMsgSomethingwrong();
							}
							else {
								showMsgSomethingwrong();
							}
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


var clientRegistrationValidate = function($form){
	$form.validate({
		rules: {
			name: {
				required: true,
				onlyLetters: true,
				maxlength: 70
				//minlength: 2,
			},
			phone: {
				required: true,
				//digits: true,
				minlength: 18
				//maxlength: 18,
			},
			mode: {
				required: true
			},
			clinic: {
				required: true,
				onlyLetters: true,
				maxlength: 70
				//minlength: 2,
			},
			email: {
				required: true,
				email: true
			},
			agreed: {
				required: true
			}
		},
		messages: {
			name: {
				required: "Пожалуйста, введите Ваше имя",
				maxlength: "Не более 70 символов"
				//minlength: "Имя должно состоять более, чем из 1 буквы"
			},
			phone: {
				//required: "Пожалуйста, введите номер вашего телефона",
				required: "Укажите полный номер",
				digits: "Номер телефона должен содержать только цифры",
				minlength: "Укажите полный номер"
				//maxlength: "Номер телефона не может содержать больше 11 цифр"
			},
			mode : {
				maxlength: "Выберите один из вариантов"
			},
			clinic: {
				required: "Пожалуйста, введите название клиники",
				maxlength: "Не более 70 символов"
				//minlength: "Имя должно состоять более, чем из 1 буквы"
			},
			email : {
				required: "Пожалуйста, введите email",
				email: "Проверьте правильность email"
			},
			agreed : {
				required: "Для продолжения необходимо согласиться с условиями договора оферты"
			}
		}
	});
};



(function() {
	var $form = $(".review_form");
	var $formBtn = $('input[type="submit"]', $form);

	$form.validate({
		ignore: [],
		rules: {
			reviewName: {
				required: true,
				onlyLetters: true,
				maxlength: 70
				//minlength: 2,
			},
			reviewPhone: {
				required: true,
				//digits: true,
				minlength: 18
				//maxlength: 18
			},
			reviewComment: {
				required: true,
				minlength: 10,
				maxlength: 1000
			},
			rating_qualification: {
				required: true,
				minlength: 1
			},
			rating_attention: {
				required: true
			},
			rating_room: {
				required: true
			}
		},
		messages: {
			reviewName: {
				required: "Введите Ваше имя",
				onlyLetters: "Может состоять только из букв",
				maxlength: "Не более 70 символов"
				//minlength: "Имя должно состоять более, чем из 1 буквы"
			},
			reviewPhone: {
				//required: "Пожалуйста, введите номер вашего телефона",
				required: "Укажите полный номер",
				digits: "Номер телефона должен содержать только цифры",
				minlength: "Укажите полный номер",
				maxlength: "Номер телефона не может содержать больше 11 цифр"
			},
			reviewComment : {
				required: "Напишите ваш отзыв",
				minlength: "Не менее 10 символов",
				maxlength: "Не более 1000 символов"
			},
			rating_qualification : {
				required: "поставьте оценку"
			},
			rating_attention : {
				required: "поставьте оценку"
			},
			rating_room : {
				required: "поставьте оценку"
			}
		},
		submitHandler: function(){

			var url = $form.attr("action"); // the script where you handle the form input.

			$.ajax({
				type: "POST",
				url: url,
				data: $form.serialize(), // serializes the form's elements.
				dataType: 'JSON',
				beforeSend: function() {
					// Блокировка от повторного нажатия на "Высказаться"
					if ($form.data("submiting") != undefined) {
						return false;
					} else {
						$form.data("submiting", "1");
					}
				},
				success: function(requestResponse)
				{
					var flagJsonSuccess;
					try
					{
						flagJsonSuccess = true;
					}
					catch(e) {
						flagJsonSuccess = false;

						var $generic_error = $('<label class="error_generic">При отправке формы произошла ошибка, пожалуйста, проверьте введённые данные или просто позвоните нам: </label>') ;
						var phoneNumber = $(".header_contacts__phone").text();
						var $phoneNumber = $('<span style="white-space: nowrap;">' + phoneNumber + '</span>');
						$generic_error.append($phoneNumber);

						if ( $(".error_generic").length == 0 ) {
							$generic_error.insertAfter($form);
						}
						$(".rev_form__submit-btn").removeClass("s-disabled");
					}
					if (flagJsonSuccess == true) {
						var dataObj = requestResponse;

						if (dataObj.status == "success") {
							if (dataObj.url != false && typeof dataObj.url != 'undefined' ) {
								//alert(dataObj.url);
								window.location = dataObj.url;
							}
							else {
								showReviewSent();
								if( $(".error_generic").length > 0) {
									$(".error_generic").remove();
								}
							}

						}
						else {
							if ( requestResponse == "undefined") {
								showMsgSomethingwrong();
							}
							else {
								showMsgSomethingwrong($formBtn, $form);
							}


						}
					}

					//снимается блокировка кнопки "Высказаться"
					setTimeout(function () {
						$form.data("submiting", null);
					},1000);
				},
				error: function() {
					return;
				}

			});

			return false;
		}
	});

	if ($('.nearest_doctors_slider').length && $('.nearest_doctors_item').length > 3) {
		var widthItemSliderNearest = $('.nearest_doctors_slider').data('itemwidth') ? $('.nearest_doctors_slider').data('itemwidth') : 298;
		$('.nearest_doctors_slider').bxSlider({
			minSlides: 1,
			maxSlides: 3,
			moveSlides: 1,
			slideMargin: 0,
			pager: false,
			useCSS: false,
			slideWidth: widthItemSliderNearest
		});
	}
	if ($('.clinic_card_gallery').length && $('.clinic_card_gallery_item').length > 1) {
		$('.clinic_card_gallery').bxSlider({
			minSlides: 1,
			maxSlides: 1,
			moveSlides: 1,
			slideMargin: 0,
			pager: false,
			useCSS: false
		});
	}

	if ($('.schedule_doctor_slider').length) {
		$('.schedule_doctor_slider').bxSlider({
			minSlides: 1,
			maxSlides: 7,
			moveSlides: 1,
			slideMargin: 0,
			pager: false,
			useCSS: false,
			slideWidth: $('.schedule_doctor_slider_item:first').width(),
			infiniteLoop: false,
			hideControlOnEnd: true,
			speed: 150
		});

		$('.doctor_list .schedule_doctor_tab').click(function() {
			$(this).toggleClass('schedule_doctor_tab_active');
			$('.schedule_doctor_slider_wrap', $(this).parents('.schedule_doctor_wrap')).toggle();
		});
	}

	$('.js-ymap-ct').click(function() {
		var zoomObj = $(this).find('.js-ymap-zoom');
		$('body').css('top', -(document.documentElement.scrollTop) + 'px')
			.addClass('noscroll');

		if (!zoomObj.hasClass('popup-address-open')) {
			var $doctorMap = zoomObj.closest('section.doctor_map');
			var addressData = $('.js-map-data', $doctorMap).clone();
			var address = $('.doctor_address_wrap', $doctorMap).clone();

			addressData.removeAttr('data-draggable');

			$('.popup-address-map').append(addressData);
			$('.popup-address-items').append(address).append('<div class="doctor_address_dotted"></div>');
			$('.popup-address .doctor_address_clinic').addClass('i-address-doctor');
			if ($(".popup-address .doctor_address_item").length > 1) {
				$('.i-address-doctor').addClass('i-ext-address');

				var firstAddress = $(".popup-address .doctor_address_item:first");
				$(firstAddress).addClass('address_selector-act');
				$(firstAddress).children('.popup-address .i-address-doctor').removeClass('i-ext-address');
			}

			showMap('map_address_zoom');

			ymaps.ready(function () {

				var zoomControl = new ymaps.control.ZoomControl({
					options: {
						position: {top: 40, left: 10}
					}
				});
				yaMapObj.map_address_zoom.controls.add(zoomControl);

				$('.js-ymap-zoom').addClass('popup-address-open');
			});
		}

		ymaps.ready(function () {
			var address = $(".popup-address .doctor_address_item:first");
			if ($(".popup-address .doctor_address_item").length > 1) {
				address = $(".popup-address .address_selector-act");
			}
			yaMapObj.map_address_zoom.setCenter([address.data('latitude'), address.data('longitude')], 15);
		});

	});

	if ($(".doctor_address_item").length > 1) {
		$('.popup-address').on('click', '.doctor_address_item', function() {
			$('.popup-address .i-address-doctor').addClass('i-ext-address');

			yaMapObj.map_address_zoom.setCenter([$(this).data('latitude'), $(this).data('longitude')], 15);

			$('.popup-address .address_selector-act').removeClass('address_selector-act');
			$(this).addClass('address_selector-act');
			$(this).children('.popup-address .i-address-doctor').removeClass('i-ext-address');
		});
	}

	if ($('.doctor_list .doctor_desc').length) {
		$('.doctor_list .doctor_desc').dotdotdot({
			height: 70,
			ellipsis: "",
			after	: '<span class="doctor_desc_switch_show">&rarr;</span>'
		});
	}

	$('.doctor_desc_switch_show').click(function() {
		location.href = $(this).parents('.doctor_info').find('.doctor_name a').attr('href');
	});

})();

/* additional validator method = only letters */
jQuery.validator.addMethod("onlyLetters", function(value, element) {
	return this.optional(element) || /^[а-яёЁ 0-9 a-z-_.]+$/i.test(value);
},  "Может состоять только из букв");
/* additional validator method = only letters END */

/* form validation end */

/* show form messages */
var showMsgSomethingwrong = function($formBtn, $form){
	if ( !$formBtn || !$form ) {
		return false;
	}
	else {
		var $generic_error = $('<label class="error_generic">При отправке формы произошла ошибка, пожалуйста, проверьте введённые данные или просто позвоните нам.</label>') ;
		if ( $(".error_generic").length == 0 ) {
			$generic_error.insertAfter($form);
		}
		$formBtn.removeClass("s-disabled");
	}
};

var showMsgRequestSent = function(dataObj){
	if ($(".req_form", ".js-popup")){
		var $form = $(".req_form", ".js-popup");
		var $popup = $(".req_form").parent(".js-popup");
		if ($(".js-request-success").length > 0) {
			var $successText = $(".js-request-success");
			$successText.show();
		}
		else {
			$popup.append(dataObj.success);

			$("body").on("keyup", "#requestClientEmail", function () {
				$(".request-email-container .error").hide();
				$("#requestClientEmail").removeClass("form-error");
			});

			$("body").on("click", "#requestClientButton", function(){
				var $container = $(this).parent().parent().parent().parent();
				var clientEmail = $container.find("#requestClientEmail").val();
				if (!clientEmail) {
					$container.find(".client-email-error-empty").show();
					$container.find("#requestClientEmail").addClass("form-error");
				} else {
					$.ajax({
						url: "/routing.php?r=client/saveEmail",
						type: "POST",
						data: {clientId: dataObj.cl_id, clientEmail: clientEmail},
						success: function (data) {
							if (parseInt(data)) {
								$container.find(".popup_close").click();
							} else {
								$container.find(".client-email-error-not-correctly").show();
								$container.find("#requestClientEmail").addClass("form-error");
							}
						}
					});
				}

				return false;
			});
		}
		$form.slideUp().addClass("s-closed");
	}
	else {
		$(".req_form").html(successText);
	}
};

var showReviewSent = function(){
	var successText = "Спасибо за отзыв, Ваше мнение очень важно для нас. Отзыв отправлен на проверку и будет опубликован в том случае, если Вы записывались к врачу с помощью нашего сервиса."
	$(".review_form").html(successText);
};
/* show form messages end */

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

/* =ratingstars */
var ratingInit = function() {
	var ratingHint = "Рейтинг врача сформирован на основании следующих показателей: образование, опыт работы, научная степень, отзывы пациентов.";
	var path;
	var width;

	if ($('.js-reviews-rating-big').length > 0) {
		path = '/img/plugins/raty/yellow_big';
		width = 120;
	} else {
		path = '/img/plugins/raty';
		width = 110;
	}

	$('.js-rating').raty({
		score: function() {
			return $(this).attr('data-score');
		},
		scoreName: function() {
			return $(this).attr('data-related');
		},
		readOnly:  function() {
			return (!$(this).attr('data-editable'));
		},
		noRatedMsg: "Оценок пока нет",
		half: true,
		halfShow: true,
		path: path,
		width: width,
		space: false
	});
};

var starsReviewsInit = function() {
	var path;
	var width;

	if ($('.js-reviews-rating-big').length > 0) {
		path = '/img/plugins/raty/green_big';
		width = 75;
	} else {
		path = '/img/plugins/raty/green_small';
		width = 60;
	}

	$('.js-rating-small').raty({
		score: function() {
			return $(this).attr('data-score');
		},
		scoreName: function() {
			return $(this).attr('data-related');
		},
		readOnly:  function() {
			return (!$(this).attr('data-editable'));
		},
		noRatedMsg: "Оценок пока нет",
		half:   false,
		halfShow:   false,
		path: path,
		width: width,
		space: false
	});

	$(".js-rating-small img").click(function(){
		$(this).parent().children("label.error").remove();
	});
};


/* rating stars end */

/* phone mask */
$(function() {
	$.mask.definitions['~'] = "[+-]";$(".js-mask-phone").mask("+7 ?(999) 999-99-99");
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
				$('<p class="js-tooltip ui-bg-grey"></p>')
					.text(titleText)
					.appendTo('body')
					.css('top', (event.pageY - 10) + 'px')
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

/* Polyfills */
Modernizr.load([{
	test: Modernizr.input.placeholder,
	nope:
		[
			'/css/polyfills/polyfill_placeholder.css',
			'/js/polyfills/polyfill_placeholder.js',
			'/js/polyfills/polyfill_placeholder_onchange.js'
		]
}]);
/* Polyfills end */

/* js-autocomplete-trigger */

//data-autocomplete-id="autocomplete-spec"
$(".js-autocomplete-trigger").each(function(){
	var self = $(this);
	self.relatedInput = self.data("autocomplete-id");
	self.$relatedInput = $('input[data-autocomplete-id=' + self.relatedInput + ']');

	self.$relatedInput.focus(function(){
		$(this).autocomplete('search', '');

		if ( self.data("autocomplete-submit") == 1 ) {
			$(this).autocomplete({
				select: function() {
					$(this).closest("form").submit();
				}
			});
		}
	});

	self.click(function(){
		self.$relatedInput.focus();
	});
});

/* js-autocomplete-trigger END */


function initClinic($div) {
	if (!$div || !$div.length) return;

	$('.clinic_reviews .js-show-more', $div).click(function() {
		getMoreReviewsForClinic($div);
		return false;
	});

	$('.clinic_card_doctors .js-show-more', $div).click(function() {
		getMoreDoctorsForClinic($div, false);
		return false;
	});

	$('.clinic_card_doctors_select select', $div).change(function() {
		getMoreDoctorsForClinic($div, true);
	});
}

function getMoreReviewsForClinic($div) {
	var $link = $('.clinic_reviews .js-show-more', $div);

	$link.hide();

	$.ajax({
		url: '/clinic/moreReviews',
		data: {
			clinicId: $link.data('clinicId'),
			offset: $('.reviews .reviews_item', $div).length
		}
	}).done(function(data) {
		if (!data.success) {
			$link.show();
		}
		else if (data.reviews) {
			var countMore = parseInt($link.data('countMore'), 10);

			for (var i in data.reviews) {
				$('.js-more-reviews', $div).append(data.reviews[i]);
				countMore--;
			}

			$link.data('countMore', countMore);

			if (countMore > 0) {
				if (countMore > 10) countMore = 10;
				$link.text('и еще ' + countMore + ' ' + declension(countMore, ['отзыв', 'отзыва', 'отзывов']));
				$link.show();
			}

			starsReviewsInit();
			tooltipsInit($('.reviews', $div));
		}
	}).error(function() {
		$link.show();
	});
}

function getMoreDoctorsForClinic($div, reset) {
	var $link = $('.clinic_card_doctors .js-show-more', $div);

	$link.hide();

	$.ajax({
		url: '/clinic/moreDoctors',
		data: {
			clinicId: $link.data('clinicId'),
			offset: reset ? null : $('.doctor_list .js-doctor-short', $div).length,
			speciality: $('.clinic_card_doctors_select select', $div).val()
		}
	}).done(function(data) {
		if (!data.success) {
			$link.show();
		}
		else if (data.doctors) {
			var countMore = parseInt($link.data('countMore'), 10);

			if (reset) {
				countMore = data.countAll;
				$('.doctor_list', $div).empty();
				$('.clinic_card_doctors_count', $div).text(data.countAll + ' ' + declension(data.countAll, ['врач', 'врача', 'врачей']));
			}

			for (var i in data.doctors) {
				$('.doctor_list', $div).append(data.doctors[i]);
				countMore--;
			}

			$link.data('countMore', countMore);

			if (countMore > 0) {
				if (countMore > 10) countMore = 10;
				$link.text('показать еще ' + countMore + ' ' + declension(countMore, ['врача', 'врачей', 'врачей']));
				$link.show();
			}

			tooltipsInit($('.doctor_list', $div));
		}
	}).error(function() {
		$link.show();
	});
}


/* window ready events */
$().ready(function() {

	/* request forms validation */
	$(".req_form").each(function(){
			requestValidate($(this));
		}
	);
	/* request forms validation */

	/* client registration form validation */
	if ( $(".client_reg_form").length > 0 ) {
		clientRegistrationValidate( $(".client_reg_form") );
	}
	/* client registration form validation */

	/* slidedown init */
	slidedownInit($(".js-slidedown-tr"));
	/* slidedown init end */

	/* tabs init */
	tabsInit();
	/* tabs init END */

	/* showall init */
	initShowall();
	/* showall init END */

	/* ratings init */
	ratingInit();
	starsReviewsInit();
	/* ratings init END */

	/* tooltips init */
	tooltipsInit();
	/* tooltips init END */

	/* callmeback init */
	callmebackInit();
	/* callmeback init END */

	/* howto init */
	howtoInit();
	/* howto init END */

	$('.page_press_alllonk').click(function() {
		$(this).hide();
		$('.page_press_more').slideDown(400);
	});
	$('.page_faq_head').click(function() {
		$('.page_faq_wrap .page_faq_text:visible').slideUp(400);
		$(this).siblings('.page_faq_text').stop(true, true).slideToggle(400);
	});
	$('.doctor_address_switch_show').click(function() {
		$(this).prev().slideDown(400);
		$(this).hide();
	});

	initClinic($('.js-clinic-full'));
});
/* window ready events END */

// возвращает cookie с именем name, если есть, если нет, то undefined
function getCookie(name) {
	var matches = document.cookie.match(new RegExp("(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"));
	return matches ? decodeURIComponent(matches[1]) : undefined;
}

// Склонение окончаний
function declension(num, expressions) {
	var count = num % 100;
	if (count >= 5 && count <= 20) return expressions['2'];
	count = count % 10;
	return expressions[(count == 1) ? '0' : (count >= 2 && count <= 4 ? '1' : '2')];
}