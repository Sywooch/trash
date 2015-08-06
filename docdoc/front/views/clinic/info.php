<?php

use dfs\docdoc\models\ClinicModel;
use dfs\docdoc\models\SectorModel;
use dfs\docdoc\extensions\TextUtils;

/**
 * @var ClinicModel $clinic
 * @var SectorModel[] $sectors
 */

$phone = $clinic->getClinicPhone();
$countReviews = $clinic->getCountReviews();
$rating = $clinic->rating_show ? TextUtils::ratingFormat($clinic->rating_show) : null;
$uniqueStations = $clinic->getUniqueStations();
$district = $clinic->district;
$clinicName = CHtml::encode($clinic->name);

$sectorLinks = [];
foreach ($sectors as $sector) {
	if (!$sector->is_double && $sector->spec_name) {
		$sectorLinks[] = '<a href="/clinic/spec/' . $sector->rewrite_spec_name . '" class="notlink">' . $sector->spec_name . '</a>';
	}
}
?>

<link href="/js/jquery.bxslider/jquery.bxslider.css" rel="stylesheet" />

<div class="clinic_card_left">
	<div class="clinic_card_cont">

		<div class="doctor_info_aside">
			<div class="doctor_rating_wrap">

				<div class="reviews_count<?php echo $countReviews > 0 ? '' : ' reviews_counter_no'; ?>">
					<a href="/clinic/<?php echo $clinic->rewrite_name; ?>#reviews" class="reviews_counter"><?php echo $countReviews ?: 'нет'; ?></a>
					<a href="/clinic/<?php echo $clinic->rewrite_name; ?>#reviews" class="reviews_counter_text">
						<?php echo RussianTextUtils::caseForNumber($countReviews, ['отзыв', 'отзыва', 'отзывов']); ?>
					</a>
				</div>

				<div class="doctor_rating js-tooltip-tr" title="Рейтинг сформирован на основе отзывов пациентов о врачах клиники на сайте docdoc.ru">
					<p class="doctor_rating_numbers">
						<?php if ($rating): ?>
							<span class="doctor_rating_main"><?php echo $rating['main']; ?></span><span class="doctor_rating_sub">.<?php echo $rating['sub']; ?></span>
						<?php else: ?>
							<span class="doctor_rating_no">нет</span>
						<?php endif; ?>
					</p>
					<span class="doctor_rating_disclaimer">рейтинг</span>
				</div>

			</div>
		</div>

		<h1 class="clinic_card_name"><?php echo $clinic->name; ?></h1>

		<p class="mtn t-fs-s clinic_card_spec">
			<?php echo $clinic->getTypeOfInstitution(); ?>
		</p>

		<div style="clear:right;"></div>

		<ul class="clinic_card_gallery">
			<?php if ($clinic->photos): ?>
				<?php
					$i = 0;
					foreach ($clinic->photos as $photo) {
						switch (++$i) {
							case 2: $alt = '&quot;' . $clinicName . '&quot; фото'; break;
							case 3: $alt = 'медицинский центр &quot;' . $clinicName . '&quot;'; break;
							default: $alt = $clinicName; break;
						}
						echo '<li class="clinic_card_gallery_item"><img src="', $photo->getUrl(), '" alt="', $alt, '"/></li>';
					}
				?>
			<?php else: ?>
				<li class="clinic_card_gallery_item clinic_card_gallery_nophoto"></li>
			<?php endif; ?>
		</ul>

	</div>
</div>

<div class="clinic_card_right">
	<div class="clinic_card_cont">

		<a href="/clinic/print/<?php echo $clinic->rewrite_name; ?>" target="_blank" class="clinic_card_print_ico" rel="nofollow"></a>

		<h2 class="t-fs-l mbs">Информация</h2>
		<div class="doctor_address_item">
			<div class="t-fs-s i-address-doctor i-ext-address">
				<div class="clinic-address"><?php echo $clinic->getAddress(); ?></div>
				<?php
					$n = count($uniqueStations);

					if ($district) {
						if ($district->area) {
							echo '<a href="/clinic/area/' . $district->area->rewrite_name . '" class="notlink">',
								$district->area->name,
								'</a>' . ($n ? ', ' : '');
						}
						echo '<a href="/clinic/district/' . $district->rewrite_name . '" class="notlink">',
							$district->name,
							'</a>' . ($n ? ', ' : '');
					}

					foreach ($uniqueStations as $station) {
						echo '<a href="/clinic/station/' . $station->rewrite_name . '" class="notlink">',
							'м. ', $station->name,
							'</a>', (--$n > 0 ? ', ' : '');
					}
				?>
			</div>
		</div>

		<?php echo $this->renderPartial('/clinic/map', ['clinic' => $clinic]); ?>

		<div class="clinic_time_card">
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

		<?php if ($phone): ?>
			<div class="clinic_card_phone t-fs-l">
			<?php if (Yii::app()->mobileDetect->isAdaptedMobile()): ?>
				<a class="request_tel_call" href="tel:+<?php echo $phone->getNumber(); ?>">
					<?php echo $phone->prettyFormat(); ?>
				</a>
			<?php else: ?>
				<?php echo $phone->prettyFormat(); ?>
			<?php endif; ?>
			</div>
		<?php else: ?>
			<div style="height: 7px;"></div>
		<?php endif; ?>

		<?php if ($clinic->status == ClinicModel::STATUS_ACTIVE): ?>
			<form class="doctor_desc__request form_request_clinic" method="post" action="/clinic/request">
				<input type="hidden" name="clinicId" value="<?php echo $clinic->id; ?>"/>
				<input class="ui-btn ui-btn_green js-request-popup js-popup-tr request-button"
					   type="submit"
					   value="Запись на приём"
					   data-stat="btnCardFullClinic"
					   data-clinic-id="<?php echo $clinic->id; ?>"
					   data-clinic-name="<?php echo $clinicName; ?>"
					   data-clinic-area="<?php echo $clinic->district->name; ?>"
					   data-clinic-metro="<?php echo implode(', ', array_keys($uniqueStations)); ?>"
					   data-popup-id="js-popup-request-clinic"
					   data-popup-width="440"
					   data-request-type="clinic"
					   data-request-tel="<?php echo $phone ? $phone->prettyFormat('+7 ') : ''; ?>"
					   data-request-tel-digit="+<?php echo $phone ? $phone->getNumber() : ''; ?>"
					/>
				<input type="hidden" name="requestBtnType" value="requestCardFullClinic"/>
			</form>
		<?php endif; ?>

	</div>
</div>

<div class="clinic_card_cont clinic_card_desc_wrap">

	<h2 class="t-fs-l mbs">О Клинике / специализация</h2>

	<p class="clinic_card_desc"><?php echo $clinic->description; ?></p>

	<?php if ($sectorLinks): ?>
		<p class="clinic_card_desc">
			Специализации: <?php echo implode(', ', $sectorLinks); ?>
		</p>
	<?php endif; ?>

</div>
