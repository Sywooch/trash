<?php

use dfs\docdoc\models\DoctorModel;
use dfs\docdoc\models\ClinicModel;
use dfs\docdoc\models\SectorModel;
use dfs\docdoc\models\TipsMessageModel;
use dfs\docdoc\extensions\TextUtils;

/**
 * @var DoctorModel $doctor
 * @var SectorModel $speciality
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

$clinicCount = count($doctor->getActiveClinics());
$onlineBooking = $clinicId ? $doctor->canOnlineBooking($clinicId) : null;

$specialities = CHtml::listData($doctor->visibleSectors, 'id', 'name');

$extraInfo = $doctor->departure ?
	'Выезжает на дом' . ($doctor->kids_reception ? ', принимает детей' : '') :
	($doctor->kids_reception ? 'Принимает детей' : '');
?>

<div class="doctor_info">

	<h1 class="doctor_name">
		<span class="doctor_name_word"><?php echo substr($doctor->name, 0, $posName); ?></span><br/>
		<?php echo substr($doctor->name, $posName + 1); ?>
	</h1>

	<div class="doctor_info_aside">
		<?php if ($rating): ?>
			<div class="doctor_rating js-tooltip-tr" title="Рейтинг врача сформирован на основании следующих показателей: образование, опыт работы, научная степень.">
				<p class="doctor_rating_numbers">
					<span class="doctor_rating_main"><?php echo $rating['main']; ?></span><span class="doctor_rating_sub">.<?php echo $rating['sub']; ?></span>
				</p>
				<span class="doctor_rating_disclaimer">рейтинг</span>
			</div>
		<?php endif; ?>
	</div>

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

	<?php if ($doctor->status == DoctorModel::STATUS_BLOCKED || !$clinic || $clinic->status == ClinicModel::STATUS_BLOCKED): ?>

		<div class="doctor_desc__request">
			<p class="request_unaviable">Уважаемые посетители, в настоящий момент запись к данному врачу ограничена.
				<br/>
				Вы можете
				<a href="/doctor/<?php echo $speciality->rewrite_name; ?>">выбрать из доступных</a>
				<?php echo mb_strtolower($speciality->name_plural_genitive); ?> или записаться по телефону <!-- phone number -->
			</p>
		</div>

	<?php else: ?>

		<form class="doctor_desc__request mtm" method="post" action="/request?doctor=<?php echo $doctor->id; ?>">
			<input type="hidden" name="doctor" value="<?php echo $doctor->id; ?>"/>
			<input type="hidden" name="clinicId" value="<?php echo $clinicId; ?>"/>
			<input type="submit" value="Запись на приём"
				   class="ui-btn ui-btn_green request-button <?php echo $onlineBooking && $clinicCount == 1 ? 'request-online' : 'js-request-popup js-popup-tr'; ?>"
				   data-stat="<?php echo $onlineBooking && $clinicCount == 1 ? 'btnCardFullDoctorOnline' : 'btnCardFullDoctor'; ?>"
				   data-clinic="<?php echo $clinicId; ?>"
				   data-doctor="<?php echo $doctor->id; ?>"
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
				   id="btn_<?php echo $doctor->id?>"
				   data-request-tel=""
				   data-request-tel-digit=""/>
			<input type="hidden" name="requestBtnType" value="requestCardFullDoctor"/>
		</form>

	<?php endif; ?>

	<?php
		if ($tipMessage) {
			echo '<div class="tips_message" style="color: ' . ($tipMessage->tips->color ?: '#289B4C') . ';">',
			$tipMessage->getMessage(),
			'</div>';
		}
	?>

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

	<?php if ($extraInfo): ?>
		<p class="mtn t-fs-s"><?php echo $extraInfo; ?></p>
	<?php endif; ?>

	<p class="t-fs-s">
		<?php echo $doctor->text; ?>
	</p>

</div>
