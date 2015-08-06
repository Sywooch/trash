<?php $this->renderPartial("_header", compact("model")); ?>


<div class="lk-contacts">
	<?php $form = $this->beginWidget(
		'CActiveForm',
		array(
			'id'                     => 'registration-form',
			'enableClientValidation' => true,
			'clientOptions'          => array(
				'validateOnSubmit' => true,
			),
		)
	); ?>

	<div class="title">Получить деньги</div>

		<div>
			<p><br/>Укажите все необходимые реквизиты для перевода денег:</p>
			<?php echo $form->textArea($paymentMoney, 'text', array("class" => "contacts-form contacts-text-form")); ?>
			<?php echo $form->error($paymentMoney, 'text'); ?>
		</div>

		<div>
			<?php echo CHtml::submitButton('Отправить запрос', array("class" => "btn")); ?>
		</div>

	<?php $this->endWidget(); ?>
</div>