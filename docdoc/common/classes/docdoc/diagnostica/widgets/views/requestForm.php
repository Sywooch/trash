<?php
/**
 * @var int $selectedDiagnosticId
 * @var dfs\docdoc\objects\Phone $phone
 * @var dfs\docdoc\models\PartnerModel $partner
 * @var bool $hasDiagnosticParent
 */
?>
<div class="diagn-online-record">
	<form class="req_form req_form_clinic" method="post" action="/request/save/" novalidate="novalidate">
		<div class="request_popup_wrap clearfix">

			<div class="request_popup_left_wrap">
				<div class="request_popup_left">
					<div class="request_popup_head js-request-popup-head-left"></div>
					<div class="request_popup_clinic_info request_popup_dotted">
						<div class="request_popup_clinic_name"></div>
						<div class="request_popup_clinic_address"></div>
						<div class="request_popup_clinic_metro"></div>
					</div>
					<div class="request_popup_specprice request_popup_dotted" style="display: none;">
						<div class="request_popup_clinic_spec"></div>
						<div class="request_popup_clinic_price"></div>
						<div class="request_popup_clinic_spec_price js-tooltip-tr tooltip-popup" title=""></div>
					</div>
					<div class="request_popup_datetime request_popup_dotted">
						<div class="request_popup_date_text"></div>
						Время: <span class="request_popup_time_text"></span>
					</div>
				</div>
				<div class="ui-btn ui-btn_grey hover request_popup_btn_prev js-request-popup-step-prev">Назад</div>
			</div>

			<div class="request_popup_right_wrap">
				<div class="request_popup_right">
					<div class="request_popup_head">
						<div class="request_popup_head_mayhidden">
							Шаг <span class="js-request-popup-step-num"></span> из 3. 
							<span class="request_popup_step_head js-request-popup-step-text"></span>
						</div>
					</div>
					<div class="request_popup_steps_wrap request_popup_dotted">

						<div class="request_popup_step" data-id="1">
							<div class="request_popup_head_big">Вид диагностики</div>
							<div class="b-select_wrap b-select_spec" id="request-diagnostic-type">
								<div class="b-select_arr"></div>
								<div class="b-select_current ui-grad-grey">выберите из списка</div>
								<div class="b-select_list"></div>
							</div>
							<div class="diagnostic_subtype" style="display: none;">
								<div class="request_popup_head_big">Диагностика</div>
								<div class="b-select_wrap b-select_spec__sub" id="request-diagnostic-subtype">
									<div class="b-select_arr"></div>
									<div class="b-select_current ui-grad-grey">выберите из списка</div>
									<div class="b-select_list"></div>
								</div>
							</div>
						</div>

						<div class="request_popup_step" data-id="2">
							<div class="request_popup_head_big">Дата записи</div>
							<div class="request_popup_select_date">
								<div class="b-select_wrap">
									<div class="b-select_arr"></div>
									<div class="b-select_current ui-grad-grey">Выберите дату</div>
									<div class="b-select_list">
										<?php
											$df = Yii::app()->dateFormatter;
											$time = time();

											for ($i = 0; $i < 7; $i++) {
												$format = $i > 1 ? 'cccc' : ($i === 1 ? 'Завтра' : 'Сегодня');

												echo '<div class="b-select_list__item" data-date="', date('d-m-Y', $time) , '">';
												echo $df->format($format . ', d MMMM yyyy', $time);
												echo '</div>';

												$time += 86400;
											}
										?>
									</div>
								</div>
								<input type="text" class="request_popup_date_hidden" value="" name="requestForm[date_admission]">
								<div class="request_popup_select_date_ico"></div>
							</div>
							<div class="request_popup_time_wrap">
								<div class="request_popup_time_prev" style="display:none;">...</div>
								<div class="request_popup_time_list"></div>
								<div class="request_popup_time_next" style="display:none;">...</div>
							</div>
						</div>

						<div class="request_popup_step" data-id="3">
							<div class="request_popup_head_big">Введите ваше имя</div>
							<input class="request_popup_input request_popup_input_client" id="client-name" type="text" autofocus="true" placeholder="Введите имя" name="requestForm[client_name]" style="margin-bottom: 15px;" />
							<div class="request_popup_head_big">Подтвердите телефон</div>
							<div class="request_popup_phone_valid_wrap">
								<div class="request_popup_phone_valid_item">
									<div class="l-ib request_popup_phone_valid_txt">
										+7
									</div><div class="l-ib request_popup_phone_valid_inp">
										<input id="client-phone" class="request_popup_input js-mask-phone-request" type="text" placeholder="" name="requestForm[client_phone]" data-validate-phone="0" data-send-button=".request_popup_phone_send">
									</div><div class="ui-btn ui-btn_green request_popup_phone_send">Получить код</div>
									<div class="request_popup_phone_valid_note">Секретный код придет на ваш телефон в течение 20 секунд</div>
									<div class="request_popup_phone_valid_ico"></div>
								</div>
								<div class="request_popup_phone_valid_item" id="div-validation-code">
									<div class="l-ib request_popup_phone_valid_txt">
										Код
									</div><div class="l-ib request_popup_phone_valid_inp">
										<input id="validation-code-input" type="text" class="request_popup_input" name="requestForm[validation_code]" data-send-button=".request_popup_validate_send" value="">
									</div><div class="ui-btn ui-btn_green request_popup_validate_send">Подтвердить</div>
								</div>
							</div>
						</div>

						<div class="request_popup_step" data-id="4">
							<div class="request_popup_head_big">
								<span class="request_popup_client_val"></span>
							</div>
							<div class="request_popup_text_success">
								<span class="request-success-text">
									Ваша заявка на приём принята!<br>
									В течении 15 минут, с вами свяжется наш оператор. <br>
									Спасибо что воспользовались нашим сервисом!
								</span>
								<br><br>
								Оставьте вашу почту, чтобы получить информацию о записе.
							</div>
							<div class="l-ib request_popup_phone_valid_inp" style="width: 263px; margin-right: 15px;">
								<input id="clientEmail" type="text" class="request_popup_input" name="requestForm[client_email]" data-send-button=".request_popup_email_send" value="">
							</div><div class="ui-btn ui-btn_green request_popup_email_send" style="width: 163px;">Отправить</div>
						</div>

					</div>
				</div>
			</div>

			<div class="request_popup_bottom">
				<div class="request_popup_bottom_padding">
					<div class="request_popup_bottom_cont">
						<div class="ui-btn ui-btn_green request_popup_btn_next js-request-popup-step-next">Продолжить</div>
						<?php
						if ($phone) {
							echo '<div>Тел. для справок: ' . $phone->prettyFormat('+7 ') . '</div>';
						}

						if ($this->partner) {
							echo '<div class="">Сервис предоставлен <a href="http://docdoc.ru/?pid='.$this->partner->id.'" target="_blank">DocDoc.ru</a></a></div>';
						}
						?>
						<div class="request_popup_howwork_btn">Как это работает?</div>
					</div>
				</div>
			</div>

		</div>

		<div class="request_popup_howwork_wrap clearfix">
			<div class="request_popup_howwork_item">
				<div class="request_popup_howwork_padding">
					<div class="request_popup_howwork_num">1</div>
					<div class="request_popup_howwork_text">
						<div class="request_popup_head">Выберите</div>
						<div class="request_popup_howwork_txt">услугу, день и <br>время записи</div>
					</div>
				</div>
			</div>
			<div class="request_popup_howwork_item">
				<div class="request_popup_howwork_padding">
					<div class="request_popup_howwork_num">2</div>
					<div class="request_popup_howwork_text">
						<div class="request_popup_head">Подтвердите</div>
						<div class="request_popup_howwork_txt">свой телефон кодом <br>из SMS</div>
					</div>
				</div>
			</div>
			<div class="request_popup_howwork_item">
				<div class="request_popup_howwork_padding">
					<div class="request_popup_howwork_num">3</div>
					<div class="request_popup_howwork_text">
						<div class="request_popup_head">Получите SMS</div>
						<div class="request_popup_howwork_txt">с подтверждением <br>брони</div>
					</div>
				</div>
			</div>
		</div>
	
		<div class="request_popup_close_wrap">
			<div class="request_popup_close_overlay"></div>
			<div class="request_popup_close_cont">
				<div class="request_popup_close_text">
					<div class="request_popup_head">Вы действительно хотите <br>прервать запись?</div>
					<?php
					if ($phone) {
					?>
					<div class="request_popup_dotted">
						Если у вас возникли вопросы,<br>
						позвоните нам по телефону:
						<div class="request_popup_close_phone"><?php echo $phone->prettyFormat('8 '); ?></div>
					</div>
					<?php
					}
					?>
				</div>
				<div class="ui-btn ui-btn_grey hover request_popup_close_yes">Прервать</div>
				<div class="ui-btn ui-btn_green hover request_popup_close_no">Продолжить</div>
			</div>
		</div>

		<input id="reqId" type="hidden" name="requestForm[req_id]" value="">
		<input id="clientId" type="hidden" name="requestForm[client_id]" value="">

		<input type="hidden" name="requestForm[clinic_id]" value="" id="clinic">
		<?php
		if ($partner) {
			echo CHtml::hiddenField("requestForm[partner_id]", $partner->id);
		}
		?>
		<input
			type="hidden"
			name="requestForm[diagnostics_id]"
			value="<?php echo $selectedDiagnosticId; ?>"
			data-has-parent="<?php echo $hasDiagnosticParent; ?>"
			id="diagnosticsId"
			>
	</form>
</div>
