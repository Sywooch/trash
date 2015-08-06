<div class="page-container">
	<h1>Контактная информация</h1>

	<?php echo Text::model()->findByPk(4)->text; ?>

	<?php if ($isSend) { ?>
		Ваше сообщение успешно отправлено!
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
			<?php echo $form->textField($model,'phone', array("placeholder" => "Телефон", "class" => "fb-form")); ?>
			<?php echo $form->error($model,'phone'); ?>
		</div>
		<div>
			<?php echo $form->textArea($model,'text', array("placeholder" => "Сообщение", "class" => "fb-form fb-text")); ?>
			<?php echo $form->error($model,'text'); ?>
		</div>

		<div><?php echo CHtml::submitButton('Отправить', array("class" => "btn")); ?></div>

		<?php $this->endWidget(); ?>

	<?php } ?>

</div>