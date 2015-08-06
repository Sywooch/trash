<div class="dd-widget dd-widget-modal">
	<div class="dd-sign-up-popup" id="dd-sign-up-popup">
		<div class="dd-sign-up-popup-close" id="dd-sign-up-popup-close" title="Закрыть"></div>
		<div class="dd-sign-up-popup-title">Запись к врачу онлайн<span id="dd-partner-phone" style="display:none;"><br/>или по телефону <span id="dd-partner-phone-value"></span></span></div>
		<form method="get" action="" id="dd-sign-up-popup-form">
			<?php
				//@todo передавать врача и/или клинику
			?>
			<div class="dd-sign-up-popup-form-container">
				<div class="dd-sign-up-popup-form-label">Ваше имя: <span>*</span></div>
				<?php
				   echo $this->getNameTextField();
				?>
				<div class="dd-sign-up-popup-form-success" id="dd-success-name"></div>
				<div class="dd-sign-up-popup-form-error" id="dd-name-empty">Пожалуйста, введите Ваше имя</div>
			</div>
			<div class="dd-sign-up-popup-form-container">
				<div class="dd-sign-up-popup-form-label">Ваш телефон: <span>*</span></div>
				<?php
					echo $this->getPhoneTextField();
				?>
				<div class="dd-sign-up-popup-form-success" id="dd-success-phone"></div>
				<div class="dd-sign-up-popup-form-error" id="dd-phone-empty">
					Пожалуйста, введите Ваш телефон
				</div>
				<div class="dd-sign-up-popup-form-error" id="dd-phone-empty">
					Пожалуйста, введите Ваш телефон
				</div>
				<div class="dd-sign-up-popup-form-error" id="dd-phone-incorrect">
					Пожалуйста, укажите корректный номер телефона
				</div>
			</div>
			<div class="dd-sign-up-popup-under-form-description">
				Оставьте заявку и мы подберем вам врача за 15 минут
			</div>
			<div class="dd-sign-up-popup-submit-button-container">
				<button class="dd-button dd-submit" id="dd-submit">
					<span>Записаться</span>
				</button>
			</div>
		</form>
	</div>
	<div class="dd-sign-up-popup-success" id="dd-sign-up-popup-success">
		<div class="dd-sign-up-popup-title">Заявка отправлена</div>
		<div class="dd-sign-up-popup-success-text">
			Наши консультанты свяжутся с Вами в течение 15 минут (ежедневно с 9:00 до 21:00) и запишут Вас на прием.
		</div>
		<div class="dd-sign-up-popup-submit-button-container">
			<button class="dd-button" id="dd-submit-success">
				<span>Ок</span>
			</button>
		</div>
	</div>
	<div class="dd-sign-up-popup-error" id="dd-sign-up-popup-error">
		<div class="dd-sign-up-popup-title">Ошибка</div>
		<div class="dd-sign-up-popup-error-text">
			В ходе отправления заявки произошла ошибка. <br/> Мы уже знаем о проблеме. Приносим свои извинения.
		</div>
		<div class="dd-sign-up-popup-submit-button-container">
			<button class="dd-button" id="dd-submit-error">
				<span>Ок</span>
			</button>
		</div>
	</div>
	<div class="dd-sign-up-overlay" id="dd-sign-up-overlay"></div>
</div>