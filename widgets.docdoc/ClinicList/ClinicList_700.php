<?php
$list = $this->getItemList(); // Список клиник
$isShowMessageForBest = false; // Было ли показано сообщение о том что клиник найдено мало
?>
<div class="dd-widget dd-widget-list-container">
	<div class="dd-list-top-container">
		Направление
		<div class="dd-list-select-container">
			<?php
			echo $this->getSectorListField();
			?>
		</div>
		в районе
		<div class="dd-list-select-container">
			<?php echo $this->isDistrict() ? $this->getDistrictListField() : $this->getStationListField(); ?>
		</div>
	</div>

	<?php if (!empty($list[0]) && !$list[0]["isBest"]) { ?>
		<div class="dd-list-header">
			<div class="dd-list-header-found">
				<?php
				echo $this->getFoundNumText(
					['Найдена', 'Найдено', 'Найдено'],
					['клиника и центр', 'клиники и центра', 'клиник и центров']
				);
				?>
			</div>
		</div>
	<?php } ?>

	<?php foreach ($list as $c) { ?>
		<?php
		if (!$isShowMessageForBest && $c["isBest"]) {
			$isShowMessageForBest = true; ?>
			<div class="dd-list-header">
				<div class="dd-no-find-text">
					<?php echo $this->messageForBest; ?>
				</div>
				<div class="dd-list-header-found">
					Лучшие клиники
				</div>
			</div>
		<?php } ?>
		<div class="dd-list-card">
			<div class="dd-list-card-left">
				<a href="<?= $c['url'] ?>"
				   class="dd-list-card-img-link">
					<img src="<?= $c['logo'] ?>"/>
				</a>
			</div>
			<div class="dd-list-card-info">
				<div class="dd-list-card-info-right dd-list-card-info-right-clinic">
					<div class="dd-list-card-info-right-rating">
						<div class="dd-list-card-info-right-rating-numbers">
							<span class="dd-list-card-info-right-rating-numbers-main"><?php echo (int)floor(
									$c['rating']
								) ?></span><span
								class="dd-list-card-info-right-rating-numbers-sub">.<?php echo
									($c['rating'] - floor($c['rating'])) *
									10; ?></span>
							/10
						</div>
						<div class="dd-list-card-info-right-rating-disclaimer">рейтинг</div>
					</div>
					<?= $this->getSignUpButton($c, 'Записаться онлайн') ?>
					<dl class="dd-list-card-info-right-schedule">
						<label>График работы центра:</label>
						<?php foreach ($c['schedule'] as $v) { ?>
							<dt class="dd-list-card-info-right-schedule-days"><?= $v['DayTitle'] ?>:</dt>
							<dd class="dd-list-card-info-right-schedule-time"><?= $v['StartTime'] ?>
								- <?= $v['EndTime'] ?></dd>
						<?php } ?>
					</dl>
				</div>
				<div class="dd-list-card-info-left">
					<div class="dd-list-card-info-name">
						<a href="<?= $c['url'] ?>"><?= $c['name'] ?></a>
					</div>
					<div class="dd-list-card-info-clinic-specialty">Многопрофильный медицинский центр</div>
					<?php if ($this->isDistrict()) { ?>
						<div class="dd-district">
							<a href="<?php echo $c['district']['url']; ?>">
								<?php echo $c['district']['name']; ?> район
							</a>
						</div>
					<?php } else { ?>
						<ul class="dd-list-card-info-metro-list">
							<?php
							$sts = [];
							foreach ($c['stations'] as $st) {
								$sts[] =
									'<li><a href="' .
									$st['url'] .
									'" class="dd-metro-line dd-metro-line-' .
									$st['lineId'] .
									'">' .
									$st['name'] .
									'</a></li>';
							}

							echo implode(", ", $sts);
							?>
						</ul>
					<?php } ?>
					<span class="dd-list-card-info-adress"><?= $c['address'] ?></span>

					<div class="dd-list-card-info-description"><?= $c['description'] ?></div>
					<?php

					if (!is_null($c['price'])) {
						?>
						<div class="dd-list-card-info-price">Стоимость приема - <strong><?= $c['price'] ?>
								р.</strong></div>
					<?php } ?>
				</div>
			</div>
		</div>
	<?php } ?>
	<?php
	echo $this->getPager();
	?>
</div>
