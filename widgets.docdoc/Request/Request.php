<?php
/**
 * @var string                         $host
 * @var string                         $name
 * @var string                         $phone
 * @var \dfs\docdoc\models\SectorModel $sector
 */

?>
<div class="dd-widget-rs <?php echo $this->getContainerName(); ?>">
	<div class="dd-success-message">
		Спасибо, ваша заявка отправлена. <br/> Мы перезвоним вам в течение 15 минут и подберем врача
	</div>
	<div class="dd-error-message">
		В ходе отправления заявки произошла ошибка. <br/> Мы уже знаем о проблеме. Приносим свои извинения.
	</div>
	<form method="get" action="/" class="dd-request-form">
		<div class="dd-r">БЕСПЛАТНО подберем вам врача за 15 минут</div>

		<div class="dd-title">
			<?php
			$sector = $this->getSectorInfo();
			if ($sector) { ?>
				Ищете <?= mb_strtolower($sector['name_genitive'], 'UTF-8'); ?>?
			<?php } else { ?>
				Ищете врача?
			<?php } ?>
		</div>
		<div class="dd-input"><span class="dd-pic dd-bg-user"></span>
			<?php
				echo $this->getClientNameTextField();
			?>
		</div>
		<div class="dd-input"><span class="dd-pic dd-bg-phone"></span>
			<?php
				echo $this->getPhoneTextField();
			?>
		</div>
		<input class="dd-submit dd-button dd-size2" value="Подобрать" type="submit">

		<div class="dd-error-request dd-name-request-empty">
			Пожалуйста, введите Ваше имя
		</div>
		<div class="dd-error-request dd-phone-request-empty">
			Пожалуйста, введите Ваш телефон
		</div>
		<div class="dd-error-request dd-phone-request-incorrect">
			Пожалуйста, укажите корректный номер телефона
		</div>

		<div class="dd-small">мы перезвоним вам в течение 10 минут и порекомендуем врача</div>
	</form>
</div>