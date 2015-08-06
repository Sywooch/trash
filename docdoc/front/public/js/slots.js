
$(document).ready(function () {
	var calendarMonthRu = ['января','февраля','марта','апреля','мая','июня', 'июля','августа','сентября','октября','ноября','декабря'];
	var calendarDayRu = ['Воскресенье','Понедельник','Вторник','Среда','Четверг','Пятница','Суббота'];

	if ($('.select_slots').length > 0) {
		var countOnPage = 20;
		var selectDate = null;
		var selectTime = null;

		var requestForm = {
			form: null,
			eventsAttached: false,
			clinic: null,
			diagnosticsId: null,
			expanded: false,
			data: {},
			setTime: function () {
				selectTime = $('.request_popup_time_act', this.form).data('time');

				$('.request_popup_time_text', this.form).text(selectTime ? selectTime : '');
				$('.request_popup_date_hidden', this.form).val(selectDate ? selectDate + (selectTime ? ' ' + selectTime + ':00' : '') : '');
			},
			setDateText: function (val) {
				if (!val) {
					val = $('.request_popup_select_date .b-select_current', this.form).text();
				}
				$('.request_popup_select_date .b-select_list__act', this.form).removeClass("b-select_list__act");
				$('.request_popup_date_text', this.form).text(val);
				selectDate = $('.request_popup_date_hidden', this.form).val();
				$('.request_popup_select_date .b-select_list__item[data-date="' + selectDate + '"]', this.form).addClass("b-select_list__act");
				this.loadSlots();
			},
			loadSlots: function () {
				var main = this;
				var workDate = $('.request_popup_date_hidden', this.form)[0].value;
				var data = this.data;

				if (workDate && (data.workDate != workDate)) {
					data.workDate = workDate;

					$('.request_popup_time_list', this.form).empty();
					$('.request_popup_time_prev,.request_popup_time_next', this.form).hide();

					$.ajax({
						type: "POST",
						url: '/schedule/diagnosticSlots',
						data: data,
						success: function (response) {
							var html = '';
							var slots = [];

							if (response.slots && response.slots[workDate]) {
								slots = response.slots[workDate];

								$.each(slots, function (index, slot) {
									html += '<div class="request_popup_time' + (slot.active ? ' request_popup_time_maybe' : '') +
									'" data-time="' + slot.start_time + '">' +
									slot.start_time +
									'</div>';
								});
							}
							$('.request_popup_time_list', main.form).html(html);

							var $current = $('.request_popup_time[data-time="' + selectTime + '"]', main.form);

							if ($current.length) {
								requestForm.setActiveSlot($current);
							} else {
								requestForm.showSlotsPage(requestForm.getSlotsPageNum($('.request_popup_time_maybe:first', main.form)));
								requestForm.setTime();
							}
						}
					});
				}
			},
			getSlotsPageNum: function ($slot) {
				var count = $('.request_popup_time', this.form).size();
				var index = $('.request_popup_time', this.form).index($slot);

				var num = 0;

				if (count > countOnPage) {
					var np = countOnPage - 2;
					var page = parseInt((index - 1) / np, 10);

					num = page * np + 1;

					if (index + 1 == count && num == index) {
						page--;
						num -= np;
					}

					num = page < 1 ? 0 : num;
				}

				return num;
			},
			setActiveSlot: function ($slot) {
				var num = this.getSlotsPageNum($slot);

				this.showSlotsPage(num);

				$('.request_popup_time_act', this.form).removeClass('request_popup_time_act');
				if ($slot.hasClass('request_popup_time_maybe')) {
					$slot.addClass('request_popup_time_act');
				}

				this.setTime();
			},
			showSlotsPage: function (num) {
				var $wrap = $('.request_popup_time_wrap', this.form);
				var $prev = $('.request_popup_time_prev', $wrap);
				var $next = $('.request_popup_time_next', $wrap);
				var count = $('.request_popup_time', $wrap).size();

				var lastNum = num + countOnPage - (num > 0 ? 2 : 1);

				if (count > countOnPage) {
					$('.request_popup_time', $wrap).hide();
					$('.request_popup_time' + ':lt(' + lastNum + ')' + (num > 0 ? ':gt(' + (num - 1) + ')' : ''), $wrap).show();

					(num > 0) ? $prev.show() : $prev.hide();
					(lastNum < count) ? $next.show() : $next.hide();

					var prevNum = num - countOnPage + 2;

					$prev.data('slotNum', prevNum > 1 ? prevNum : 0);
					$next.data('slotNum', lastNum);
				} else {
					$prev.hide();
					$next.hide();
				}
			},
			attachEvents: function() {
				var main = this;

				this.form.on('click', '.request_popup_time', function () {
					if (!$(this).hasClass('request_popup_time_maybe') || $(this).hasClass('request_popup_time_act')) return;

					$('.request_popup_time_act', main.form).removeClass('request_popup_time_act');
					$(this).addClass('request_popup_time_act');
					requestForm.setTime();

					$('.request_date_admission', main.form).val(selectDate ? selectDate + (selectTime ? ' ' + selectTime + ':00' : '') : '');
				});

				$('.request_popup_time_next,.request_popup_time_prev', this.form).click(function () {
					requestForm.showSlotsPage($(this).data('slotNum'));
				});

				$('.request_popup_date_hidden', this.form).change(function () {
					requestForm.setDateText();
				});

				$('.request_popup_select_date .b-select_list__item', this.form).click(function () {
					var $self = $(this);
					$('.request_popup_date_hidden', main.form).val($self.data('date'));
					requestForm.setDateText($self.text());
				});
			},
			init: function() {
				var main = this;

				this.form = $('.select_slots');

				this.data.doctorId = this.form.data('doctorId');
				this.data.clinicId = this.form.data('clinicId');

				this.attachEvents();

				var datetime = $('input.request_date_admission', this.form).val();

				if (datetime) {
					var dt = datetime.split(' ');
					selectDate = dt[0];
					selectTime = dt[1];
					this.loadSlots();
				} else {
					var $date = $('.request_popup_select_date .b-select_list__item:first', this.form);
					$('.request_popup_date_hidden', this.form).val($date.data('date'));
					$('.request_popup_select_date .b-select_current').text($date.text());
					this.setDateText($date.text());
				}

				$('.request_popup_select_date_ico', this.form).click(function() {
					$('.request_popup_date_hidden').focus().blur();
				});

				$('.b-select_current,.b-select_arr', this.form).click(function() {
					var parent = $(this).parents('.b-select_wrap');

					parent.find('.b-select_list').slideDown(1, function() {
						parent.addClass('b-select_list__open');
					});
				});

				$('.b-select_list__item', this.form).click(function() {
					var parent = $(this).parents('.b-select_wrap');
					var el = $(this);

					$('.b-select_list__act', parent).removeClass('b-select_list__act');
					el.addClass('b-select_list__act');
					$('.b-select_current', parent).text(el.text());
					$('.b-select_list', parent).hide();
					$('.b-select_list__open', parent).removeClass('b-select_list__open');
				});

				$('body').click(function() {
					$('.b-select_list__open .b-select_list', main.div).hide();
					$('.b-select_list__open', main.div).removeClass('b-select_list__open');
				});

				$('.request_popup_date_hidden', this.form).datetimepicker({
					format: 'd-m-Y',
					timepicker:false,
					minDate: '0',
					lang: 'ru',
					closeOnDateSelect:true,
					todayButton: false,
					dayOfWeekStart: 1,
					className: 'request_popup_datepicker',
					fixed: true,
					i18n: {
						ru: {
							months: [
								'Январь','Февраль','Март','Апрель','Май','Июнь',
								'Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'
							],

							dayOfWeek: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб']
						}
					},
					onSelectDate:function(ct, $input) {
						$('.request_popup_select_date .b-select_current').text(
							calendarDayRu[ct.dateFormat('w')] + ', ' +
							ct.dateFormat('j') + ' ' +
							calendarMonthRu[ct.dateFormat('n')-1] + ' ' +
							ct.dateFormat('Y')
						);
					}
				});
			}
		};

		requestForm.init();
	}
});
