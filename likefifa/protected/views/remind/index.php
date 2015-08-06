<div class="content-wrap content-pad-bottom">
	<div class="det-line_sep" style="margin-top: 11px;"><h1>Восстановление пароля</h1></div>
	<p>Введите e-mail, который Вы указали при регистрации.</p>
	
		<?php $form=$this->beginWidget('CActiveForm', array(
			'id'=>'remind',
			'enableAjaxValidation'=>false,
			'htmlOptions' => array(),
		)); ?>
		<div style="width:237px;">
			<div style="margin-bottom:20px;"><?php echo LfHtml::activeTextField($model,'email', array('placeholder'=>'Ваш E-mail')); ?></div>
			<div id="remind-sbmt" class="button button-blue"><span>Отправить пароль</span></div>
			<div class="remind-submit"><?php echo CHtml::submitButton('Отправить пароль'); ?></div>
		</div>
		<?php echo $form->error($model,'email'); ?>
		<?php $this->endWidget(); ?>
	
</div>