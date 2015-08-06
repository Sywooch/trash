<?php
/**
 * @var dfs\docdoc\models\ClinicAdminModel $admin
 */
?>

<div class="result_title__ct">
	<h1 class="result_main__title">Настройки</h1>
</div>

<div class="info_content">
	<div class="l-bubble">
		<span class="clinic_settings__row">
			<span class="strong">Имя: </span> <?php echo $admin->getFullName(); ?>
		</span>
		<span class="clinic_settings__row">
			<span class="strong">Номер мобильного телефона: </span> <?php echo $admin->cell_phone; ?>
		</span>
		<span class="clinic_settings__row">
			<span class="strong">Номер телефона: </span> <?php echo $admin->phone; ?>
		</span>
		<span class="clinic_settings__row">
			<span class="strong">Электронная почта: </span> <?php echo $admin->email; ?>
		</span>
	</div>

	<form class="pasword_change__form l-bubble" method="POST" action="/lk/service/changePassword">
		<input class="pasword_change__input" name="login" type="hidden" value="<?php echo $admin->clinic_admin_id; ?>" />

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

		<input class="pasword_change__submit button_lk" type="submit" value="Поменять пароль" />
	</form>
</div>
