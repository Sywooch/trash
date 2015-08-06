function BookingSlider(srcElement) {

	var calendarMonthRu = [ 'января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря' ];
	var calendarDayRu = [ 'Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота' ];


	var SelectDate = function(element, input) {
		this.$div = $(element);
		this.$current = $('.b-select_current', element);
		this.$wrap = $('.b-select_wrap', element);
		this.$list = $('.b-select_list', element);
		this.$input = $(input);

		this.value = this.$input.val();

		this.initEvents();

		this.selectVal($('.active-date', this.$list));
	};

	SelectDate.prototype.on = function(events, handler)
	{
		this.$div.on(events, handler);

		return this;
	};

	SelectDate.prototype.showList = function()
	{
		var select = this;
		this.$list.slideDown(1, function() {
			select.$wrap.addClass('b-select_list__open');
		});

		return this;
	};

	SelectDate.prototype.hideList = function()
	{
		this.$list.hide();
		this.$wrap.removeClass('b-select_list__open');

		return this;
	};

	SelectDate.prototype.selectVal = function($element)
	{
		$('.b-select_list__act', this.$list).removeClass('b-select_list__act');
		$element.addClass('b-select_list__act');

		this.$current.text($element.text());

		this.hideList();

		this.$input.val($element.data('date'));
		this.$input.trigger('change');

		return this;
	};

	SelectDate.prototype.initEvents = function()
	{
		var select = this;

		$('body').click(function() {
			select.hideList();
		});

		$('.b-select_current,.b-select_arr', this.$div).click(function() {
			select.showList();
			return false;
		});

		$('.b-select_list__item', this.$list).click(function() {
			select.selectVal($(this));
		});

		$('.request_popup_select_date_ico', this.$div).click(function() {
			select.$input.focus().blur();
		});

		this.$input.change(function() {
			if (select.value != this.value) {
				select.value = this.value;
				select.$input.trigger('changeDate');
			}
		});

		var dates = this.$input.data('dates');
		this.$input.datetimepicker({
			format: 'd-m-Y',
			timepicker: false,
			lang:'ru',
			closeOnDateSelect: true,
			todayButton: false,
			dayOfWeekStart: 1,
			className: 'request_popup_datepicker',
			fixed: true,
			onSelectDate: function(ct, $input) {
				select.$current.text(
					calendarDayRu[ct.dateFormat('w')] + ', ' +
					ct.dateFormat('j') + ' ' +
					calendarMonthRu[ct.dateFormat('n') - 1] + ' ' +
					ct.dateFormat('Y')
				);
			},
			onGenerate:function( ct ){
				$(this).find('.xdsoft_date')
					.addClass('xdsoft_disabled');

				$(this).find('.xdsoft_date').each(
					function() {
						var d = $(this).data('date');
						var m = $(this).data("month") + 1;
						var y = $(this).data("year");
						d = d < 10 ? '0'+d : d;
						m = m < 10 ? '0'+m : m;
						d = d+"-"+m+"-"+y;
						if (dates[d])
							$(this).removeClass('xdsoft_disabled');
					}
				);
			}
		});

		return this;
	};


	var SelectSlot = function(element, input) {
		this.$div = $(element);
		this.$list = $('.request_popup_time_list', element);
		this.$prev = $('.request_popup_time_prev', element);
		this.$next = $('.request_popup_time_next', element);
		this.$input = $(input);

		this.countOnPage = 20;
		this.url = null;

		this.isLoaded = false;

		this.clinicId = null;
		this.doctorId = null;
		this.workDate = null;

		this.value = this.$input.val();
		this.slotId = null;

		this.initEvents();
	};

	SelectSlot.prototype.on = function(events, handler)
	{
		this.$div.on(events, handler);

		return this;
	};

	SelectSlot.prototype.loadSlots = function()
	{
		var select = this;

		this.showSlots();

		if (this.workDate && (this.clinicId || this.doctorId)) {
			this.isLoaded = true;

			$.ajax({
				type: 'POST',
				url: this.url,
				data: {
					clinicId: this.clinicId,
					doctorId: this.doctorId,
					workDate: this.workDate
				},
				success: function (response) {
					var slots = response.slots ? response.slots[select.workDate] : null;

					select.showSlots(slots);
				}
			});
		}
	};

	SelectSlot.prototype.showSlots = function(slots)
	{
		this.$list.empty();
		this.$prev.hide();
		this.$next.hide();

		if (slots) {
			var html = '';

			$.each(slots, function (index, slot) {
				html += '<div'
				+ ' class="request_popup_time' + (slot.active ? ' request_popup_time_maybe' : '') + '"'
				+ ' data-time="' + slot.start_time + '"'
				+ ' data-slot-id="' + slot.id + '">'
				+ slot.start_time
				+ '</div>';
			});

			this.$list.html(html);
		}

		var $current = $('.request_popup_time[data-time="' + this.value + '"]', this.$list);

		if ($current.length) {
			this.setActiveSlot($current);
		} else {
			// this.$input.val('');
			// this.$input.trigger('change');

			this.showSlotsPage(this.getSlotsPageNum($('.request_popup_time_maybe:first', this.$list)));
		}

		return this;
	};

	SelectSlot.prototype.getSlotsPageNum = function($slot)
	{
		var count = $('.request_popup_time', this.$list).size();
		var index = $('.request_popup_time', this.$list).index($slot);

		var num = 0;

		if (count > this.countOnPage) {
			var np = this.countOnPage - 2;
			var page = parseInt((index - 1) / np);

			num = page * np + 1;

			if (index + 1 == count && num == index) {
				page--;
				num -= np;
			}

			num = page < 1 ? 0 : num;
		}

		return num;
	};

	SelectSlot.prototype.setActiveSlot = function($slot)
	{
		var num = this.getSlotsPageNum($slot);

		this.showSlotsPage(num);

		$('.request_popup_time_act', this.$list).removeClass('request_popup_time_act');
		if ($slot.hasClass('request_popup_time_maybe')) {
			$slot.addClass('request_popup_time_act');
		}
	};

	SelectSlot.prototype.showSlotsPage = function(num)
	{
		var count = $('.request_popup_time', this.$list).size();

		var lastNum = num + this.countOnPage - (num > 0 ? 2 : 1);

		if (count > this.countOnPage) {
			$('.request_popup_time', this.$list).hide();
			$('.request_popup_time' + ':lt(' + lastNum + ')' + (num > 0 ? ':gt(' + (num - 1) + ')' : ''), this.$list).show();

			(num > 0) ? this.$prev.show() : this.$prev.hide();
			(lastNum < count) ? this.$next.show() : this.$next.hide();

			var prevNum = num - this.countOnPage + 2;

			this.$prev.data('slotNum', prevNum > 1 ? prevNum : 0);
			this.$next.data('slotNum', lastNum);
		} else {
			this.$prev.hide();
			this.$next.hide();
		}
	};

	SelectSlot.prototype.selectVal = function($element)
	{
		$('.request_popup_time_act', this.$list).removeClass('request_popup_time_act');
		$element.addClass('request_popup_time_act');

		this.slotId = $element.data('slot-id');
		this.$input.val($element.data('time'));
		this.$input.trigger('change');

		return this;
	};

	SelectSlot.prototype.initEvents = function()
	{
		var select = this;

		$('.request_popup_time_next,.request_popup_time_prev', this.$div).click(function () {
			select.showSlotsPage($(this).data('slotNum'));
		});

		this.$div.on('click', '.request_popup_time_maybe:not(.request_popup_time_act)', function () {
			select.selectVal($(this));
		});

		this.$input.change(function() {
			if (select.value != this.value) {
				select.value = this.value;
				select.$input.trigger('changeTime');
			}
		});

		return this;
	};


	var Request = function(el, options) {
		this.$div = $(el);
		this.form = $('form', el)[0];
		this.expanded = false;
		this.gaEvent = null;
		this.step = 0;
		this.phoneCache = null;

		this.urls = {
			slots: '/schedule/slots',
			validate: '/request/validate',
			save: '/request/save',
			saveEmail: '/client/saveEmail'
		};

		this.selectDate = new SelectDate($('.request_popup_select_date', this.$div), this.form['work_date']);

		this.selectTime = new SelectSlot($('.request_popup_time_wrap', this.$div), this.form['work_time']);

		this.selectTime.clinicId = this.form['clinic'].value;
		this.selectTime.doctorId = this.form['doctor'].value;
		this.selectTime.workDate = this.form['work_date'].value;
		this.selectTime.url = this.urls.slots;

		this.initEvents();
		this.selectDateTime();
	};

	Request.prototype.changeStep = function(step)
	{
		var current = $('.request_popup_step[data-' + (typeof(step) === 'number' ? 'id' : 'step-name') + '="' + step + '"]', this.$div);
		var stepName = current.data('step-name');
		var needShow = current.data('show-step');
		if (needShow != undefined && needShow == 0) {
			step = current.data('id');
			step++;
			this.changeStep(step);
			return;
		}

		if (stepName) {
			$('.request_popup_step', this.$div).hide();
			this.step = parseInt(current.data('id'), 10);
			this['step' + stepName]();
			current.show();
		}
	};

	Request.prototype.save = function()
	{
		var request = this;

		var valid = this.validateField('requestName');
		valid = this.validateField('requestPhone') && valid;

		if (valid) {
			$.ajax({
				url: this.urls.save,
				type: 'POST',
				data: $(this.form).serialize(),
				success: function (response) {
					if (response.success) {
						request.form['req_id'].value = response.req_id;
						request.form['client_id'].value = response.cl_id;
						if (request.gaEvent && DD.ga.eventsList[request.gaEvent]) {
							DD.ga.eventsList[request.gaEvent]();
						}
						request.changeStep('Email');
					} else {
						if (response.errors) {
							if (response.errors.date_admission) {
								request.changeStep('DateAndTime');
							} else {
								request.saveError();
							}
						} else {
							request.saveError();
						}
					}
				},
				error: function() {
					request.saveError();
				}
			});
		} else {
			this.saveError();
		}
	};

	Request.prototype.validateField = function(name)
	{
		var $input = $(this.form[name]);

		if (name === 'requestPhone') {
			var length = $input.val().replace(/[\D]/g, '').length;
			if (length != 10 && length != 11) {
				$input.addClass('error');
				$(".request-phone-error").css("display", "block");
				return false;
			}
		} else if ($.trim($input.val()).length < 1) {
			$input.addClass('error');
			return false;
		}

		$("div.error").css("display", "none");
		$input.removeClass('error');
		return true;
	};

	Request.prototype.saveError = function()
	{
		$('#validation-code-input', this.$div).addClass('error');
	};

	Request.prototype.selectDateTime = function() {
		var date = this.selectDate.value;
		var time = this.selectTime.value;

		this.form['date_admission'].value = date ? date + (time ? ' ' + time + ':00' : '') : '';

		$('.request_popup_date_text', this.$div).text(this.selectDate.$current.html());
		$('.request_popup_time_text', this.$div).text(time);

		$('.request_popup_datetime', this.$div)[date && time ? 'show' : 'hide']();

		return this;
	};

	Request.prototype.initEvents = function()
	{
		var request = this;

		this.selectDate.on('changeDate', function() {
			request.selectTime.workDate = request.selectDate.value;
			request.selectTime.value = null;
			request.selectTime.loadSlots();

			request.selectDateTime();
		});

		this.selectTime.on('changeTime', function() {
			request.selectDateTime();
			request.form['slotId'].value = request.selectTime.slotId;

			if (request.selectTime.value) {
				request.changeStep(request.step + 1);
			}
		});

		$('.step-next', this.$div).click(function() {
			request.changeStep(request.step + 1);
		});

		$('.step-prev', this.$div).click(function() {
			request.changeStep(request.step - 1);
		});

		$('.request_popup_save', this.$div).click(function() {
			request.save();
		});

		$('.request_popup_howwork_btn', this.$div).click(function(){
			$('.request_popup_howwork_wrap', request.$div).toggle();
			request.expanded = !request.expanded;
			window.parent.postMessage(request.expanded ? 'expand' : 'collapse', '*');
		});

		$('.request-popup-close').on("click", function() {
			if (request.step == 3) {
				request.close();
			} else {
				$(this).parent().find(".request_popup_close_wrap").css("display", "block");
				$('.request_popup_howwork_wrap').css("display", "none");
			}
		});

		$('.request_popup_close_no').on("click", function() {
			$(this).closest(".request_popup_close_wrap").css("display", "none");
		});

		$('.request_popup_close_yes').on("click", function() {
			request.close();
		});


		$('.request_popup_email_send', this.$div).click(function() {
			if ($.trim($('[name=client_email]', request.$div).val()).length > 1) {
				$.ajax({
					url: request.urls.saveEmail,
					type: 'POST',
					data: {
						clientId: $('[name=client_id]', request.$div).val(),
						clientEmail: $('[name=client_email]', request.$div).val()
					},
					success: function (resp) {
						request.close();
					}
				});
			}
		});
	};

	Request.prototype.close = function()
	{
		window.parent.postMessage('close', '*');
		$(document).trigger("closePopup");
	}

	Request.prototype.stepButtons = function(prev, next)
	{
		$('.step-prev', this.$div)[prev ? 'show' : 'hide']();
		$('.step-next', this.$div)[next ? 'show' : 'hide']();
		$('.step-save', this.$div)[prev ? 'show' : 'hide']();
	};


	Request.prototype.stepDiagnostic = function()
	{
		this.stepButtons(false, true);
	};

	Request.prototype.stepDateAndTime = function()
	{
		this.stepButtons(false, true);

		if (!this.selectTime.isLoaded) {
			this.selectTime.loadSlots();
		}
	};

	Request.prototype.stepContactInfo = function()
	{
		$.mask.definitions['~'] = "[+-]";
		$(".js-mask-phone-request").mask("+7 ?(999) 999-99-99");
		this.stepButtons(true, false);
	};

	Request.prototype.stepEmail = function()
	{
		this.stepButtons(false, false);

		$('.request_popup_client_val', this.$div).text($(this.form['requestName']).val());
		$('.request_popup_phone_val', this.$div).text($(this.form['requestPhone']).val());
	};

	Request.prototype.setGaEvents = function(srcElement)
	{
		if (srcElement == undefined) {
			return;
		}

		var gaEvent = $(srcElement).data("stat");
		if (gaEvent != undefined && DD.ga.eventsList[gaEvent]) {
			DD.ga.eventsList[gaEvent]();
		}

		this.gaEvent = gaEvent == undefined ? null : gaEvent+"Request";
	};

	$('.requestForm').each(function(index, element) {
		var request = new Request(element);
		request.setGaEvents(srcElement);
		var step = $(element).data('step');
		if (step) {
			request.changeStep(parseInt(step, 10));

			if (step == 1) {
				$('.step-prev', request.$div).hide();
			}
		}
	});
}
