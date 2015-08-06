<?php $this->renderPartial("_header", compact("model")); ?>


<div class="lk-contacts">
	<?php $form = $this->beginWidget(
		'CActiveForm',
		array(
			'id' => 'registration-form',
			'enableClientValidation' => true,
			'clientOptions' => array(
				'validateOnSubmit' => true,
			),
		)
	); ?>

	<div class="title">Получить деньги</div>

	<div>
		<p><br/>Укажите удобный для вас способ получения денег:</p>
		<?php echo $form->dropDownList(
			$paymentMoney,
			'text',
			array(
				"perfect: {$model->perfect}" => "Кошелек PerfectMoney: {$model->perfect}",
				"payer: {$model->payer}"     => "Кошелек Payeer: {$model->payer}"
			),
			array("class" => "select2")
		); ?>
		<p>Или заполните другие реквизиты:</p>
		<?php echo CHtml::textArea(
			'text2',
			null,
			array("class" => "contacts-form", "style" => "height: 100px;")
		); ?>

		<?php echo $form->error($paymentMoney, 'text'); ?>
		<p><br/>Если оба кошелька не заполнены, заполните их в <a href="/lk/contacts/">контактной информации</a></p>
	</div>

	<div>
		<?php echo CHtml::submitButton('Отправить запрос', array("class" => "btn")); ?>
	</div>

	<?php $this->endWidget(); ?>
</div>