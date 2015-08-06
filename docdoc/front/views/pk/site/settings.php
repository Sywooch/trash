<?php
/**
 * @var dfs\docdoc\models\PartnerModel $partner
 */
?>

<div class="result_title__ct">
    <h1 class="result_main__title">Настройки</h1>
</div>

<div class="info_content">
	<div class="l-bubble">
		<span class="clinic_settings__row">
			<span class="strong">Имя: </span> <?php echo $partner->name; ?>
		</span>
		<span class="clinic_settings__row">
			<span class="strong">Номер мобильного телефона: </span> <?php echo $partner->contact_phone; ?>
		</span>
		<span class="clinic_settings__row">
			<span class="strong">Электронная почта: </span> <?php echo $partner->contact_email; ?>
		</span>
	</div>

	<form class="pasword_change__form l-bubble" method="POST" action="/pk/service/changePassword">
		<input class="pasword_change__input" name="login" type="hidden" value="<?php echo $partner->login; ?>" />

		Смена пароля:
		<div class="mvm">
			<label class="password_change__label">
				<span class="password_change__text">Введите текущий пароль:</span>
				<input class="pasword_change__input lk_input" name="currentPassword" type="password" />
			</label>
		</div>
		<div class="mvm">
			<label class="password_change__label">
				<span class="password_change__text">Введите новый пароль:</span>
				<input class="pasword_change__input lk_input" name="newPassword" type="password" />
			</label>
		</div>
		<div class="mvm">
			<label class="password_change__label">
				<span class="password_change__text">Введите новый пароль еще раз:</span>
				<input class="pasword_change__input lk_input" name="repeatPassword" type="password" />
			</label>
		</div>

		<div class="mvm">
			<label class="password_change__label">
				<span class="password_change__text">
					Отправить пароль на <?php echo $partner->contact_email; ?>
					<br/>
					(DocDoc.Ru не хранит пароли в открытом виде):
				</span>
				<input class="pasword_change__input lk_input" name="sendToEmail" type="checkbox" checked="true" />
			</label>
		</div>

		<input class="pasword_change__submit button_lk" type="submit" value="Поменять пароль" />
	</form>
</div>
