<?php

use dfs\docdoc\models\DoctorModel;
use dfs\docdoc\models\ClinicModel;
use dfs\docdoc\models\SectorModel;
use dfs\docdoc\models\TipsMessageModel;
use dfs\docdoc\extensions\TextUtils;

/**
 * @var DoctorModel $doctor
 * @var SectorModel $speciality
 * @var string $teaserType
 */

$speciality = empty($speciality) ? $doctor->getDefaultSector() : $speciality;

$posName = strpos($doctor->name, ' ');
$experience = $doctor->getExperience();
$awards = $doctor->getAwards();

$tipMessage = TipsMessageModel::model()->findRandomForRecord($doctor->id);

$rating = TextUtils::ratingFormat($doctor->getDoctorRating());
$clinic = $doctor->getDefaultClinic();
$clinicId = $clinic ? $clinic->id : null;
$countReviews = $doctor->countReviews();

$specialities = CHtml::listData($doctor->visibleSectors, 'id', 'name');
?>

<article class="doctor_card js-doctor-short">

	<div class="doctor_person">
		<a href="/doctor/<?php echo $doctor->rewrite_name; ?>" class="doctor_img_link">
			<img src="<?php echo $doctor->getImg('med'); ?>" class="doctor_img" />
		</a>
	</div>

	<div class="doctor_info">

		<h2 class="doctor_name">
			<a href="/doctor/<?php echo $doctor->rewrite_name; ?>">
				<span class="doctor_name_word"><?php echo substr($doctor->name, 0, $posName); ?></span><br/>
				<?php echo substr($doctor->name, $posName + 1); ?>
			</a>
		</h2>

		<div class="doctor_info_aside">

			<div class="doctor_rating_wrap">
				<div class="reviews_count<?php echo $countReviews > 0 ? '' : ' reviews_counter_no'; ?>">
					<a href="/doctor/<?php echo $doctor->rewrite_name; ?>#reviews" class="reviews_counter"><?php echo $countReviews ?: 'нет'; ?></a>
					<a href="/doctor/<?php echo $doctor->rewrite_name; ?>#reviews" class="reviews_counter_text">
						<?php echo RussianTextUtils::caseForNumber($countReviews, ['отзыв', 'отзыва', 'отзывов']); ?>
					</a>
				</div>

				<?php if ($rating): ?>
					<div class="doctor_rating js-tooltip-tr" title="Рейтинг врача сформирован на основании следующих показателей: образование, опыт работы, научная степень.">
						<p class="doctor_rating_numbers">
							<span class="doctor_rating_main"><?php echo $rating['main']; ?></span><span class="doctor_rating_sub">.<?php echo $rating['sub']; ?></span>
						</p>
						<span class="doctor_rating_disclaimer">рейтинг</span>
					</div>
				<?php endif; ?>
			</div>

			<?php if ($doctor->status == DoctorModel::STATUS_BLOCKED || !$clinic || $clinic->status == ClinicModel::STATUS_BLOCKED): ?>

				<div class="doctor_desc__request">
					<p class="request_unaviable">Уважаемые посетители, в настоящий момент запись к данному врачу ограничена.
						<br/>
						Вы можете
						<a href="/doctor/<?php echo $speciality->rewrite_name; ?>">выбрать из доступных</a>
						<?php echo mb_strtolower($speciality->name_plural_genitive); ?> или записаться по телефону
					</p>
				</div>

			<?php else: ?>

				<form class="doctor_desc__request" method="post" action="/request?doctor=<?php echo $doctor->id; ?>">
					<input type="hidden" name="doctor" value="<?php echo $doctor->id; ?>"/>
					<input type="hidden" name="clinicId" value="<?php echo $clinicId; ?>"/>
					<input class="ui-btn ui-btn_green js-request-popup js-popup-tr request-button"
						   data-stat="btnCardShortDoctor" type="submit" value="Запись на приём"
						   data-doctor-name="<?php echo CHtml::encode($doctor->name); ?>"
						   data-clinic-id="<?php echo $clinicId; ?>"
						   data-clinic-name="<?php echo $clinic ? CHtml::encode($clinic->name) : ''; ?>"
						   data-clinic-metro="<?php echo $clinic ? implode(', ', array_keys($clinic->getUniqueStations())) : ''; ?>"
						   data-doctor-id="<?php echo $doctor->id; ?>"
						   data-doctor-reviews="<?php echo $countReviews; ?>"
						   data-doctor-rating="<?php echo $rating['main'] . '.' . $rating['sub']; ?>"
						   data-doctor-experience="<?php echo $experience; ?>"
						   data-doctor-awards="<?php echo $awards; ?>"
						   data-doctor-price="<?php echo $doctor->price ; ?>"
						   data-doctor-special-price="<?php echo $doctor->special_price; ?>"
						   data-doctor-image="<?php echo $doctor->getImg(); ?>"
						   data-doctor-spec="<?php echo implode(', ', $specialities); ?>"
						   data-popup-id="js-popup-request"
						   data-popup-width="440"
						   data-request-type="doctor"
						   data-request-tel=""
						   data-request-tel-digit=""
						   id="btn_<?php echo $doctor->id; ?>"
						/>

					<input type="hidden" name="requestBtnType" value="requestCardShortDoctor"/>
				</form>

			<?php endif; ?>

			<?php
				if ($tipMessage) {
					echo '<div class="tips_message" style="color: ' . ($tipMessage->tips->color ?: '#289B4C') . ';">',
							$tipMessage->getMessage(),
						'</div>';
				}
			?>
		</div>

		<div class="doctor_card_info_wrap">
			<p class="mvn t-fs-s">
				<?php echo implode(', ', $specialities); ?>
			</p>

			<p class="mbm mtn t-fs-s">
				<?php
					if ($experience > 0) {
						echo 'Стаж ', $experience, ' ', RussianTextUtils::caseForNumber($experience, ['год', 'года', 'лет']);
						echo $awards ? ' / ' : '';
					}
					echo $awards;
				?>
			</p>

			<p class="strong">
				<span class="js-tooltip-tr" title="Цена указана за первичный прием. В нее не входит стоимость дополнительных исследований и выезда на дом.">
					Стоимость приема -
					<?php if ($doctor->special_price > 0): ?>
						<del class="oldprice"><?php echo $doctor->price; ?>р.</del>
						<ins class="price_special"><?php echo $doctor->special_price; ?>р. только на DocDoc!</ins>
					<?php else: ?>
						<span class="price"><?php echo $doctor->price; ?>р.</span>
					<?php endif; ?>
				</span>
			</p>

			<?php echo $teaserType == 'clinicList' ? '<noindex>' : ''; ?>
			<p class="doctor_desc t-fs-s" data-ellipsis-height="40">
				<?php echo $doctor->text; ?>
			</p>
			<?php echo $teaserType == 'clinicList' ? '</noindex>' : ''; ?>
		</div>
	</div>

	<?php
		if ($teaserType == 'doctorList') {
			$i = 0;
			$showClinicName = Yii::app()->params['doctorCard']['showClinicName'];

			echo '<div class="doctor_address_wrap doctor_address_dotted">';
			foreach ($doctor->getActiveClinics() as $clinic) {
				echo $this->renderPartial('/clinic/stations', [
					'clinic'         => $clinic,
					'stationUrl'     => $speciality ? '/doctor/' . $speciality->rewrite_spec_name  . '/' : '/search/stations/',
					'showClinicName' => $showClinicName,
					'extraClass'     => $i++ > 0 ? 'i-ext-address' : '',
				]);
			}
			echo '</div>';
		}
	?>

</article>
