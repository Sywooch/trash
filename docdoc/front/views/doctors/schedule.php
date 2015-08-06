<?php
/**
 * @var array $schedule
 */
?>

<div class="schedule_doctor_wrap">

	<div class="schedule_doctor_tab schedule_doctor_tab_active">
		<span class="schedule_doctor_tab_ico l-ib"></span><span class="l-ib schedule_doctor_tab_txt t-fs-xs">Время работы</span>
	</div>

	<div class="schedule_doctor_slider_wrap">
		<ul class="schedule_doctor_slider">
			<?php foreach ($schedule as $day => $sc): ?>
				<li class="l-ib schedule_doctor_slider_item">
					<div class="schedule_doctor_slider_day"><?php echo $sc['Day']; ?></div>
					<div class="schedule_doctor_slider_time">
						<?php if ($sc['Work']): ?>
							<span class="schedule_doctor_slider_txt l-ib">с</span><a href="#" data-stat="btnCardFullScheduleOnline" data-doctor="<?=$sc['DoctorId']?>"  data-clinic="<?=$sc['ClinicId']?>"  data-date="<?=$sc['Date']?>"><?php echo $sc['Begin']; ?></a>
							<br>
							<span class="schedule_doctor_slider_txt l-ib">до</span><a href="#" data-stat="btnCardFullScheduleOnline" data-doctor="<?=$sc['DoctorId']?>"  data-clinic="<?=$sc['ClinicId']?>"  data-date="<?=$sc['Date']?>"><?php echo $sc['End']; ?></a>
						<?php else: ?>
							<div class="schedule_doctor_slider_holiday">Выходной<br> день</div>
						<?php endif; ?>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>

</div>
