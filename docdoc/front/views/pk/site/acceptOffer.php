<?php
$emailSupport = Yii::app()->params['email']['support'];
$user = Yii::app()->user;
?>

<div class="reg_form__holder accept-offer-container">
	<form id="lk-login-form" class="reg_form" method="post" action="/pk/service/acceptOffer">
		<h3 class="reg_form__title" style="font-weight: normal; font-size: 16px; line-height: 16px;">
			Пожалуйста, примите условия сотрудничества <br> для партнеров
		</h3>

		<?php if ($user->hasFlash('error')): ?>
			<div class="reg_form__errors">
				<p><?php echo $user->getFlash('error'); ?></p>
			</div>
		<?php endif; ?>

		<div class="reg_form__rememberme">
			<input name="rememberMe" id="LKLoginForm_rememberMe" type="checkbox" checked="true"/>
			<label for="LKLoginForm_rememberMe">
				Я согласен с <a href="/static/docs/docdoc_partner_offer.pdf" target="_blank">условиями сотрудничества для партнеров</a>
			</label>
		</div>

		<input class="button_lk" type="submit" value="Принять" />
	</form>

	<div class="reg_form__info">

	</div>
</div>
