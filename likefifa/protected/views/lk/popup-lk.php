<div id="overlay" class="overlay-lk" style='display: block;'></div>
<div id="popup" class="popup-appointment popup-appointment-lk" master-id="<?php echo $model->id; ?>">
	<div class="popup-app_head">
		<span>Ура! Вы зарегистрированы!</span>
		<div class="popup-close png"></div>
	</div>
	<div class="popup-app_cont">
		<div class='popup-appointment-lk-text'>
			Мы благодарим Вас за регистрацию на нашем портале и дарим Вам подарок -
				<?php echo Yii::app()->params["bonusMaster"]; ?> руб. на счет.
			<br/>После заполнения Ваша анкета отправится на модерацию и будет опубликована в течение 24 часов.
				Так же, просим Вас, внимательно ознакомиться с правилами сотрудничества.
		</div>
	</div>
</div>

<div id="popup2" class="popup-success popup-success-lk" style="display: none;">
	<div class="popup-close"></div>
	<div class="popup-success_head">Подарок принят!</div>
	<div class="popup-success_txt">LikeFifa.ru благодарит Вас за сотрудничество и дарит подарок -
		<?php echo Yii::app()->params["bonusMaster"]; ?> рублей на Ваш счет</div>
	<div class="popup-success_thx">LikeFifa</div>
</div>

<div class='contract-window-overlay'></div>
<div class='contract-window'>
	<div class='close'></div>
	<div class='content'>
		<?php echo file_get_contents(Yii::getPathOfAlias('webroot.protected.data') . '/contract.html'); ?>
	</div>
</div>