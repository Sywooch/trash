<?php
$list = $this->getItemList(); // Список врачей
$isShowMessageForBest = false; // Было ли показано сообщение о том что врчей найдено мало
?>
<div class="dd-widget dd-widget-list-container">
	<div class="dd-list-top-container">
		Специальность
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
				echo $this->getFoundNumText(['Найден', 'Найдено', 'Найдено'], ['врач', 'врача', 'врачей']);
				?>
			</div>
			<ul class="dd-list-header-filter">
				<li class="dd-list-header-filter-checkbox">
					<a href="#">
						<label>
							<?php
							echo $this->getAtHomeField();
							?>
							Выезд на дом
						</label>
					</a>
				</li>
				<li>Сортировка по</li>
				<li class="dd-list-header-filter-sort">
					<?php
					echo $this->getFilterLink('doctorRating', 'Рейтингу');
					?>
				</li>
				<li class="dd-list-header-filter-sort">
					<?php
					echo $this->getFilterLink('experience', 'Стажу');
					?>
				</li>
				<li class="dd-list-header-filter-sort">
					<?php
					echo $this->getFilterLink('price', 'Стоимости');
					?>
				</li>
			</ul>
		</div>
	<?php } ?>
	<?php foreach ($list as $d) { ?>
		<?php
		if (!$isShowMessageForBest && $d["isBest"]) {
			$isShowMessageForBest = true; ?>
			<div class="dd-list-header">
				<div class="dd-no-find-text">
					<?php echo $this->messageForBest; ?>
				</div>
				<div class="dd-list-header-found">
					Лучшие врачи
				</div>
			</div>
		<?php } ?>
		<div class="dd-list-card">
			<div class="dd-list-card-left">
				<a href="<?= $d['url'] ?>" class="dd-list-card-img-link">
					<img src="<?= $d['logo'] ?>"/>
				</a>

				<div class="dd-list-card-reviews-container">
					<a href="<?= $d['url'] ?>#reviews" class="dd-list-card-reviews-count">
						<span class="dd-list-card-reviews-counter"><?= $d['countReviews'] ?></span>
						&nbsp; <?php
						echo $d['reviewText'];
						?>
					</a>
				</div>
			</div>
			<div class="dd-list-card-info">
				<div class="dd-list-card-info-right">
					<div class="dd-list-card-info-right-rating">
						<div class="dd-list-card-info-right-rating-numbers">
							<span class="dd-list-card-info-right-rating-numbers-main"><?php echo (int)floor(
									$d['rating']
								) ?></span><span
								class="dd-list-card-info-right-rating-numbers-sub">.<?php echo
									($d['rating'] - floor($d['rating'])) *
									10; ?></span>
							/10
						</div>
						<div class="dd-list-card-info-right-rating-disclaimer">рейтинг</div>
					</div>
					<?= $this->getSignUpButton($d, 'Записаться онлайн') ?>
				</div>
				<div class="dd-list-card-info-left">
					<div class="dd-list-card-info-name">
						<a href="<?= $d['url'] ?>"><?= $d['name'] ?></a>
					</div>
					<div class="dd-list-card-info-specialty"><?php
						echo implode(", ", $d['sectors']);
						?></div>
					<?php

					if ($d['experience'] > 0) {
						echo
							'<div class="dd-list-card-info-experience">Стаж <strong>' .
							$d['experienceText'] .
							'</strong></div>';
					}

					?>
					<?php if ($this->isDistrict()) { ?>
						<div class="dd-district">
							<a href="<?php echo $d['clinic']['district']['url']; ?>">
								<?php echo $d['clinic']['district']['name']; ?> район
							</a>
						</div>
					<?php } else { ?>
						<ul class="dd-list-card-info-metro-list">
							<?php
							$sts = [];
							foreach ($d['clinic']['stations'] as $st) {
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
					<span class="dd-list-card-info-adress"><?php echo $d['clinic']['address']; ?></span>
				</div>
				<div class="dd-list-card-info-description">
					<?php echo $d['text']; ?>
				</div>
				<?php
				if (!empty($d['price'])) {
					echo
						'<div class="dd-list-card-info-price">Стоимость приема - <strong>' .
						$d['price'] .
						' р.</strong></div>';
				}
				?>

			</div>
		</div>
	<?php
	}
	echo $this->getPager(); ?>
</div>
