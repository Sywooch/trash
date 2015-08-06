
var DD = {};

DD.ga = {};
DD.ga.eventsList = {};

DD.ga.bindEvent = function($element, gaEvent) {
	$element.click(gaEvent);
};

DD.ga.bindEventEarly = function(element, gaEvent) {
	$(document).on('click', element, gaEvent);
};

//нажатие (клик) на кнопку записаться из краткой анкеты врача
DD.ga.eventsList.requestBtnCardShortDoctor = function() {
	ga('send', 'event', 'nazapis', 'perehod', 'short');
};

//нажатие (клик) на кнопку записаться из полной анкеты врача
DD.ga.eventsList.requestBtnCardFullDoctor = function() {
	ga('send', 'event', 'nazapis', 'perehod', 'large');
};

//запись после нажатия на кнопку из краткой анкет врача
DD.ga.eventsList.requestSuccessCardShortDoctor = function() {
	ga('send', 'event', 'zapis', 'click', 'click on short');
};

//запись после нажатия на кнопку из полной анкеты врача
DD.ga.eventsList.requestSuccessCardFullDoctor = function() {
	ga('send', 'event', 'zapis', 'click', 'click na zapis');
};


//отправка телефона после нажатия перезвоните мне
DD.ga.eventsList.requestSuccessCallmeback = function() {
	ga('send', 'event', 'zapis', 'click', 'callback');
};


// отслеживание отправки заявки без ожидания ответа сервера

//отправка записи после нажатия на кнопку из краткой анкет врача
DD.ga.eventsList.requestBtnSendCardShortDoctor = function() {
	ga('send', 'event', 'send', 'click', 'click on short');
};

//отправка записи после нажатия на кнопку из полной анкеты врача
DD.ga.eventsList.requestBtnSendCardFullDoctor = function() {
	ga('send', 'event', 'send', 'click', 'click on full');
};


// error
DD.ga.eventsList.errorEvent = function(){
	ga('send', 'event', 'error', 'fall', 'script error');
};


$(function() {
	DD.ga.bindEvent($('div.to-form-doctor-btn a'), DD.ga.eventsList.requestBtnCardFullDoctor);

	$(document)
		.on('requestSend', function(e, form, data) {
			DD.ga.eventsList.requestBtnSendCardFullDoctor();
		})
		.on('requestCreated', function(e, form, data, params) {
			DD.ga.eventsList.requestSuccessCardFullDoctor();
		});
});
