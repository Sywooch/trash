<?php $this->renderPartial("/common/_header"); ?>

<div id="registration">
	<h1>Регистрация</h1>


	<?php $form=$this->beginWidget('CActiveForm', array(
			'id'=>'lf-master-form',
			'enableAjaxValidation'=>false,
			'htmlOptions' => array(
				'enctype' => 'multipart/form-data',
			),
		));

	?>

	<div class="field">
		<div class="label"><?php echo $form->labelEx($model,'name'); ?></div>
		<?php echo $form->textField($model,'name'); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>

	<div class="field">
		<div class="label"><?php echo $form->labelEx($model,'phone'); ?></div>
		<?php echo $form->textField($model,'phone'); ?>
		<?php echo $form->error($model,'phone'); ?>
	</div>

	<div class="field">
		<div class="label"><?php echo $form->labelEx($model,'email'); ?></div>
		<?php echo $form->textField($model,'email'); ?>
		<?php echo $form->error($model,'email'); ?>
	</div>

	<div class="row buttons button-blue master-form-button">
		<?php echo CHtml::submitButton("Зарегистрироваться"); ?>
	</div>

	<?php $this->endWidget(); ?>
</div>
