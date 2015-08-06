/* js-request-popup */
$(document).ready(function () {
	if ($(".diagn-online-record").length > 0) {
		var countOnPage = 20;

		var requestStepCurrent = 0; // переменная внутреннего шага
		var requestStepPublic = 1; // номер шага для публички, может не совпадать с системным шагом
		var phoneCache = null;
		var selectDate = null;
		var selectTime = null;

		var requestForm = {
			form: null,
			eventsAttached: false,
			clinic: null,
			diagnosticsId: null,
			contactPhone: null,
			expanded: false,
			data: {},
			defaultDiagnosticId: $('#diagnosticsId', this.form).val(),
			diagnosticList: {},
			text: [
				{
					headLeft: 'Вы записываетесь:',
					headStep: 'Выбор диагностики'
				},
				{
					headLeft: 'Вы записываетесь:',
					headStep: 'Выбор дня и времени посещения клиники'
				},
				{
					headLeft: 'Вы записываетесь:',
					headStep: 'Оставьте контактные данные'
				},
				{
					headLeft: 'Вы записались:',
					headStep: 'Оставьте контактные данные'
				}
			],
			setTime: function () {
				selectTime = $('.request_popup_time_act', this.form).data('time');

				$('.request_popup_time_text', this.form).text(selectTime ? selectTime : '');
				$('.request_popup_date_hidden', this.form).val(selectDate ? selectDate + (selectTime ? ' ' + selectTime + ':00' : '') : '');
			},
			setDateText: function (val) {
				selectTime = null;
				this.form.find('.request_popup_datetime').hide();
				if (!val) {
					val = $('.request_popup_select_date .b-select_current', this.form).text();
				}
				$('.request_popup_select_date .b-select_list__act', this.form).removeClass("b-select_list__act");
				$('.request_popup_date_text', this.form).text(val);
				selectDate = $('.request_popup_date_hidden', this.form).val();
				$('.request_popup_select_date .b-select_list__item[data-date="' + selectDate + '"]', this.form).addClass("b-select_list__act");
				this.loadSlots();
			},
			changeStep: function (step, stepPublic) {
				requestStepCurrent = step;
				requestStepPublic = stepPublic;

				this.form.find('.request_popup_step').hide();
				this.form.find('.request_popup_step[data-id=' + (step + 1) + ']').show();
				this.form.find('.js-request-popup-step-text').text(requestForm.text[step].headStep);
				this.form.find('.js-request-popup-head-left').text(requestForm.text[step].headLeft);
				this.form.find('.js-request-popup-step-num').text(stepPublic);

				switch (step) {
					case 0:
						this.form.find('.js-request-popup-step-next').show();
						this.sendEvent("chooseDiagnostic");
						break;
					case 1:
						this.form.find('.js-request-popup-step-next').show();
						this.sendEvent("slots");
						break;
					case 2:
						this.form.find('.js-request-popup-step-next').hide();
						$('.request_popup_head_mayhidden').show();
						this.sendEvent("form");
						break;
					case 3:
						$('.request_popup_client_val').text($('#client-name').val());
						this.sendEvent("emailForm");
						break;
				}

				switch (stepPublic) {
					case 1:
						this.form.find('.js-request-popup-step-prev').hide();
						break;
					case 4:
						this.form.find('.js-request-popup-step-prev').hide();
						break;
					default:
						this.form.find('.js-request-popup-step-prev').show();
						break;
				}
			},
			closeStart: function()
			{
				if (requestStepPublic == 4) {
					requestForm.close();
				} else {
					$('.request_popup_close_wrap').show();
					this.sendEvent("closeStart");
				}
			},
			close: function() {
				window.parent.postMessage('close', '*');
				$('body').removeClass('noscroll').scrollTop($('body').data('scroll-position'));

				$('.popup_bg, .popup').fadeOut(200);
				$('.request_popup_close_wrap', this.form).hide();

				this.form.find('.request_popup_datetime, .request_popup_howwork_wrap').hide();
				this.sendEvent("close");
			},
			loadDiagnostics: function () {

				this.diagnosticList = {};
				requestForm.selectDiagnostic(0);

				$.ajax({
					type: "GET",
					url: '/diagnostics/listForClinic/',
					data: {
						clinicId: this.clinic.id,
						specialities: this.specialities
					},
					success: function (response) {
						requestForm.diagnosticList = response.diagnostics;

						$('#request-diagnostic-type .b-select_list', this.form)
							.html(requestForm.buildDiagnosticsListHtml(response.parentIds, 'js-diagselect'));

						var d = requestForm.diagnosticList[requestForm.defaultDiagnosticId];
						if (!d && response.parentIds.length === 1) {
							d = requestForm.diagnosticList[response.parentIds[0]];
						}
						if (d && d.parent_id) {
							requestForm.selectDiagnostic(d.parent_id);
						}
						requestForm.selectDiagnostic(d ? d.id : 0);
					}
				});
			},
			selectDiagnostic: function (diagnosticId) {
				var d = this.diagnosticList[diagnosticId];
				var parent = d && d.parent_id ? this.diagnosticList[d.parent_id] : null;

				$('#diagnosticsId', this.form).val(d ? d.id : 0);

				if (d) {
					var parentName = parent ? (parent.reduction_name ? parent.reduction_name : parent.name) + ' ' : '';
					var price = d.special_price ? d.special_price : d.price;
					var priceOnline = d.price_for_online;

					$('.request_popup_clinic_spec', this.form).html(parentName + (d.reduction_name ? d.reduction_name : d.name));

					if (this.clinic.discountOnline > 0 && priceOnline > 0) {
						priceText = d.price ? '<span>Цена &ndash; <strike>' + price + 'р.</strike></span>' : '';
						specPriceText = 'Спец. цена &ndash; <b>' + priceOnline + 'р.</b>';
					} else {
						priceText = price ? 'Цена &ndash; ' + price + 'р.' : '';
						specPriceText = '';
					}
					$('.request_popup_clinic_price', this.form).html(priceText);
					$('.request_popup_clinic_spec_price', this.form)
						.html(specPriceText)
						.attr('title', 'Дополнительная скидка ' + d.discount + '% при онлайн записи на эту услугу!');
					$('.request_popup_specprice', this.form).show();
				} else {
					$('.request_popup_specprice', this.form).hide();
				}

				if (d && parent) {
					$('#request-diagnostic-subtype .b-select_current', this.form).html(d.name);
				} else {
					var html = '';

					$('#request-diagnostic-type .b-select_current', this.form).html(d ? d.name : 'выберите из списка');

					if (d && d.childIds && d.childIds.length > 0) {
						$('#request-diagnostic-subtype .b-select_current', this.form).html('выберите из списка');
						$('.diagnostic_subtype', this.form).show();

						html = requestForm.buildDiagnosticsListHtml(d.childIds, 'js-subdiagselect');
					} else {
						$('.diagnostic_subtype', this.form).hide();
					}

					$('#request-diagnostic-subtype .b-select_list', this.form).html(html);
				}
			},
			buildDiagnosticsListHtml: function (ids, divClass) {
				var html = '';
				if (this.diagnosticList && ids) {
					$.each(ids, function (index, id) {
						var d = requestForm.diagnosticList[id];
						html += '<div class="' + divClass + ' b-select_list__item" data-spec-id="' + d.id + '">' + d.name + '</div>';
					});
				}
				return html;
			},
			loadSlots: function () {
				var workDate = $('.request_popup_date_hidden', this.form)[0].value;
				var data = this.data;

				if (workDate && (data.workDate != workDate || data.clinicId != this.clinic.id)) {
					data.workDate = workDate;
					data.clinicId = this.clinic.id;

					$('.request_popup_time_list', this.form).empty();
					$('.request_popup_time_prev,.request_popup_time_next', this.form).hide();

					$.ajax({
						type: "POST",
						url: '/schedule/slots/',
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
							$('.request_popup_time_list', this.form).html(html);

							var $current = $('.request_popup_time[data-time="' + selectTime + '"]', this.form);

							if ($current.length) {
								requestForm.setActiveSlot($current);
							} else {
								requestForm.showSlotsPage(requestForm.getSlotsPageNum($('.request_popup_time_maybe:first', this.form)));
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
			validatePhoneActive: function(isError) {
				var $ico = $('.request_popup_phone_valid_ico', this.form);
				if (isError) {
					$ico.addClass('request_popup_phone_valid_ico-error').show();
				} else {
					$ico.removeClass('request_popup_phone_valid_ico-error').hide();
				}
				$('.request_popup_phone_send', this.form).removeClass('ui-btn_grey').addClass('ui-btn_green');
			},
			validatePhoneComplete: function() {
				$('.request_popup_phone_valid_ico', this.form).removeClass('request_popup_phone_valid_ico-error').show();
				$('.request_popup_phone_send', this.form).addClass('ui-btn_grey').removeClass('ui-btn_green');
			},
			attachEvents: function() {

				if (this.eventsAttached) {
					return;
				}

				this.eventsAttached = true;

				$('.js-request-popup-step-next', this.form).click(function () {
					requestForm.changeStep(requestStepCurrent + 1, requestStepPublic + 1);
				});

				$('.js-request-popup-step-prev', this.form).click(function () {
					requestForm.changeStep(requestStepCurrent - 1, requestStepPublic - 1);
				});

				$('.request_popup_howwork_btn', this.form).click(function () {
					$('.request_popup_howwork_wrap', this.form).toggle();
					this.expanded = !this.expanded;
					window.parent.postMessage(this.expanded ? 'expand' : 'collapse', '*');
				});

				$('.request_popup_close_no', this.form).click(function () {
					$('.request_popup_close_wrap', this.form).hide();
					requestForm.sendEvent("closeReturn");
				});

				$('.request_popup_close_yes', this.form).click(function () {
					requestForm.close();
				});

				this.form.on('click', '.js-diagselect,.js-subdiagselect', function () {
					requestForm.selectDiagnostic($(this).data('spec-id'));
				});

				this.form.on('click', '.request_popup_time', function () {
					requestForm.form.find('.request_popup_datetime').show();
					if (!$(this).hasClass('request_popup_time_maybe') || $(this).hasClass('request_popup_time_act')) return;

					$('.request_popup_time_act', this.form).removeClass('request_popup_time_act');
					$(this).addClass('request_popup_time_act');
					requestForm.setTime();
					requestForm.changeStep(requestStepCurrent + 1, requestStepPublic + 1);
				});

				$('.request_popup_time_next,.request_popup_time_prev', this.form).click(function () {
					requestForm.showSlotsPage($(this).data('slotNum'));
				});

				$('.request_popup_date_hidden', this.form).change(function () {
					requestForm.setDateText();
				});

				$('.request_popup_select_date .b-select_list__item', this.form).click(function () {
					var $self = $(this);
					$('.request_popup_date_hidden', this.form).val($self.data('date'));
					requestForm.setDateText($self.text());
				});

				$('#client-phone', this.form).keyup(function () {
					var phone = $("#client-phone").val().replace(/[\D]/g, '');
					if (phoneCache !== phone) {
						$("#div-validation-code").hide();
						requestForm.validatePhoneActive(false);
						phoneCache = null;
					}
				});

				$('#client-phone, #validation-code-input, #clientEmail', this.form).keyup(function (e) {
					if (e.keyCode == 13) {
						$($(this).data('send-button'), this.form).trigger('click');
					}
				});

				$('.request_popup_phone_send', this.form).click(function () {
					if ($(this).hasClass('ui-btn_grey')) return;

					var $client_phone = $("#client-phone");
					var $client_name = $("#client-name");

					var phone = $client_phone.val().replace(/[\D]/g, '');

					if (phone.length == 10 && $client_name.val() && phoneCache !== phone) {
						phoneCache = phone;

						$("#div-validation-code").show();

						$.ajax({
							url: '/request/validate/',
							type: 'POST',
							data: $(".req_form.req_form_clinic", this.form).serialize(),
							success: function (resp) {
								if (resp.success) {
									$("#reqId").val(resp.req_id);
									$("#clientId").val(resp.cl_id);
									phoneCache = phone;
								} else {
									requestForm.validatePhoneActive(true);
									phoneCache = null;
								}
							},
							error: function (jqXHR, status, error) {
								requestForm.validatePhoneActive(true);
								phoneCache = null;
							}
						});
						requestForm.validatePhoneComplete();
					} else {
						if (phone.length < 10) {
							$client_phone.addClass('error');
							requestForm.validatePhoneActive(true);
						} else {
							requestForm.validatePhoneActive(false);
						}
						if (!$client_name.val()) {
							$client_name.addClass('error');
						}
					}
				});

				$('.request_popup_validate_send', this.form).click(function () {
					var $input = $('#validation-code-input', this.form);

					if ($.trim($input.val()).length > 1) {
						$input.removeClass('error');

						$.ajax({
							url: '/request/save/',
							type: 'POST',
							data: $(".req_form.req_form_clinic", this.form).serialize(),
							success: function (resp) {
								if (resp.success) {
									requestForm.changeStep(requestStepCurrent + 1, requestStepPublic + 1);
									$("#div-validation-code").hide();
									$('.request_popup_head_mayhidden').text("Заявка № " + resp.req_id);
									$('.request-success-text').html(resp.successText);
									$input.val('');
									phoneCache = null;
									window.parent.postMessage('saveSuccess', '*');
								} else {
									if (resp.errors) {
										if (resp.errors.date_admission) {
											requestForm.changeStep(1, 2);
										} else {
											$input.addClass('error');
										}
									} else {
										$input.addClass('error');
									}
								}
							},
							error: function() {
								$input.addClass('error');
							}
						});
					} else {
						$input.addClass('error');
					}
				});

				$('.request_popup_email_send', this.form).click(function() {
					if ($.trim($('#clientEmail').val()).length > 1) {
						$.ajax({
							url: '/request/saveEmail/',
							type: 'POST',
							data: {
								client_id: $('#clientId').val(),
								client_email: $('#clientEmail').val()
							},
							success: function (resp) {
								requestForm.sendEvent("emailSended");
								requestForm.close();
							}
						});
					}
				});
			},
			showClinicInfo: function() {
				var metroText = "";
				if (this.clinic.metro.length > 0) {
					for (var i = 0; this.clinic.metro.length > i; i++) {
						var m = this.clinic.metro[i];
						metroText =
							'<div>' + m.title + ' <span>' + (m.dist ? '(' + m.dist + ')' : '') + '</span></div>';
					}
				}

				$("#clinic", this.form).val(this.clinic.id);
				$(".request_popup_clinic_name", this.form).text(this.clinic.name);
				$(".request_popup_clinic_address", this.form).text(this.clinic.address);
				$(".request_popup_clinic_metro", this.form).html(metroText);
			},
			init: function(params) {
				this.form = $(".diagn-online-record");
				for (var each in params) {
					this[each] = params[each];
				}
				this.attachEvents();

				this.showClinicInfo();

				$("#div-validation-code").hide();
				$("#reqId").val('');
				$("#validation-code-input").val('');
				$("#client-phone").data('validate-phone', 1);

				if ($(".req_form").hasClass("s-closed")) {
					$(".req_form").removeClass("s-closed").show();
					$(".js-request-success").hide();
				}

				this.validatePhoneActive();
				this.loadDiagnostics();

				phoneCache = null;

				if (!selectDate) {
					var $date = $('.request_popup_select_date .b-select_list__item:first', this.form);
					$('.request_popup_date_hidden', this.form).val($date.data('date'));
					$('.request_popup_select_date .b-select_current').text($date.text());
					this.setDateText($date.text());
				}

				if (params.skipStepDiagnostic) {
					this.changeStep(1, 2);
				} else {
					this.changeStep(0, 1);
				}

				$('.request_popup_select_date_ico').click(function() {
					$('.request_popup_date_hidden').focus().blur();
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

							dayOfWeek: [
								'Вс','Пн','Вт','Ср','Чт','Пт','Сб'
							]
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

				var calendarMonthRu = [
						'января','февраля','марта','апреля','мая','июня',
						'июля','августа','сентября','октября','ноября','декабря'
					],
					calendarDayRu = [
						'Воскресенье','Понедельник','Вторник','Среда','Четверг','Пятница','Суббота'
					];

			},
			sendEvent: function(action) {
				$(document).trigger("DiagnosticOnlineForm", {action: action, type: requestType})
			}
		};

	}

	$(document).on("requestPopupOpen", function(e, data) {
		requestForm.init(data);
	});

	$(document).on("requestPopupCloseStart", function(e, data){
		requestForm.closeStart();
	});
});
