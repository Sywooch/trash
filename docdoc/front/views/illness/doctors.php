<?php

use dfs\docdoc\models\DoctorModel;
use dfs\docdoc\models\IllnessModel;

/**
 * @var IllnessModel $illness
 * @var DoctorModel[] $doctors
 * @var int $countAll
 */
?>

<?php if (!empty($doctors)): ?>

	<div id="IllnessDoctors">
		<h3 class="h1 i-doctor_5">
			В нашей базе
			<span class="t-orange t-fs-xl"><?php echo $countAll; ?></span>
			<?php echo RussianTextUtils::caseForNumber($countAll, ['специалист', 'специалиста', 'специалистов']); ?>
			по лечению <?php echo $illness->full_name; ?>
		</h3>

		<section class="doctor_list">
			<?php foreach ($doctors as $doctor): ?>
				<?php echo $this->renderPartial('/doctors/teaser', [
					'doctor' => $doctor,
					'teaserType' => 'illness',
				]); ?>
			<?php endforeach; ?>
		</section>

		<a href="/doctor/<?php echo $illness->sector->rewrite_name; ?>">Показать всех специалистов</a>
	</div>

<?php endif; ?>
