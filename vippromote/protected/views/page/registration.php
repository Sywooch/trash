<div class="page-container">
	<h1>Регистрация</h1>
	<?php $form=$this->beginWidget('CActiveForm', array(
			'id'=>'registration-form',
			'enableClientValidation'=>true,
			'clientOptions'=>array(
				'validateOnSubmit'=>true,
			),
		)); ?>
	<div id="registration-container">
		<div>
			<?php echo $form->textField($model, 'name', array("placeholder" => "Ваше имя")); ?>
			<?php echo $form->error($model, 'name'); ?>
		</div>
		<div>
			<?php echo $form->textField($model, 'email', array("placeholder" => "Ваш электронный адрес")); ?>
			<?php echo $form->error($model, 'email'); ?>
		</div>
		<div>
			<?php echo $form->textField($model, 'skype', array("placeholder" => "Skype")); ?>
			<?php echo $form->error($model, 'skype'); ?>
		</div>

	</div>
	<?php echo CHtml::submitButton('Регистрация', array("class" => "btn")); ?>
	<?php $this->endWidget(); ?>
</div>