<?php
/**
 * @var \dfs\docdoc\back\controllers\UndergroundStationController $this
 * @var dfs\docdoc\models\UndergroundStationModel                 $model
 * @var CActiveForm                                               $form
 */

?>
<div class="form">

	<?php $form = $this->beginWidget(
		'CActiveForm',
		array(
			'id'                   => 'underground-station-form',
			'enableAjaxValidation' => false,
		)
	); ?>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model, 'name'); ?>
		<?php echo $form->textField($model, 'name'); ?>
		<?php echo $form->error($model, 'name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'rewrite_name'); ?>
		<?php echo $form->textField($model, 'rewrite_name'); ?>
		<?php echo $form->error($model, 'rewrite_name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'underground_line_id'); ?>
		<?php echo $form->dropDownList(
			$model,
			'underground_line_id',
			dfs\docdoc\models\UndergroundLineModel::model()->getLineList()
		); ?>
		<?php echo $form->error($model, 'underground_line_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'index'); ?>
		<?php echo $form->textField($model, 'index'); ?>
		<?php echo $form->error($model, 'index'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'longitude'); ?>
		<?php echo $form->textField($model, 'longitude'); ?>
		<?php echo $form->error($model, 'longitude'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'latitude'); ?>
		<?php echo $form->textField($model, 'latitude'); ?>
		<?php echo $form->error($model, 'latitude'); ?>
	</div>

	<div class="row">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить'); ?>
	</div>

	<?php $this->endWidget(); ?>

</div>