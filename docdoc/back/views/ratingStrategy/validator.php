<?php

/**
 * @var \dfs\docdoc\back\controllers\PageController $this
 * @var dfs\docdoc\models\RatingStrategyModel       $model
 * @var dfs\docdoc\objects\Formula                  $formula
 * @var CActiveForm                                 $form
 */
$this->breadcrumbs = array(
	'Стратегии' => array('index'),
	'Валидация формулы для стратегии',
);

?>

<h1>Валидация формулы для стратегии - <?php echo $model->name; ?></h1>
<div style="float:left;">
	<?php
	$this->widget('zii.widgets.CBreadcrumbs', array(
		'links'=> $this->breadcrumbs,
		'htmlOptions' => ['class'=>'']
	));
	?>
	<div class="form">

		<?php $form = $this->beginWidget(
			'CActiveForm',
			array(
				'id'                   => 'strategy-form',
				'enableAjaxValidation' => false,
				'action'               => "/2.0/ratingStrategy/check/{$model->id}"
			)
		); ?>

		<div class="row">
			<?php echo $form->labelEx($model, 'params'); ?>
			<p><?=$model->params?></p>
		</div>

		<?php if ($model->for_object == $model::FOR_DOCTOR) {?>
			<div class="row">
				<label>Введите ID врача:</label>
				<input type="text" name="doctorId" value="<?php isset($doctor) ? $doctor->id : '';?>" />
			</div>
		<?php }?>

		<div class="row">
			<label>Введите ID клиники:</label>
			<input type="text" name="clinicId" value="<?php isset($doctor) ? $doctor->id : '';?>" />
		</div>

		<div class="row">
			<?php echo CHtml::submitButton('Проверить'); ?>
		</div>

		<?php $this->endWidget(); ?>
		<br />
		<?php if (isset($formula)) {?>
			<label>Параметры в формуле:</label>
			<?php if (isset($doctor)) {?>
				<ul class="formula-params">
				<?php foreach ($formula->getVariables('doctor') as $v) {?>
					<li>$doctor.<?=$v?> = <?=(float)$doctor->$v?></li>
				<?php }?>
				</ul>
			<?php }?>

			<?php if (isset($clinic)) {?>
				<ul class="formula-params">
				<?php foreach ($formula->getVariables('clinic') as $v) {?>
					<li>$clinic.<?=$v?> = <?=(float)$clinic->$v?></li>
				<?php }?>
				</ul>
			<?php }?>

			<label>Результат:</label>
			<div class="formula-result">
				<?php echo isset($result) ? $result : 'Не найден врач в данной клинике!';?>
			</div>

		<?php }?>

	</div><!-- form -->