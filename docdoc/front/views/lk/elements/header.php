<?php
use dfs\docdoc\helpers\BrowserHelper;

/**
 * @var \dfs\docdoc\front\controllers\lk\FrontController $this
 */
?>

<header class="header">
	<?php if (!BrowserHelper::browserIsSupported()): ?>
		<div class="warning" style="border: dashed 1px black;background-color: yellow; padding:10px; margin-bottom:10px;">
			Внимание! Вы используете устаревший или не поддерживаемый браузер.
			В связи с этим часть данных может быть не отображена или отображена в искаженном виде.
			Для правильного отображения всех данных установите бразуер Google Chrome доступный по ссылке
			<a href="http://www.google.ru/chrome/" target="blank">http://www.google.ru/chrome/</a>, и откройте в нем личный кабинет.
		</div>
	<?php endif; ?>

	<div class="header_clinic">
		<?php if ($this->_clinic): ?>

			<?php
				echo $this->renderPartial('/elements/changeClinic', [
					'current' => $this->_clinic,
					'clinics' => $this->getAdminClinicList(),
				]);
			?>

			<p class="header_clinic__employees">
            	<span class="strong">Врачи: <?php echo $this->getDoctorsCount(); ?></span>
			</p>

		<?php endif; ?>
	</div>

	<a class="logo" href="/lk">
		<img class="logo_img" src="/i/logo-lk.png" alt="Docdoc.ru" />
	</a>
</header>
