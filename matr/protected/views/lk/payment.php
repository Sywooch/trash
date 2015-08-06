<?php $this->renderPartial("_header", compact("model")); ?>

<div class="lk-payment">
	<?php $form = $this->beginWidget(
		'CActiveForm',
		array(
			'id'                     => 'payment-form',
			'enableClientValidation' => true,
			'clientOptions'          => array(
				'validateOnSubmit' => true,
			),
		)
	); ?>
	<div>Чтобы активировать аккаунт, необходимо купить что-нибудь</div>
	<div>
		<br/>Выберите, что купить: <br/>
		<?php echo CHtml::dropDownList(
			"type",
			Yii::app()->request->getQuery("type"),
			array(
				0 => "Матрешка \"Россияночка\" 7 кукольная - 1500р",
				1 => "Матрешка \"Россияночка\" 15 кукольная - 3500р",
				2 => "Сувенирная продукция магазина - 5500р"
			),
			array("class" => "select2")
		); ?>
	</div>

	<?php if (IS_PAYMENT) { ?>
			<div>
			<?php echo $form->hiddenField($model, 'balanceAdd', array("value" => 100)); ?>
		</div>

		<?php echo CHtml::hiddenField("User[payTo]", 2); ?>

		<div><br>
			Для оплаты вы можете использовать активационный код, если он имеется: <br>
			<input type="text" name="activation_code" class="contacts-form" />
		</div>

		<div>
			<?php echo CHtml::submitButton('Далее', array("class" => "next")); ?>
		</div>

		<?php $this->endWidget(); ?>
	<?php } ?>

	<?php if (!IS_PAYMENT) { ?>


		<div>
			<?php echo $form->hiddenField($model, 'balanceAdd', array("value" => 100)); ?>
			<?php echo $form->error($model, 'balanceAdd'); ?>
		</div>

		<div>
			<?php echo CHtml::submitButton('Оплатить', array("class" => "btn")); ?>
		</div>

		<?php $this->endWidget(); ?>
	<?php } ?>




</div>