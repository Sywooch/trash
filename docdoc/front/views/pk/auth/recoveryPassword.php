<?php
$emailSupport = Yii::app()->params['email']['support'];
$user = Yii::app()->user;
?>

<div class="reg_form__holder">

	<form id="lk-login-form" class="reg_form" method="post" action="/pk/service/recovery">
		<h1 class="reg_form__title">Восстановление пароля</h1>

		<label class="required" for="LKLoginForm_email"></label>
		<input id="LKLoginForm_email"
			   class="reg_form__input required"
			   type="text"
			   name="email"
			   placeholder="введите email"
			   autofocus="autofocus"
			   autocomplete="off"/>

		<?php if ($user->hasFlash('error')): ?>
			<div class="reg_form__errors">
				<p><?php echo $user->getFlash('error'); ?></p>
			</div>
		<?php endif; ?>

		<div class="button-place">
			<input class="button_lk" type="submit" value="Восстановить" />
		</div>
	</form>

	<div class="reg_form__info">
		<p class="reg_form__desc">
			Если у вас возникли вопросы, связанные с авторизацией, свяжитесь с нами:
			<br /><br />
			<strong class="reg_form__contact">
				email: <a class="reg_form__link" href="mailto:<?php echo $emailSupport; ?>"><?php echo $emailSupport; ?></a>
				<br/>
				тел.: <?php echo GeneralPhone; ?>
				<br />
			</strong>
		</p>
	</div>

</div>
