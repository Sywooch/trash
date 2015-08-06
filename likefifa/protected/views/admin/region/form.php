<?php
use likefifa\models\RegionModel;

/**
 * @var RegionModel      $model
 * @var CActiveForm      $form
 * @var RegionController $this
 * @var string           $h1
 */
?>

<h1><?php echo $h1; ?></h1>

<div class="form">

	<?php $form = $this->beginWidget(
		'CActiveForm',
		array(
			'id'                   => 'region-form',
			'enableAjaxValidation' => false,
		)
	); ?>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model, 'prefix'); ?>
		<?php echo $form->textField($model, 'prefix', array("size" => 8)); ?>
		<?php echo $form->error($model, 'prefix'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'name'); ?>
		<?php echo $form->textField($model, 'name', array("size" => 32)); ?>
		<?php echo $form->error($model, 'name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'name_genitive'); ?>
		<?php echo $form->textField($model, 'name_genitive', array("size" => 32)); ?>
		<?php echo $form->error($model, 'name_genitive'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'name_prepositional'); ?>
		<?php echo $form->textField($model, 'name_prepositional', array("size" => 32)); ?>
		<?php echo $form->error($model, 'name_prepositional'); ?>
	</div>

	<div class="row">
		<?php echo $form->checkBox($model, 'is_active'); ?>
		<?php echo $form->labelEx($model, 'is_active', array("class" => "inline-block")); ?>
		<?php echo $form->error($model, 'is_active'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить'); ?>
	</div>

	<?php $this->endWidget(); ?>

</div>