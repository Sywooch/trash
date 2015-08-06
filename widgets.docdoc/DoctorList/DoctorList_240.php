<div class="dd-widget dd-widget-list-240-container <?php echo $this->getContainerName(); ?>">
	<div class="dd-widget-list-240-title"><?php echo $this->getDoctorInSectorTitle(); ?></div>

	<?php

	foreach ($this->getItemList() as $d) {

		?>
		<div class="dd-widget-list-240-card">
			<div class="dd-widget-list-240-card-photo">
				<a href="<?=$d['url'] ?>">
					<img src="<?= $d['logo'] ?>"/>
				</a>
			</div>
			<div class="dd-widget-list-240-card-info">
				<div class="dd-widget-list-240-card-info-name">
					<a href="<?=$d['url'] ?>"><?= $d['name'] ?></a>
				</div>
				<div class="dd-widget-list-240-card-info-details">
					<div class="dd-widget-list-240-card-info-details-reviews">
						<a href="<?=$d['url'] ?>#reviews">
							<span><?= $d['countReviews'] ?></span> <br>
							<?php echo $d['reviewText']; ?>
						</a>
					</div>
					<div class="dd-widget-list-240-card-info-details-rating">
						<a href="<?=$d['url'] ?>">
						<span><?php echo (int)floor($d['rating']) ?><small>.<?php echo
									($d['rating'] - floor($d['rating'])) *
									10; ?></small></span> <br>
							рейтинг
						</a>
					</div>
				</div>
				<?=$this->getSignUpButton($d, 'Записаться')?>
			</div>
		</div>
	<?php } ?>

	<div class="dd-widget-list-240-card-more">
		<a href="<?= $this->getDoctorInSectorUrl()?>">Посмотреть всех врачей</a>
	</div>
</div>
