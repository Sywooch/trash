/**
 * Отправка метрики при успешной заявке (для 2Gis)
 */
function sendMetric() {
	try{
		ga('send', 'event', 'order', 'sent');
	}
	catch(e) {
		console.log(e);
	}
}

/**
 * Отображение блока - услуга
 */
function showServices() {
	$(".steps").children().removeClass("active");
	$(".js-service-tab").addClass("active");
	$(".js-block").hide();
	$(".js-services").show();
	$(".js-next").removeClass("hidden");
	$(".js-book, .js-back").addClass("hidden");
	$(".js-doctors .scroll").getNiceScroll().hide();
	$(".js-services .scroll").getNiceScroll().show();
}

/**
 * Отображение блока - специалист
 */
function showDoctors() {
	$.ajax({
		url: '/appointment/doctors',
		type: "POST",
		data: {
			'clinicId': $("#clinicId").val(),
			'specId': $("#specId").val()
		},
		success: function(data)
		{
			initShowDoctors(data);
		}
	});
}

/**
 * Отображение блока - контакты
 */
function showContactsForm() {
	$(".js-services .scroll, .js-doctors .scroll").getNiceScroll().hide();

	$(".steps").children().removeClass("active");
	$(".js-contacts-form-tab").addClass("active");
	$(".js-block").hide();
	$(".js-contacts-form").show();
	$(".js-back, .js-book").removeClass("hidden");
	$(".js-next").addClass("hidden");

	$.mask.definitions['~'] = "[+-]";
	$(".js-mask-phone").mask("+7 ?(999) 999-99-99");
}

/**
 * Отображение успешно созданной заявки
 */
function showSuccess() {
	$(".steps, .js-contacts-form").hide();
	$(".js-success").show();
	$(".btn").addClass("hidden");
}

/**
 * Инициализация кнопок
 */
var initButtons = function(){
	$(".js-back").click(function(){
		$("#doctorId").val('');
		if ($("#diagnosticId").val() > 0){
			showServices();
		} else {
			if ($(".js-contacts-form-tab").hasClass("active")) {
				showDoctors();
			} else {
				showServices();
			}
		}
	});
	$(".js-next").click(function(){
		if ($(".js-service-tab").hasClass("active")) {
			showDoctors();
		} else if ($(".js-doctor-tab").hasClass("active")) {
			showContactsForm();
		}
	});
}

/**
 * Инициализация выбора специалиста
 */
var initShowDoctors = function(data){
	$(".js-services .scroll").getNiceScroll().hide();

	$(".steps").children().removeClass("active");
	$(".js-doctor-tab").addClass("active");
	$(".js-block").hide();
	$(".js-doctors").html(data).show();
	$(".js-back, .js-next").removeClass("hidden");
	$(".js-book").addClass("hidden");

	$('.js-doctors .scroll').niceScroll({
		cursorcolor: "#ccc",
		autohidemode: false,
		cursorwidth: "7px"
	});

	$(".js-doctor").click(function(){
		$("#doctorId").val($(this).data("id"));
		showContactsForm();
	});
}

/**
 * Инициализация выбора услуги
 */
var initShowServices = function(){
	$(".js-service").click(function(){
		var id = $(this).data("id");
		$(".js-service").removeClass("active");
		$(this).addClass("active");
		$(".js-services").hide();
		$("#specId, #diagnosticId").val('');
		if ($(this).data("type") == "spec") {
			$("#specId").val(id);
			showDoctors();
		} else if ($(this).data("type") == "diag") {
			$("#diagnosticId").val(id);
			showContactsForm();
		}
	});
}

/**
 * Валидация отправка формы заявки
 *
 * @param $form
 */
var requestValidate = function($form){
	$(".js-book").click(function(){
		$form.submit();
	})

	$form.validate({
		rules: {
			requestName: {
				required: true,
				onlyLetters: true,
				maxlength: 70
			},
			requestPhone: {
				required: true,
				minlength: 18
			},
			requestComments: {
				maxlength: 1000
			}
		},
		messages: {
			requestName: {
				required: "Пожалуйста, введите Ваше имя"
			},
			requestPhone: {
				required: "Укажите полный номер",
				minlength: "Укажите полный номер"
			},
			requestComments : {
				maxlength: "Не более 1000 символов"
			}
		},
		submitHandler: function(){
			var url = $form.attr("action")
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
				success: function(requestResponse) {
					showSuccess();
					sendMetric();
				}
			});
		}
	});
};

/* additional validator method = only letters */
jQuery.validator.addMethod("onlyLetters", function(value, element) {
	return this.optional(element) || /^[а-яёЁ 0-9 a-z-_.]+$/i.test(value);
},  "Может состоять только из букв");
/* additional validator method = only letters END */

$().ready(function() {

	$('.js-services .scroll').niceScroll({
		cursorcolor: "#ccc",
		autohidemode: false,
		cursorwidth: "7px"
	});

	initShowServices();

	initButtons();

	if ($(".request-form").length > 0) {
		requestValidate($(".request-form"));
	}

});