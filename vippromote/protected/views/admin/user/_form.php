<?php
use likefifa\models\AdminModel;

/**
 * @var AdminModel  $model
 * @var CActiveForm $form
 */
?>

<div class="form">

	<?php $form = $this->beginWidget(
		'CActiveForm',
		array(
			'id'                   => 'admins-form',
			'enableAjaxValidation' => false,
		)
	); ?>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model, 'email'); ?>
		<?php echo $form->textField($model, 'email', array("size" => 64)); ?>
		<?php echo $form->error($model, 'email'); ?>
	</div>

	<?php if ($model->isNewRecord) { ?>
		<div class="row">
			<?php echo $form->labelEx($model, 'password'); ?>
			<?php echo $form->passwordField($model, 'password', array("size" => 32)); ?>
			<?php echo $form->error($model, 'password'); ?>
		</div>
	<?php } ?>

	<div class="row">
		<?php echo $form->labelEx($model, 'name'); ?>
		<?php echo $form->textField($model, 'name', array("size" => 64)); ?>
		<?php echo $form->error($model, 'name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'skype'); ?>
		<?php echo $form->textField($model, 'skype', array("size" => 64)); ?>
		<?php echo $form->error($model, 'skype'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'phone'); ?>
		<?php echo $form->textField($model, 'phone', array("size" => 16)); ?>
		<?php echo $form->error($model, 'phone'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'city'); ?>
		<?php echo $form->textField($model, 'city', array("size" => 16)); ?>
		<?php echo $form->error($model, 'city'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'parent_id'); ?>
		<?php echo $form->textField($model, 'parent_id', array("size" => 11)); ?>
		<?php echo $form->error($model, 'parent_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'balance_personal'); ?>
		<?php echo $form->textField($model, 'balance_personal', array("size" => 4)); ?>
		<?php echo $form->error($model, 'balance_personal'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'balance_shop'); ?>
		<?php echo $form->textField($model, 'balance_shop', array("size" => 4)); ?>
		<?php echo $form->error($model, 'balance_shop'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить'); ?>
	</div>

	<?php $this->endWidget(); ?>

</div>