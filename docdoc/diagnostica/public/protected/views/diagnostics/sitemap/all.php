<h1>Карта сайта</h1>

<ul class="sitemap">
<li>
	&ndash; <a href="/kliniki"><strong>Все виды диагностики</strong></a>
<ul>
	<li>
		&ndash; <strong>В районе:</strong>
		<ul>
			<?php foreach ($districts as $district) {?>
				<li>&ndash; <a href="/district/<?php echo $district->rewrite_name;?>"><?php echo $district->name;?></a></li>
			<?php }?>
		</ul>
		<?php if(!empty($stations)) {?>
			&ndash; <strong>На станции метро:</strong>
			<ul>
				<?php foreach ($stations as $station) { ?>
					<li>&ndash; <a href="/station/<?php echo $station->rewrite_name;?>"><?php echo $station['name']; ?></a></li>
				<?php } ?>
			</ul>
			<?php }?>
		<?php if(!empty($regCities)) {?>
			&ndash; <strong>В городе Подмосковья:</strong>
			<ul>
				<?php foreach ($regCities as $regCity) { ?>
					<li>&ndash; <a href="/city/<?php echo $regCity->rewrite_name;?>"><?php echo $regCity['name']; ?></a></li>
				<?php } ?>
			</ul>
		<?php }?>
	</li>
</ul>
</li>

</ul>
