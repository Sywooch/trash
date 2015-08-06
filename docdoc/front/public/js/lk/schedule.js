/**
 * управление расписанием врача
 */
$(document).ready(function() {

	$("[name=schedule_start_date]").datepicker();

	var doc_id = $("#sc_doctor_id").val();
	var clinic_id = $("#sc_clinic_id").val();
	var minEventDate = moment();
	var maxEventDate =  moment();
	var isEndDateChanged = true;


	$(document).on("calendarDateInervalChange", function() {

		//если есть что копировать
		if (calendar.fullCalendar('clientEvents').length > 0) {
			$("#copy_filter").removeClass("hide-copy-params");
		} else {
			return;
		}

		drawCopyWeek();
		isEndDateChanged = false;
	});

	var options = {
		timezone: "local",
		theme: false,
		allDaySlot: false,
		header: {
			left: 'prev,next today',
			center: 'title',
			right: 'agendaWeek, month'
		},
		defaultView: 'agendaWeek',
		columnFormat:{
			month: 'ddd',    // Mon
			week: 'ddd, D.MM', // Mon 9/7
			day: 'dddd D.MM'  // Monday 9/7
		},
		buttonText:{
			today:    'Сегодня',
			month:    'Месяц',
			week:     'Неделя'
		},
		schedule_step: 30,
		slotDuration: '00:30:00',
		monthNames: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль',
			'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
		monthNamesShort: ['Янв', 'Фев', 'Март', 'Апр', 'Май', 'Июнь',
			'Июль', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'],
		dayNamesShort: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
		firstDay: 1,
		weekends: true,
		timeFormat: 'H:mm',
		firstHour:8,
		axisFormat:'H:mm',
		editable: true,
		selectable: true,
		selectHelper: true,
		//при выборе интервала или дня
		select: function(start, end) {
			//нельзя создавать события в прошлом
			//нельзя создавать события на несколько дней
			if(isPast(start) || start.diff(end, 'days') > 0 ) {
				calendar.fullCalendar('unselect');
				return false;
			}

			//если клик в view = month, открываем этот  день во view = week
			if (calendar.fullCalendar('getView').name == "month") {
				calendar.fullCalendar('changeView' , 'agendaWeek');
				calendar.fullCalendar('gotoDate' , start);
				return false;
			}

			//проверяем, что события не пересекаются
			var exists_event = isIntersectEvent(start, end);
			var new_event = null;
			if (exists_event) {

				new_event = {
					allDay: false,
					start: moment(exists_event.start),
					end: moment(exists_event.end)
				};
				//все старое, что пересекается удалем
				calendar.fullCalendar( 'removeEvents', function(e){
					if (e._id ==  exists_event._id) {
						return true;
					}
				});
				//отменяем вставку
				calendar.fullCalendar('unselect');
			}

			if (!new_event) {
				new_event = {
					start: start,
					end: end,
					allDay: false
				};
			}

			calendar.fullCalendar('renderEvent',
				new_event,
				true
			);

			//Сохраняем
			saveEvents();
		},
		//после рендеринга события
		eventAfterRender: function (event, element) {

			// будущие собыия можно удалять
			var close_btn = '';
			if (moment().format("YYYY-MM-DD") <= event.start.format("YYYY-MM-DD")) {
				close_btn = '<button type="button" class="fc-delete-button" data-event-id="' + event._id + '">Х</button>';
			}

			element.find('.fc-event-time').html(
				moment(event.start).format('HH:mm') + " - "+
				moment(event.end).format('HH:mm') + close_btn
			);

			//если появилось события с максимальной датой больше, чем есть, ставим флаг, что дата изменилась
			if (event.end.format("YYYY-MM-DD") > maxEventDate.format("YYYY-MM-DD")) {
				maxEventDate = moment(event.end);
				isEndDateChanged = true;
			}

		},
		//серым отмечаем прошлые дни в календаре
		dayRender: function (date, cell) {
			if (isPast(date)) {
				$(cell).addClass("past-day");
			}
		},
		//при перетаскивании
		eventDrop: function( event, revertFunc, jsEvent, ui, view) {
			//нельзя перетаскивать события в прошлое
			//нельзя перетаскивать события в view=month
			//нельзя переносить на другой день
			if( isPast(event.start) || view.name == "month") {
				revertFunc();
				return;
			}

			//проверяем, что события не пересекаются
			var exists_event =  isIntersectEvent(
				event.start,
				event.end,
				event._id
			);

			//если события пересекаются
			if (exists_event) {

				var new_event = {
					allDay:false,
					start:moment(exists_event.start),
					end:moment(exists_event.end)
				};
				//все старое, что пересекается удалем
				calendar.fullCalendar( 'removeEvents', function(e){
					if (e._id == event._id || e._id ==  exists_event._id) {
						return true;
					}
				});
				//отменяем вставку
				revertFunc();

				calendar.fullCalendar('renderEvent',
					new_event,
					true);
			}

			//Сохраняем
			saveEvents();
		},
		eventResize: function() {
			//Сохраняем
			saveEvents();
		},
		//после рендеринга всех событий
		eventAfterAllRender: function(view) {
			//для недельного календаря помечаем прошлые даты неактивными
			if (view.name == "agendaWeek" && view.start.diff(new Date(), 'days') < 0) {

				var days = moment().diff(view.start, 'days');
				for (var i = 0; i < days; i++) {
					$(".fc-col"+i).addClass("past-day");
				}
			}

			//перерисовываем селекты с доступными неделями
			if (isEndDateChanged) {
				$(document).trigger("calendarDateInervalChange");
			}
		},
		events: []
	};

	var calendar =  $('#full_calendar').find(".calendar_container");


	/**
	 * Проверка, является ли дата предыдущим днем
	 *
	 * @param {moment} dt
	 * @returns {boolean}
	 */
	function isPast(dt) {

		return (dt.format('YYYY-MM-DD') < moment().format('YYYY-MM-DD'));
	}

	/**
	 * Получение параметров формы
	 *
	 * @returns {{copyWeek: (*|jQuery), copyStartDate: (*|jQuery), copyWeekNum: (*|jQuery), schedule_step: (*|jQuery), calendarScale: (*|jQuery)}}
	 */
	function getRules()
	{
		return {
			copyWeek: $("#copyWeek").val(),
			copyStartDate: $("#copyStartDate").val(),
			copyWeekNum: $("#copyWeekNum").val(),
			schedule_step: $("#schedule_step").val(),
			calendarScale:  $("#calendarScale").val()
		};
	}

	/**
	 *  Поиск пересечения события с другими соытия
	 *  Если пересечение найдеено, возвращает крайние даты от двух событий
	 *
	 * @param {moment} start
	 * @param {moment} end
	 * @param {string} id
	 * @returns {*}
	 */
	function isIntersectEvent(start, end, id)
	{
		//проверяем, что события не пересекаются
		var events = calendar.fullCalendar('clientEvents');
		var new_event = null;
		var e_start, e_end, d_start, d_end;
		for (var i = 0; i < events.length; i++) {

			if (events[i]._id == id) {
				continue;
			}

			//могут быть разные метки временных зон
			//у события и при drop
			// чтобы не убиться сравниваем строки
			e_start = events[i].start.format("YYYY-MM-DD HH:mm:ss");
			e_end = events[i].end.format("YYYY-MM-DD HH:mm:ss");
			d_start = start.format("YYYY-MM-DD HH:mm:ss");
			d_end = end.format("YYYY-MM-DD HH:mm:ss");


			//передвигаемое событие полностью внутри другого события
			if (e_start <= d_start && e_end >=  d_end)
			{
				calendar.fullCalendar( 'removeEvents', id);
				continue;
			}

			//передвигаемое полность закрывает другое событие
			if (e_start >= d_start && e_end <= d_end)
			{
				calendar.fullCalendar( 'removeEvents', events[i]._id );
				continue;
			}

			//пересечение по одной стороне
			if ((e_start <= d_start && e_end >= d_start) ||
				(e_start <= d_end && e_end >= d_end )
				) {

				new_event = events[i];
				new_event.start = (e_start >= d_start) ? start : events[i].start;
				new_event.end = (e_end > d_end) ? events[i].end : end;
			}
		}
		return new_event;
	}

	/**
	 * Копирование событийс недели, начинающейся с monday  на week недель вперед, начиная с diff недель от недели monday
	 *
	 * @param {moment} monday
	 * @param {int} diff
	 * @param {int} weeks
	 */
	function copyWeekEvents(monday, diff, weeks)
	{
		var sunday = moment(monday).add('days', 6);

		//очищаем существующие события
		var from = moment(monday).add('days', diff).format("YYYY-MM-DD");
		var to =  moment(monday).add('days', diff + 7 * weeks).format("YYYY-MM-DD");


		var events = calendar.fullCalendar('clientEvents');
		options.events = [];
		var copy_events = [];

		for (var i=0; i < events.length; i++) {

			//выбираем события, которые нужно скопировать
			if (events[i].start.format("YYYY-MM-DD") >= monday.format("YYYY-MM-DD") && events[i].start.format("YYYY-MM-DD") <= sunday.format("YYYY-MM-DD")) {
				copy_events.push(events[i]);
			}

			//если на неделе, куда будут скопированы события что-то есть, то не включаем их в массив новых событий
			if (events[i].start.format("YYYY-MM-DD") < from || events[i].start.format("YYYY-MM-DD") > to) {
				options.events.push({
					allDay : false,
					start: events[i].start,
					end: events[i].end
				});
			}
		}

		//переносим их на week недель вперед
		var evnt;
		for (var j = 0; j < weeks; j++) {
			for (var i=0; i<copy_events.length; i++) {
				evnt = {allDay : false};
				evnt.start = moment(copy_events[i].start).add('days', diff + 7 * j);
				evnt.end = moment(copy_events[i].end).add('days', diff + 7 * j);
				options.events.push(evnt);
			}
		}

		destroyCalendar();
		calendar.fullCalendar(options);
		calendar.fullCalendar( 'changeView', 'month' );

		//Сохраняем
		saveEvents(options.events);
	}

	/**
	 *  перерисовка "Скопировать расписание за неделю"
	 *
	 */
	function drawCopyWeek()
	{
		//отсчет у нас начинается с понедельника
		if (minEventDate.format('e') > 1) {
			minEventDate.add('days', 1 - minEventDate.format('e'));
		}
		//если воскресенье
		if (minEventDate.format('e') == 0) {
			minEventDate.add('days', -6);
		}

		//отсчет заканчивается воскресеньем
		if (maxEventDate.format('e') > 0) {
			maxEventDate.add('days' , 6 - maxEventDate.format('e'));
		}


		//определяем количество недель между конечными точками
		var weeks = (maxEventDate.diff(minEventDate, 'days')+2)/7;

		var copyWeeks = '';
		var monday;
		var today = new Date();

		for (var i=0; i<weeks; i++) {
			monday = moment(minEventDate).add('days', 7 * i);

			copyWeeks += '<option value="' + monday.format('YYYY-MM-DD') + '">' +
				monday.format('DD.MM.YYYY') +
				' - ' + monday.add('days', 6).format('DD.MM.YYYY') + '</option>';
		}
		$("#copyWeek").html(copyWeeks);

		drawCopyStartDate();
	}

	/**
	 *  перерисовка "Начиная с даты"
	 *
	 */
	function drawCopyStartDate()
	{
		//проставляем "начиная с даты" на 3 недели вперед от выбраного значения
		var startWeeks = '';
		var next_monday =  moment(new Date($("#copyWeek").val())).add('days', 7);
		for (var i = 0; i<3; i++) {
			monday = moment(next_monday).add('days', 7 * i);

			startWeeks += '<option value="' + monday.format('YYYY-MM-DD') + '">Понедельник, ' +
				monday.format('DD.MM.YYYY') + '</option>';
		}

		$("#copyStartDate").html(startWeeks);

	}

	$("#copyButon").click(function() {
		var rules = getRules();
		var monday = moment(new Date(rules.copyWeek));
		var diff = moment(rules.copyStartDate).diff(monday, 'days') + 1;

		if (diff > 0) {

			copyWeekEvents(monday, diff, rules.copyWeekNum);
		}

		return false;
	});

	$("#copyWeek").change(drawCopyStartDate);
	$("#calendarScale").change(
		function() {
			options.slotDuration = '00:' + $(this).val() + ':00';
			options.events = calendar.fullCalendar('clientEvents');
			calendar.fullCalendar('destroy');

			$('#full_calendar').html('<div class="calendar_container"></div>')
			calendar =  $('#full_calendar').find(".calendar_container");
			calendar.fullCalendar(options);
		}
	);

	//при изменении продолжительности приема
	$("#schedule_step").change(
		function() {
			$("#scheduleAlert").show('fast');
		}
	);

	$("#changeDuration").click(function() {

		options.schedule_step = $("#schedule_step").val();
		saveEvents(calendar.fullCalendar('clientEvents'),
			function() {
				destroyCalendar();
				loadCalendar();
			}
		);
		$("#scheduleAlert").hide('fast');
	});

	$("#cancelChangeDuration").click(function() {
		$("#schedule_step").val(options.schedule_step);
		$("#scheduleAlert").hide('fast');
	});

	/**
	 * сохранеине событий
	 *
	 * @param events
	 */
	function saveEvents(events, callback)
	{
		if (events == undefined) {
			events = calendar.fullCalendar('clientEvents');
		}

		var send = [];

		for (var i = 0; i < events.length; i++) {
			send.push({
				start: events[i].start.format("YYYY-MM-DD HH:mm:ss"),
				end: events[i].end.format("YYYY-MM-DD HH:mm:ss")
			})
		}
		//загрузка расписания врача
		$.post('/lk/schedule/save?doctorId=' + doc_id + '&clinicId=' + clinic_id,
			{
				events: send,
				rules: getRules()
			},
			function(data) {
				if (callback != undefined) {
					callback();
				}
			}
		);
	}

	function destroyCalendar()
	{
		calendar.fullCalendar('destroy');
		$('#full_calendar').html('<div class="calendar_container"></div>')
		calendar =  $('#full_calendar').find(".calendar_container");
	}

	/**
	 * загрузка расписания врача
	 */
	function loadCalendar()
	{
		$.post('/lk/schedule/calendar?doctorId=' + doc_id + '&clinicId=' + clinic_id, null, function(events) {

			var today = moment().format("YYYY-MM-DD");

			//дисаблим все прошедшие события
			for (var i = 0; i < events.events.length; i++) {
				if (events.events[i].start < today) {
					events.events[i].className = "past-event";
					events.events[i].editable  = false;
					if (minEventDate.format("YYYY-MM-DD") > events.events[i].start) {
						minEventDate = moment(events.events[i].start);
					}
				}
			}
			options.schedule_step = $("#schedule_step").val();
			options.events = events.events;
			options.slotDuration = '00:' + $("#calendarScale").val() + ':00';
			maxEventDate = (events.maxEventDate) ? moment(events.maxEventDate) : moment();
			calendar.fullCalendar(options);
		}, 'json');

	}


	// при удалении события
	$(document).on('click', '.fc-delete-button',
		function(jsEvent, event_id) {
			calendar.fullCalendar( 'removeEvents', [$(this).data('event-id')] );
			//Сохраняем
			saveEvents();
		}
	);

	loadCalendar();


});
