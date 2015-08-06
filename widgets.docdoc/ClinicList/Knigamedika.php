<div class="dd-widget dd-widget-clinics <?php echo $this->getContainerName(); ?>">
		<div class="dd-title">Клиники по этой проблеме</div>
	<?php foreach ($this->getItemList() as $c) { ?>
		
		<div class="dd-clinic">
			<div class="dd-img">
			<a rel="nofollow" href="<?= $c['url'] ?>">
				<img src="<?= $c['logo'] ?>" width="149" height="55" alt=" ">
			</a>	
			</div>
			<div class="dd-r">
			<?=$this->getSignUpButton($c, 'Записаться')?> 
			<?php
				echo $c['phone'];
			?>
			</div>
			<div class="dd-name"><a rel="nofollow" href="<?= $c['url'] ?>"><?= $c['name'] ?></a></div>
			адрес: <?=$c['address'] ?><br>
			метро: <?php
				$sts = [];
				foreach ($c['stations'] as $st) {
					$sts[] = $st['name'];
				}

				echo implode(", ", $sts);

				?><br>
			<?= $c['description'] ?>
		</div>
	<?php } ?>		
</div>