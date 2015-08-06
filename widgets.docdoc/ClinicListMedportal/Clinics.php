<?php foreach ($this->getItemList() as $c) { ?>

<div class="dd-clinic2">
	<div class="dd-clinic2__logo">
		<a rel="nofollow" href="<?= $c['url'] ?>">
			<img src="<?= $c['logo'] ?>" width="150" alt="">
		</a>
	</div>
	<div class="dd-clinic2__actions">
		<?=$this->getSignUpButton($c, 'Записаться')?>
		<div class="dd-clinic2__phone">
			<?php
			echo $c['phone'];
			?>
		</div>
	</div>
	<div class="dd-clinic2__idx">
		<div class="dd-clinic2__title">
			<a rel="nofollow" href="<?= $c['url'] ?>"><?= $c['name'] ?></a>
		</div>
		<div class="dd-clinic2__address">
			<p>адрес: <?=$c['address'] ?></p>
			<p>метро:
				<?php
				$sts = [];
				foreach ($c['stations'] as $st) {
					$sts[] = $st['name'];
				}

				echo implode(", ", $sts);

				?></p>
		</div>
		<div class="dd-clinic2__descr">
			<p><?= $c['description'] ?></p>
		</div>
	</div>
</div>

<?php } ?>