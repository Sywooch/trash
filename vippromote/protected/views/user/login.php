<?php
/**
 * @var User $model
 */
?>

<div id="login-form-container">
	<div class="login-window">
		<div class="title">
			Вход
		</div>
		<a href="#" class="close"></a>
		<div class="forms">
			<form action="/user/checkLogin/" method="post">

			<div class="text-form">
				<?php
				echo CHtml::activeTextField($model, "email", array("class" => "text-field", "placeHolder" => "Электронная почта"));
				?>
				<div class="error error-email-empty">
					Электронная почта не может быть пустой
				</div>
				<div class="error error-email-not-exist">
					Пользователя с такой электронной почтой не существует
				</div>
			</div>

			<div class="text-form">
				<?php
				echo CHtml::activePasswordField($model, "password", array("class" => "text-field", "placeHolder" => "Пароль"));
				?>
				<div class="error error-password-empty">
					Пароль не может быть пустым
				</div>
				<div class="error error-password-wrong">
					Неверный пароль
				</div>
			</div>

			<div class="checkbox-form">
				<?php
				echo CHtml::activeCheckbox($model, "remember");
				echo CHtml::activeLabel($model, "remember");
				?>
			</div>

			<?php
			echo CHtml::ajaxSubmitButton(
				"Войти",
				"/user/checkLogin/",
				array(
					"type"       => "POST",
					"success"    => 'function(data) {
						if (data) {
							$(".login-window .error-" + data).show();
						} else {
							window.location.replace("/lk/");
						}
					}'
				),
				array(
					"class" => "button",
					"id"    => uniqid(),
					"live"  => false,
				)
			);
			?>
				<a href="/recovery" class="login-recovery">Восстановление пароля</a>

			</form>

			<?php
			Yii::app()->clientScript->registerScript(
				"login-window",
				'
						$(".login-window input").on("keyup", function() {
							$(".login-window .error").hide();
						});
					'
			);
			?>
		</div>
	</div>
	<div class="login-overlay"></div>
</div>