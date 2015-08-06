<?php $this->renderPartial("_header", compact("model")); ?>

<div class="lk-payment">
	<div>Чтобы продлить аккаунт, необходимо произвести оплату</div>

	<?php if (IS_PAYMENT) { ?>
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

		<div>
			<?php echo $form->hiddenField($model, 'balanceAdd', array("value" => 100)); ?>
		</div>

		<div>
			<br/>Выберите, куда перечислить деньги: <br/>
			<?php echo $form->dropDownList($model, 'payTo', array(2 => "Payeer", 3 => "Банковская карта VISA/MasterCard и прочее"), array("class" => "select2")); ?>
		</div>

		<div>
			или ввести активационный код <br>
			<input type="text" name="activation_code" class="contacts-form" />
		</div>

		<div>
			<?php echo CHtml::submitButton('Далее', array("class" => "next")); ?>
		</div>

		<?php $this->endWidget(); ?>
	<?php } ?>

	<?php if (!IS_PAYMENT) { ?>
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