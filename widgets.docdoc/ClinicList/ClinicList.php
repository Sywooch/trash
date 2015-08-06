<div class="dd-widget-clinics <?php echo $this->getContainerName(); ?>">
	<div class="dd-title">
		<?php
		switch ($this->theme) {
			case "ClinicList/uzilab":
				echo "Самые популярные УЗИ-центры в Москве";
				break;
			case "ClinicList/uzilab2":
				echo "Самые популярные УЗИ-центры в Москве";
				break;
			default:
				echo "Клиники по данной проблеме";
				break;
		}
		?>
	</div>
	<div class="dd-recommended">
		Клиники рекомендованы сервисом по поиску врачей <a href="<?php echo $this->getDocDocUrl(); ?>">DocDoc.ru</a>
	</div>
	<?php foreach ($this->getItemList() as $c) { ?>
		<div class="dd-clinic">
			<div class="dd-img">
				<a rel="nofollow" href="<?= $c['url'] ?>">
					<img src="<?= $c['logo'] ?>" alt="<?= $c['name'] ?>">
				</a>
			</div>
			<div class="dd-r">
				<?= $this->getSignUpButton($c, 'Записаться') ?>
				<?php
				echo $c['phone'];
				?>
			</div>
			<div class="dd-name"><a rel="nofollow" href="<?= $c['url'] ?>"><?= $c['name'] ?></a></div>
			адрес: <?= $c['address'] ?><br>
			метро: <?php
			$sts = [];
			foreach ($c['stations'] as $st) {
				$sts[] = $st['name'];
			}
			echo implode(", ", $sts);
			?>
			<div class="dd-description"><?= $c['description'] ?></div>
		</div>
	<?php } ?>
	<?php
	switch ($this->theme) {
		case "ClinicList/uzilab":
			echo "<div class=\"dd-uzilab-footer\"><a href=\"http://uzilab.ru/kliniki\">Посмотреть все УЗИ-центры Москвы</a></div>";
			break;
	}
	?>
</div>