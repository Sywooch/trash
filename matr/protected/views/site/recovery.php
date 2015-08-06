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

	<div class="page-container page-container-lk">
		<h1>Восстановление пароля</h1>

		<?php if (!$success) { ?>
			<?php echo $form->textField(
				$model,
				'email',
				array("placeholder" => "Ваш электронный адрес", "class" => "login-form")
			); ?>
			<?php echo $form->error($model, 'email'); ?>

			<?php echo CHtml::submitButton('Восстановить', array("class" => "btn")); ?>
		<?php } else { ?>
			<div class="success">На указанный Вами электронный адрес выслан новый пароль.</div>
		<?php } ?>
	</div>

<?php $this->endWidget(); ?>