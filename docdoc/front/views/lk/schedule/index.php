<?php
/**
 * @var \dfs\docdoc\models\DoctorModel $doctor
 * @var \dfs\docdoc\models\DoctorClinicModel $doctorClinic
 */

$visitDuration = [ 30, 45, 60, 90 ];
$calendarScales = [ 15, 20, 30, 45, 60, 90 ];

$rules = $doctorClinic->getScheduleRules();

$scheduleStep = empty($rules['schedule_step']) ? 30 : $rules['schedule_step'];
$calendarScale = empty($rules['calendarScale']) ? 30 : $rules['calendarScale'];
?>

<div class="result_title__ct">
	<h1 class="result_main__title">
		<?php echo $doctor->name; ?>
		<input type="hidden" id="sc_doctor_id" value="<?php echo $doctor->id; ?>"/>
		<input type="hidden" id="sc_clinic_id" value="<?php echo $doctorClinic->clinic_id; ?>"/>
	</h1>
</div>

<div>
	<div>

		<div class="schedule-params result_filters">

			<div class="schedule-params-cont schedule-rule">
				<h2 class="copy-param">Настройка расписания</h2>

				<div>
					<label>Продолжительность приема</label>
					<div class="controls">
						<select name="visitDuration" id="schedule_step">
						<?php
							foreach ($visitDuration as $v) {
								echo '<option value="', $v, '"', ($v == $scheduleStep ? ' selected="selected"' : ''), '>', $v, '</option>';
							}
						?>
						</select>
						минут
					</div>
				</div>

				<div>
					<label>Размер календарной сейтки</label>
					<div class="controls">
						<select name="calendarScale" id="calendarScale">
						<?php
							foreach ($calendarScales as $v) {
								echo '<option value="', $v, '"', ($v == $calendarScale ? ' selected="selected"' : ''), '>', $v, '</option>';
							}
						?>
						</select>
						минут
					</div>
				</div>
			</div>

			<div id="copy_filter" class="schedule-params-cont hide-copy-params">

				<h2 class="copy-param">Автозаполнение графика</h2>

				<div class="schedule-rule">
					<div class="copy-param">
						<label>Скопировать расписание недели</label>
						<div class="controls">
							<select name="copyWeek" id="copyWeek"></select>
						</div>
					</div>
					<div class="copy-param">
						<label>Вставить его начиная с даты</label>
						<div class="controls">
							<select name="copyStartDate" id="copyStartDate"></select>
						</div>
					</div>
					<div class="copy-param">
						<label>Заполнить на</label>
						<div class="controls">
							<select name="copyWeekNum" id="copyWeekNum">
								<option value="1">1</option>
								<option value="2">2</option>
								<option value="3">3</option>
								<option value="4">4</option>
								<option value="5" selected="selected">5</option>
								<option value="5">6</option>
								<option value="6">7</option>
								<option value="7">8</option>
								<option value="8">9</option>
								<option value="10">10</option>
							</select>
							недель вперед
						</div>
					</div>
				</div>

				<div class="copy-param filter_submit-ct">
					<input class="button_lk" type="button" value="Скопировать" id="copyButon" />
				</div>

			</div>

		</div>

		<div class="schedule-alert" id="scheduleAlert">
			<p>При изменении продолжительности приема все расписание будет пересчитано исходя из нового значения.
				Изменится количество приемов и общая продолжительность приема врача. Вы действительно хотите внести эти изменения?</p>
			<div>
				<input class="button_lk" type="button" value="Изменить продолжительность приема" id="changeDuration" />
				<input class="button_lk" type="button" value="Не изменять" id="cancelChangeDuration" />
			</div>
		</div>

	</div>

	<br clear="all" />

	<div id="full_calendar" style="width:900px; margin-top:30px;"><div class="calendar_container"></div></div>

	<script src="/js/lk/schedule.js"></script>

</div>
