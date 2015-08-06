<div class="page-container">
	<h1>Ваши предложения</h1>

	<?php if ($isSend) { ?>
		Спасибо! Ваше предложение успешно отправлено!
	<?php } else { ?>
	<?php $form=$this->beginWidget('CActiveForm', array(
			'id'=>'registration-form',
			'enableClientValidation'=>true,
			'clientOptions'=>array(
				'validateOnSubmit'=>true,
			),
		)); ?>

	<div>
		<?php echo $form->textField($model,'name', array("placeholder" => "Как вас зовут", "class" => "fb-form")); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>
	<div>
		<?php echo $form->textField($model,'email', array("placeholder" => "Электронная почта", "class" => "fb-form")); ?>
		<?php echo $form->error($model,'email'); ?>
	</div>
	<div>
		<?php echo $form->textArea($model,'text', array("placeholder" => "Предложение", "class" => "fb-form fb-text")); ?>
		<?php echo $form->error($model,'text'); ?>
	</div>

	<div><?php echo CHtml::submitButton('Отправить', array("class" => "btn")); ?></div>

	<?php $this->endWidget(); ?>

	<?php } ?>

</div>