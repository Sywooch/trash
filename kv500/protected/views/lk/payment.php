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
			<?php echo $form->dropDownList($model, 'balanceAdd', $model->balanceList, array("class" => "select2")); ?>
			<?php echo $form->error($model, 'balanceAdd'); ?>
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
			<?php echo $form->dropDownList($model, 'balanceAdd', $model->balanceList, array("class" => "select2")); ?>
			<?php echo $form->error($model, 'balanceAdd'); ?>
		</div>

		<div>
			<?php echo CHtml::submitButton('Оплатить', array("class" => "btn")); ?>
		</div>

		<?php $this->endWidget(); ?>
	<?php } ?>




</div>