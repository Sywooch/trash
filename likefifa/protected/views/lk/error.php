<?php
/**
 * @var LkController $this
 */
?>
<div class="content-wrap content-pad-bottom">
	<div class="det-line_sep" style="margin-top: 11px;"><h1>Вход в личный кабинет</h1></div>
	<div class="top-10-message">
		<strong style='line-height: 30px;'>
			<em>Для перехода по ссылке введите логин и пароль</em>
		</strong>
	</div>
	<br>
	<br>

	<div style="width:237px;">
		<?php
		/** @var CActiveForm $form */
		$form = $this->beginWidget(
			'CActiveForm',
			array(
				'action'               => $this->createUrl(
						'landing/index',
						array('#' => 'footer-registration')
					),
				'enableAjaxValidation' => false,
				'htmlOptions'          => array(),
			)
		); ?>
		<input type="hidden" name="action" value="login"/>

		<div style="margin-bottom:20px;">
			<div class="form-inp">
				<?php echo $form->textField($this->masterLoginForm, 'email', ['placeholder' => 'Ваш E-mail']) ?>
			</div>
		</div>
		<div style="margin-bottom:20px;">
			<div class="form-inp">
				<?php echo $form->passwordField($this->masterLoginForm, 'password', ['placeholder' => 'Ваш пароль']) ?>
			</div>
		</div>

		<div class="button button-blue">
			<span>Войти</span>
			<input type="submit" value="Войти" name=""><?php // echo CHtml::submitButton('Войти'); ?>
		</div>
		<?php $this->endWidget(); ?>
	</div>

</div>
