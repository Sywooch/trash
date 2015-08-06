<p class="mvn js-request-success">
	Ваша заявка о записи на прием к врачу отправлена. Наши консультанты свяжутся с вами в течение 15 минут ежедневно с
	9:00 до 21:00 и запишут Вас на прием.
</p>
<div class="request-email-container js-request-success">
	<div class="subscription">
		Подпишитесь и получайте спец-предложения от портала Docdoc.ru
		<span>(например, скидка на комплексное лечение)</span>
	</div>
	<div class="email-container">
		<div class="label">Ваш e-mail:</div>
		<div class="form-container">
			<button id="requestClientButton" class="ui-btn ui-btn_teal">Отправить</button>
			<?php echo CHtml::textField(
				"requestClientEmail",
				null,
				array(
					"class"       => "req_form_input",
					"placeholder" => "Введите Ваш e-mail"
				)
			); ?>
			<div class="error client-email-error-empty">Поле e-mail не может быть пустым</div>
			<div class="error client-email-error-not-correctly">Поле e-mail заполнено некорректно</div>
		</div>
	</div>
</div>