<?php $this->renderPartial("_header", compact("model")); ?>


<div class="lk-contacts">
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'registration-form',
		'enableClientValidation'=>true,
		'clientOptions'=>array(
			'validateOnSubmit'=>true,
		),
	)); ?>

	<div class="title">Контактная информация</div>

	<div>
		<?php echo $form->textField($model,'name', array("class" => "contacts-form", "placeholder" => "Ваше имя")); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>

	<div>
		<?php echo $form->textField($model, 'skype', array("class" => "contacts-form", "placeholder" => "Skype")); ?>
		<?php echo $form->error($model, 'skype'); ?>
	</div>

	<div>
		<?php echo $form->textField($model, 'phone', array("class" => "contacts-form", "placeholder" => "Телефон")); ?>
		<?php echo $form->error($model, 'phone'); ?>
	</div>

	<div>
		<?php echo $form->textField($model, 'city', array("class" => "contacts-form", "placeholder" => "Город")); ?>
		<?php echo $form->error($model, 'city'); ?>
	</div>

	<div>
		<?php echo $form->textField($model, 'perfect', array("class" => "contacts-form", "placeholder" => "Кошелек PerfectMoney")); ?>
		<?php echo $form->error($model, 'perfect'); ?>
	</div>

	<div>
		<?php echo $form->textField($model, 'payer', array("class" => "contacts-form", "placeholder" => "Кошелек Payeer")); ?>
		<?php echo $form->error($model, 'payer'); ?>
	</div>

	<div>
		<?php echo CHtml::submitButton('Редактировать', array("class" => "btn")); ?>
	</div>

	<?php $this->endWidget(); ?>
</div>