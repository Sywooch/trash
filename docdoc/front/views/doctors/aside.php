<?php
/**
 * @var \dfs\docdoc\models\DoctorModel $doctor
 */

$education = $doctor->getEducation();
?>

<div class="doctor_aside">

	<?php echo $this->renderPartial('/doctors/map', ['doctor' => $doctor]); ?>

	<section class="doctor_exp">

		<div class="t-fs-l mtsl">Информация о враче</div>

		<?php if ($doctor->text_spec): ?>
			<article class="doctor_exp_item">
				<h3 class="doctor_exp_title">Специализация</h3>
				<div><?=$doctor->text_spec?></div>
			</article>
		<?php endif; ?>

		<?php if ($doctor->text_education || $education): ?>
			<article class="doctor_exp_item">
				<h3 class="doctor_exp_title">Образование</h3>
				<?php if ($education): ?>
					<ul class="list">
						<?php foreach ($education as $item): ?>
							<li class="list_item"><?=$item?></li>
						<?php endforeach; ?>
					</ul>
				<?php elseif ($doctor->text_education): ?>
					<?=$doctor->text_education?>
				<?php endif; ?>
			</article>
		<?php endif; ?>

		<?php if ($doctor->text_association): ?>
			<article class="doctor_exp_item">
				<h3 class="doctor_exp_title">Член ассоциаций врачей</h3>
				<p class="mvn"><?=$doctor->text_association?></p>
			</article>
		<?php endif; ?>

		<?php if ($doctor->text_course): ?>
			<article class="doctor_exp_item">
				<h3 class="doctor_exp_title">Курсы повышения квалификации</h3>
				<p><?php echo str_replace("\n", '<br/>', $doctor->text_course); ?></p>
			</article>
		<?php endif; ?>

		<?php if ($doctor->text_experience): ?>
			<article class="doctor_exp_item">
				<h3 class="doctor_exp_title">Опыт работы</h3>
				<div>
					<p class="mvn"><?=$doctor->text_experience?></p>
				</div>
			</article>
		<?php endif; ?>

	</section>

</div>
