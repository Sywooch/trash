<?php
$emailAccount = Yii::app()->params['email']['account'];
$user = Yii::app()->user;
?>

<div class="reg_form__holder">
	<form id="lk-login-form" class="reg_form" method="post" action="/lk/service/login">
		<h1 class="reg_form__title">Вход в личный кабинет</h1>

		<label class="required" for="LKLoginForm_email"></label>
		<input id="LKLoginForm_email"
			   class="reg_form__input required"
			   type="text"
			   name="login"
			   placeholder="введите email"
			   value="<?php // {authInfo/Login} ?>"
			   autofocus="autofocus"
			   autocomplete="off"/>

		<label class="required" for="LKLoginForm_password"></label>
		<input id="LKLoginForm_password" class="reg_form__input required" type="password" name="password" autocomplete="off" placeholder="введите пароль" />

		<p><a class="reg_form__link" href="/lk/recoveryPassword">Забыли пароль?</a></p>

		<?php if ($user->hasFlash('success')): ?>
			<div class="reg_form__success">
				<p><?php echo $user->getFlash('success'); ?></p>
			</div>
		<?php endif; ?>

		<?php if ($user->hasFlash('error')): ?>
			<div class="reg_form__errors">
				<p><?php echo $user->getFlash('error'); ?></p>
			</div>
		<?php endif; ?>

		<div class="reg_form__rememberme">
			<input name="rememberMe" id="LKLoginForm_rememberMe" type="checkbox" />
			<label for="LKLoginForm_rememberMe">Запомнить меня</label>
		</div>
		<input class="button_lk" type="submit" value="Войти" />
	</form>

	<div class="reg_form__info">
		<p class="reg_form__desc">
			Если у вас возникли вопросы, связанные с авторизацией, свяжитесь с нами:<br /><br />
			<strong class="reg_form__contact">
				email: <a class="reg_form__link" href="mailto:<?php echo $emailAccount; ?>"><?php echo $emailAccount; ?></a>
				<br/>
				тел.: <?php echo Yii::app()->city->getSiteOffice()->prettyFormat(); ?>
				<br />
			</strong>
		</p>
	</div>
</div>
