<?php

use \dfs\docdoc\models\ClinicModel;
use dfs\docdoc\extensions\TextUtils;

/**
 * @var ClinicModel $clinic
 */

$phone = $clinic->getClinicPhone();
$countReviews = $clinic->getCountReviews();
$rating = $clinic->rating_show ? TextUtils::ratingFormat($clinic->rating_show) : null;
$stations = $clinic->getUniqueStations();
?>
<div class="clinic_card">

	<div class="clinic_card_left">
		<div class="clinic_card_cont">

			<div class="clinic_info_aside">
				<div class="rating_wrap">
					<div class="rating">
						<p class="rating_numbers">
							<?php if ($rating): ?>
								<span class="doctor_rating_main"><?php echo $rating['main']; ?></span><span class="doctor_rating_sub">.<?php echo $rating['sub']; ?></span>
							<?php else: ?>
								<span class="doctor_rating_no">нет</span>
							<?php endif; ?>
						</p>
						<span class="rating_disclaimer">рейтинг</span>
					</div>
				</div>
			</div>

			<h2 class="clinic_card_name"><?php echo $clinic->name; ?></h2>

			<p class="mtn t-fs-s clinic_card_spec">
				<?php echo $clinic->getTypeOfInstitution(); ?>
			</p>

			<?php if ($clinic->way_on_car || $clinic->way_on_foot): ?>

				<?php if ($clinic->way_on_foot): ?>
					<div class="strong">Как добраться пешком</div>
					<p class="clinic_desc"><?php echo $clinic->way_on_foot; ?></p>
					<?php if ($clinic->way_on_car): ?>
						<div style="height: 17px; overflow: hidden;"></div>
					<?php endif; ?>
				<?php endif; ?>

				<?php if ($clinic->way_on_car): ?>
					<div class="strong">Как добраться на машине</div>
					<p class="clinic_desc"><?php echo $clinic->way_on_car; ?></p>
				<?php endif; ?>

			<?php else: ?>

				<div class="strong">О Клинике / специализация</div>
				<p class="clinic_desc"><?php echo $clinic->description; ?></p>

			<?php endif; ?>

		</div>
	</div>

	<div class="clinic_card_right">
		<div class="clinic_card_cont">

			<div class="t-fs-l mbs">Информация</div>

			<div class="address_item">
				<div class="t-fs-s">
					<img alt="" src="/img/icons/i-address-grey.png" class="i-address">
					<div class="clinic-address"><?php echo $clinic->getAddress(); ?></div>
					<?php if ($stations): ?>
						м. <?php echo implode(', м. ', array_keys($stations));?>
					<?php endif; ?>
				</div>
			</div>

			<div class="clinic_time_card">
				<img alt="" src="/img/icons/i-clock_mini.png" class="i-time">
				<div class="clinic_time_txt">
					<table cellspacing="0" cellpadding="0">
						<?php
							foreach ($clinic->getSchedule() as $schedule) {
								echo '<tr><td>' . $schedule['DayTitle'] . ':</td><td>' . $schedule['StartTime'] . ' - ' . $schedule['EndTime'] . '</td></tr>';
							}
						?>
					</table>
				</div>
			</div>

			<?php if ($clinic->asterisk_phone): ?>
				<div class="clinic_card_phone t-fs-l">
				<?php if (Yii::app()->mobileDetect->isAdaptedMobile()): ?>
					<a class="request_tel_call" href="tel:+<?php echo $phone->getNumber(); ?>">
						<?php echo $phone->prettyFormat(); ?>
					</a>
				<?php else: ?>
					<?php echo $phone->prettyFormat(); ?>
				<?php endif; ?>
				</div>
			<?php endif; ?>

		</div>
	</div>
	<div style="clear:both;"></div>

	<div class="b-ymap-ct">
		<div class="b-ymap">
			<img src="http://static-maps.yandex.ru/1.x/?ll=<?php echo $clinic->longitude; ?>,<?php echo $clinic->latitude; ?>&size=650,320&z=15&l=map&pt=<?php echo $clinic->longitude; ?>,<?php echo $clinic->latitude; ?>,pm2dol" alt="">
		</div>
	</div>

</div>
