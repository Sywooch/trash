<?php

use dfs\docdoc\extensions\TextUtils;

/**
 * @var \dfs\docdoc\models\ClinicModel $clinic
 * @var \dfs\docdoc\models\SectorModel $speciality
 */

$phone = $clinic->getClinicPhone();
$countReviews = $clinic->getCountReviews();
$rating = $clinic->rating_show ? TextUtils::ratingFormat($clinic->rating_show) : null;
$uniqueStations = $clinic->getUniqueStations();
$schedule = $clinic->getSchedule();
$clinicName = CHtml::encode($clinic->name);
?>

<article class="doctor_card js-clinic-short">

	<div class="doctor_person">
		<a href="/clinic/<?php echo $clinic->rewrite_name; ?>" class="doctor_img_link">
			<img src="<?php echo $clinic->getLogo(); ?>" class="doctor_img" alt="<?php echo $clinicName; ?>"/>
		</a>
	</div>

	<div class="doctor_info">

		<h2 class="doctor_name">
			<a href="/clinic/<?php echo $clinic->rewrite_name; ?>"><?php echo $clinic->name; ?></a>
		</h2>

		<div class="doctor_info_aside">

			<?php if ($schedule): ?>
			<div class="clinic_time_search">
				<div class="clinic_time_search_ico"></div>

					<div class="clinic_time_search_txt clinic_time_txt ui-bg-grey">
						<table cellspacing="0" cellpadding="0">
							<?php
								foreach ($schedule as $sc) {
									echo '<tr><td>' . $sc['DayTitle'] . ':</td><td>' . $sc['StartTime'] . ' - ' . $sc['EndTime'] . '</td></tr>';
								}
							?>
						</table>
					</div>
			</div>
			<?php endif; ?>

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

			<form class="doctor_desc__request" method="post" action="/clinic/request">
				<input type="hidden" name="clinicId" value="<?php echo $clinic->id; ?>"/>
				<input class="ui-btn ui-btn_green js-request-popup js-popup-tr request-button"
					   type="submit"
					   value="Запись на приём"
					   data-stat="btnCardShortClinic"
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
				<input type="hidden" name="requestBtnType" value="requestCardShortClinic"/>
			</form>

			<?php if ($phone): ?>
				<div class="request_tel">
					<p class="request_tel_text">или по телефону</p>
					<span class="request_tel_number"><?php echo $phone->prettyFormat(); ?></span>
				</div>
			<?php endif; ?>

		</div>

		<div class="doctor_card_info_wrap">
			<p class="mbm mtn t-fs-s clinic_list_spec">
				<?php echo $clinic->getTypeOfInstitution(); ?>
			</p>

			<p class="strong">Первичная стоимость приёма - <?php echo $clinic->getPriceLevel(); ?></p>

			<p class="doctor_desc t-fs-s" data-ellipsis-height="40">
				<?php echo $clinic->description; ?>
			</p>
		</div>

	</div>

	<div class="doctor_address_wrap doctor_address_dotted">
		<?php echo $this->renderPartial('/clinic/stations', [
			'clinic' => $clinic,
			'stationUrl' => '/clinic/' . ($speciality ? 'spec/' . $speciality->rewrite_spec_name . '/' : 'station/'),
		]); ?>
	</div>

</article>
