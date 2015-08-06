/* ga */
$(document).ready(function() {

DD.ga = {}

DD.ga.eventsList = {}
DD.ga.bindEvent = function($element, gaEvent){
	$element.click(gaEvent);
};

DD.ga.bindEventEarly = function(element, gaEvent) {
	$(document).on('click', element, gaEvent);
};

DD.ga.bindEvents = function() {
	$(document).on("click", "[data-stat]", function() {
		var handler = $(this).data('stat');
		if (DD.ga.eventsList[handler]) {
			DD.ga.eventsList[handler]();
		}
	});
};

/*
DD.ga.bindEventEarly = function($element, gaEvent) {
	$(document).on('click', button, function(e){
		DD.ga.bindEvent($element, gaEvent);
	}
)};
*/

// events list

//нажатие (клик) на кнопку записаться из краткой анкеты врача
DD.ga.eventsList.btnCardShortDoctor = function(){
	ga('send', 'event', 'nazapis', 'perehod', 'short');
}
//нажатие (клик) на кнопку записаться из краткой анкеты врача END

//нажатие (клик) на кнопку записаться из полной анкеты врача
DD.ga.eventsList.btnCardFullDoctor = function(){
	ga('send', 'event', 'nazapis', 'perehod', 'large');
}
//нажатие (клик) на кнопку записаться из полной анкеты врача END

//нажатие (клик) на кнопку записаться из краткой анкеты клиники
DD.ga.eventsList.btnCardShortClinic = function(){
	ga('send', 'event', 'nazapis', 'perehod', 'short');
}
//нажатие (клик) на кнопку записаться из краткой анкеты клиники END

//нажатие (клик) на кнопку записаться из полной анкеты клиники
DD.ga.eventsList.btnCardFullClinic = function(){
	ga('send', 'event', 'nazapis', 'perehod', 'large');
}
//нажатие (клик) на кнопку записаться из полной анкеты клиники END

//запись после нажатия на кнопку из краткой анкет врача
DD.ga.eventsList.requestSuccessCardShortDoctor = function(){
	ga('send', 'event', 'zapis', 'click', 'click on short');
}
//запись после нажатия на кнопку из краткой анкет врача END

//запись после нажатия на кнопку из полной анкеты врача
DD.ga.eventsList.requestSuccessCardFullDoctor = function(){
	ga('send', 'event', 'zapis', 'click', 'click na zapis');
}
//запись после нажатия на кнопку из полной анкеты врача END

//запись после нажатия на кнопку из краткой анкет клиники
DD.ga.eventsList.requestSuccessCardShortClinic = function(){
	ga('send', 'event', 'zapis', 'click', 'click on short clinic');
}
//запись после нажатия на кнопку из краткой анкет клиники END

//запись после нажатия на кнопку из полной анкеты клиники
DD.ga.eventsList.requestSuccessCardFullClinic = function(){
	ga('send', 'event', 'zapis', 'click', 'click on full clinic');
}
//запись после нажатия на кнопку из полной анкеты клиники END

//отправка записи после нажатия на кнопку из подбора врача (/request)
DD.ga.eventsList.requestSuccessSelectDoctor = function(){
	ga('send', 'event', 'zapis', 'click', 'click on select doctor');
}
//отправка записи после нажатия на кнопку из подбора врача (/request) END

//клик на выбор гео c попаданием в pop-up
DD.ga.eventsList.btnPopupGeo = function(){
	ga('send', 'event', 'pop-up', 'prosmotr', 'karta metro');
}
//клик на выбор гео c попаданием в pop-up END

//нажатие на tab список станций в pop-up
DD.ga.eventsList.tabListStations = function(){
	ga('send', 'event', 'pop-up', 'prosmotr', 'spisok metro');
}
//нажатие на tab список станций в pop-up END

//нажатие на tab список районов в pop-up
DD.ga.eventsList.tabListRegions = function(){
	ga('send', 'event', 'pop-up', 'prosmotr', 'area');
}
//нажатие на tab список районов в pop-up END

//отправка телефона после нажатия перезвоните мне
DD.ga.eventsList.requestSuccessCallmeback = function(){
	ga('send', 'event', 'zapis', 'click', 'callback');
}
//отправка телефона после нажатия перезвоните мне END

// отслеживание отправки заявки без ожидания ответа сервера
//отправка записи после нажатия на кнопку из краткой анкет врача
DD.ga.eventsList.requestBtnSendCardShortDoctor = function(){
	ga('send', 'event', 'send', 'click', 'click on short');
}
//отправка записи после нажатия на кнопку из краткой анкет врача END

//отправка записи после нажатия на кнопку из полной анкеты врача
DD.ga.eventsList.requestBtnSendCardFullDoctor = function(){
	ga('send', 'event', 'send', 'click', 'click on full');
}
//отправка записи после нажатия на кнопку из полной анкеты врача END

//отправка записи после нажатия на кнопку из краткой анкет клиники
DD.ga.eventsList.requestBtnSendCardShortClinic = function(){
	ga('send', 'event', 'send', 'click', 'click on short clinic');
}
//отправка записи после нажатия на кнопку из краткой анкет клиники END

//отправка записи после нажатия на кнопку из полной анкеты клиники
DD.ga.eventsList.requestBtnSendCardFullClinic = function(){
	ga('send', 'event', 'send', 'click', 'click on full clinic');
}
//отправка записи после нажатия на кнопку из полной анкеты клиники END

//отправка записи после нажатия на кнопку из подбора врача (/request)
DD.ga.eventsList.requestBtnSendSelectDoctor = function(){
	ga('send', 'event', 'send', 'click', 'click on select doctor');
}
//отправка записи после нажатия на кнопку из подбора врача (/request) END

//нажатие (клик) на кнопку записаться из краткой анкеты врача
DD.ga.eventsList.btnCardShortDoctorOnline = function(){
	ga('send', 'event', 'online-nazapis', 'perehod', 'short');
}
//нажатие (клик) на кнопку записаться из краткой анкеты врача END

//нажатие (клик) на кнопку записаться из полной анкеты врача
DD.ga.eventsList.btnCardFullDoctorOnline = function(){
	ga('send', 'event', 'online-nazapis', 'perehod', 'large');
}
//нажатие (клик) на кнопку записаться из полной анкеты врача END

//нажатие (клик) на кнопку расписания из краткой анкеты врача
DD.ga.eventsList.btnCardShortScheduleOnline = function(){
	ga('send', 'event', 'online-nazapis', 'perehod', 'short-raspisanie');
}
//нажатие (клик) на кнопку расписания из краткой анкеты врача END

//нажатие (клик) на кнопку расписания из полной анкеты врача
DD.ga.eventsList.btnCardFullScheduleOnline = function(){
	ga('send', 'event', 'online-nazapis', 'perehod', 'large-raspisanie');
}
//нажатие (клик) на кнопку расписания из полной анкеты врача END


//запись после нажатия на кнопку из краткой анкет врача
DD.ga.eventsList.btnCardShortDoctorOnlineRequest = function(){
	ga('send', 'event', 'online-zapis', 'click', 'click on short');
}
//запись после нажатия на кнопку из краткой анкет врача END

//запись после нажатия на кнопку из полной анкеты врача
DD.ga.eventsList.btnCardFullDoctorOnlineRequest = function(){
	ga('send', 'event', 'online-zapis', 'click', 'click na zapis');
}
//запись после нажатия на кнопку из полной анкеты врача END

//запись после нажатия на кнопку  расписание из краткой анкеты врача
DD.ga.eventsList.btnCardShortScheduleOnlineRequest = function(){
	ga('send', 'event', 'online-zapis', 'click', 'click on short raspisanie');
}
//запись после нажатия на кнопку расписание из краткой анкеты врача END

//запись после нажатия на кнопку расписание из полной анкеты врача
DD.ga.eventsList.btnCardFullScheduleOnlineRequest = function(){
	ga('send', 'event', 'online-zapis', 'click', 'click on full raspisanie');
}

// error
DD.ga.eventsList.errorEvent = function(){
	ga('send', 'event', 'error', 'fall', 'script error');
}

DD.ga.bindEvents();
});

// events binding END


/* ga END */