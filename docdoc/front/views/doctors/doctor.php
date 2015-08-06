<?php
/**
 * @var string $refURL
 * @var \dfs\docdoc\models\DoctorModel $doctor
 * @var \dfs\docdoc\models\DoctorModel[] $nearestDoctors
 * @var array $schedule
 * @var array $reviewsData
 */
?>
<main class="l-main l-wrapper" role="main">

	<?php if ($refURL): ?>
		<a href="<?php echo $refURL; ?>" class="i-goback link-goback">
			<span class="link">Вернуться к результатам поиска</span>
		</a>
	<?php endif; ?>

	<article class="doctor_card m-simple">
		<div class="doctor_main">

			<div class="doctor_person">
				<span class="doctor_img_link">
					<img class="doctor_img" src="<?php echo $doctor->getImg('med'); ?>" />
				</span>
			</div>

			<?php echo $this->renderPartial('/doctors/info', ['doctor' => $doctor]); ?>

			<?php echo $this->renderPartial('/doctors/aside', ['doctor' => $doctor]); ?>

			<?php
				if ($schedule) {
					echo $this->renderPartial('/doctors/schedule', ['schedule' => $schedule]);
				}
			?>

			<?php echo $this->renderPartial('/doctors/reviews', $reviewsData); ?>

		</div>
	</article>

	<?php $this->renderPartial('/doctors/nearest', [ 'doctors' => $nearestDoctors ]); ?>
	<div class="js-popup popup" data-popup-id="schedule-popup" id="schedule-popup" data-popupWidth="735">
	</div>
</main>
