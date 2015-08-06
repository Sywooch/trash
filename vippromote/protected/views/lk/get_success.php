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


	<p><br/>Ваш запрос принят! Мы свяжемся с Вами в ближайшее время. Проверьте, что вся необходимая <a href="/lk/contacts/">контактная информация</a> заполнена.<br/></p>


	<?php $this->endWidget(); ?>
</div>