<?php
/**
 * @var dfs\docdoc\models\ClinicModel $clinic
 * @var dfs\docdoc\models\DoctorModel $doctor
 * @var dfs\docdoc\models\DoctorClinicModel $doctorInClinic
 * @var dfs\docdoc\objects\Phone $phone
 * @var array $closestStations
 * @var array $slotDates
 * @var string $bookDate
 */
?>
<?php if (Yii::app()->session["specialityId"]) { ?>
	<div class="title">Запись на приём в «<?=$clinic->name?>»</div>

	<ul class="steps">
		<li class="js-service-tab">
			<a href="/request/form?clinic=<?=$clinic->id?>">
				<div class="big">1.Услуга</div>
				Выберите, на что вы<br>
				хотели бы записаться
			</a>
		</li>
		<li class="js-doctor-tab">
			<a href="/request/form?clinic=<?= $clinic->id ?>&speciality=<?php echo Yii::app()->session["specialityId"]; ?>">
				<div class="big">2.Специалист</div>
				Выберите врача, к которому<br>
				хотите записаться
			</a>
		</li>
		<li class="js-contacts-form-tab active">
			<div class="big">3.Запись</div>
			запишитесь к врачу<br>
			в удобное для Вас время
		</li>
	</ul>
<?php } ?>

<div class="request requestForm" data-step="1">
	<div class="request-popup-close" title="Закрыть"></div>
	<form method="post" action="/request/save/">
		<div class="request_popup_wrap clearfix">

			<div class="request_popup_left_wrap">
				<div class="request_popup_left">
					<div class="request_popup_head">Вы записываетесь:</div>
					<div class="request_popup_clinic_info request_popup_dotted">
						<div class="request_popup_doctor">
							<a
								href="<?php echo $doctor->getUrl(); ?>"
								target="_parent"
								class="request_popup_doctor_img"
								><img src="<?php echo $doctor->getImg(); ?>" /></a>
							<a
								href="<?php echo $doctor->getUrl(); ?>"
								target="_parent"
								class="request_popup_doctor_name"
								><?php echo $doctor->name; ?></a>
							<div class="request_popup_doctor_clear"></div>
						</div>
						<div class="request_popup_clinic_name request_popup_small">
							клиника "<?php echo $clinic->name; ?>"
						</div>
						<div class="request_popup_clinic_address">
							<?php echo $clinic->getAddress(); ?>
						</div>
						<?php foreach ($closestStations as $st) { ?>
							<div class="closest-station">
								<?php echo $st["model"]->name; ?> <span>(<?php echo $st["distance"]; ?> м)</span>
							</div>
						<?php } ?>
					</div>
					<div class="request_popup_specprice request_popup_dotted">
						<span class="request_popup_clinic_price">Прием — <?php echo $doctor->special_price ?: $doctor->price; ?> руб.</span>
					</div>
					<div class="request_popup_datetime request_popup_dotted">
						<div class="request_popup_date_text"></div>
						Время: <span class="request_popup_time_text"></span>
					</div>
				</div>
				<div class=" ui-btn ui-btn_grey request_popup_btn request_popup_btn_prev hover step-prev">Назад</div>
			</div>

			<div class="request_popup_right_wrap">
				<div class="request_popup_right">

					<div class="request_popup_step" data-id="1" data-step-name="DateAndTime" data-show-step="<?php echo (int)$doctorInClinic->has_slots; ?>">
						<div class="request_popup_head">
							<?php if (!Yii::app()->session["specialityId"]) { ?>Шаг 1. <?php } ?>
							Выбор дня и времени посещения врача
						</div>
						<div class="request_popup_steps_wrap request_popup_dotted">
							<div class="request_popup_head_big">Дата записи</div>
							<div class="request_popup_select_date">
								<div class="b-select_wrap">
									<div class="b-select_arr"></div>
									<div class="b-select_current ui-grad-grey">Выберите дату</div>
									<div class="b-select_list">
										<?php
											$df = Yii::app()->dateFormatter;
											$time = time();
											$today = date('d-m-Y');
											$tomorrow = date('d-m-Y', strtotime('tomorrow') );
											$dates = [];

											$selectActive = false;
											foreach ($slotDates as $slot) {

												$time = strtotime($slot->start_time);
												$date =  date('d-m-Y', $time);
												(!$selectActive && $date === $this->bookDate) && $selectActive = $date;

												$format = $date === $today ? 'Сегодня' : ($date === $tomorrow ? 'Завтра' : 'cccc');
												$dates[$date] = ['label' => $df->format($format . ', d MMMM', $time)];
											}

											foreach($dates as $date => $d) {
												$class = "";
												if (!$selectActive || $selectActive === $date) {
													$class = "active-date";
													$selectActive = $date;
												}

												echo '<div class="b-select_list__item ' . $class . ' " data-date="' . $date . '">';
												echo $d['label'];
												echo '</div>';
											}
										?>
									</div>
								</div>

								<input type="text" class="request_popup_date_hidden" name="work_date" value="<?php echo date('d-m-Y'); ?>" data-dates='<?=json_encode($dates);?>'>
								<div class="request_popup_select_date_ico"></div>
							</div>
							<div class="request_popup_time_wrap">
								<div class="request_popup_time_prev" style="display:none;">...</div>
								<div class="request_popup_time_list"></div>
								<div class="request_popup_time_next" style="display:none;">...</div>
								<input type="hidden" name="work_time" value="">
							</div>
						</div>
					</div>

					<div class="request_popup_step" data-id="2" data-step-name="ContactInfo">
						<div class="request_popup_head">
							<?php if (!Yii::app()->session["specialityId"]) { ?>Шаг 2. <?php } ?>
							Оставьте контактные данные
						</div>
						<div class="request_popup_steps_wrap request_popup_dotted">
							<div class="request_popup_head_big">Введите ваше имя</div>
							<input type="text"
								   autofocus="true"
								   placeholder="Введите имя"
								   name="requestName"
								   class="request_popup_input request_popup_input_client"
								   style="margin-bottom: 15px;"
								/>
							<div class="request_popup_head_big">Телефон</div>
							<input type="text"
								   placeholder=""
								   name="requestPhone"
								   class="request_popup_input js-mask-phone-request"
								   style="margin-bottom: 15px; display: inline-block;"
								/>
							<div class="request-phone-error error">Неверный формат номера телефона</div>
							<div class="request_save_message"></div>
						</div>
					</div>

					<div class="request_popup_step" data-id="3" data-step-name="Email">
						<div class="request_popup_head">
							<?php if (!Yii::app()->session["specialityId"]) { ?>Шаг 3. <?php } ?>
							Оставьте контактные данные
						</div>
						<div class="request_popup_steps_wrap request_popup_dotted">
							<div class="request_popup_head_big">
								Ваше имя: <span class="request_popup_client_val"></span> <br>
								Ваш телефон: <span class="request_popup_phone_val"></span>
							</div>
							<div class="request_popup_text_success">
								Ваша заявка на приём принята!<br>
								В течении 15 минут, с вами свяжется наш оператор. <br>
								Спасибо что воспользовались нашим сервисом!<br><br><br>

								Оставьте вашу почту, чтобы получить информацию о записи.
								<div class="l-ib request_popup_phone_valid_inp">
									<input type="text" class="request_popup_input" name="client_email" data-send-button=".request_popup_email_send" value="">
								</div>
								<div class="request_popup_btn request_popup_email_send ui-btn ui-btn_green">Отправить</div>
								<div class="request_email_error error ui-btn ui-btn_gray">Неверный формат электронной почты</div>
							</div>
						</div>
					</div>

				</div>
			</div>

			<div class="request_popup_bottom">
				<div class="request_popup_bottom_padding">
					<div class="request_popup_bottom_cont">
						<div class="request_popup_btn request_popup_btn_next step-next  ui-btn ui-btn_green">Продолжить</div>
						<div class="request_popup_btn request_popup_save step-save  ui-btn ui-btn_green">Записаться</div>
						<div style="margin-top:10px;">
							<?php
							if (!empty($phone)) {
								?>
								Тел. для справок: <?php echo $phone->prettyFormat(); ?><br>
							<?php
							}
							?>
							&nbsp;
							<!--Сервис предоставлен <a href="http://<?php echo $docdocUrl; ?>" target="_parent">docdoc.ru</a>-->
						</div>
						<!--<div class="request_popup_howwork_btn">Как это работает?</div>-->

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
					if (!empty($phone)) {
					?>
					<div class="request_popup_dotted">
						Если у вас возникли вопросы,<br>
						позвоните нам по телефону:
						<div class="request_popup_close_phone"><?php echo $phone->prettyFormat(); ?></div>
					</div>
					<?php
					}
					?>
				</div>
				<div class="request_popup_close_yes request_popup_btn ui-btn ui-btn_grey hover">Прервать</div>
				<div class="request_popup_btn request_popup_close_no ui-btn ui-btn_green">Продолжить</div>
			</div>
		</div>

		<input type="hidden" name="req_id" value="">
		<input type="hidden" name="client_id" value="">
		<input type="hidden" name="clinic" value="<?php echo $clinic->id; ?>">
		<input type="hidden" name="doctor" value="<?php echo $doctor->id; ?>">
		<input type="hidden" name="date_admission" value="">
		<input type="hidden" name="slotId" value="">
	</form>
</div>
