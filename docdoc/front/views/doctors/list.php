<?php
/**
 * @var \dfs\docdoc\models\DoctorModel[] $doctors
 */
?>

<main class="l-main l-wrapper clinics" role="main">

	<?php // SEO ?>

	<?php echo $this->renderPartial('/doctors/listFilters'); ?>

	<section class="doctor_list">
		<?php foreach ($doctors as $doctor): ?>
			<?php echo $this->renderPartial('/doctors/teaser', [
				'doctor' => $doctor,
				'teaserType' => 'doctorList',
				// 'speciality' => $doctorList->getSpeciality(),
			]); ?>
		<?php endforeach; ?>
	</section>

	<?php echo $this->renderPartial('/elements/pager', [
		'url' => '/doctors/page/',
		'page' => 1,
		'count' => 10,
	]); ?>

	<?php // SEO ?>

</main>
