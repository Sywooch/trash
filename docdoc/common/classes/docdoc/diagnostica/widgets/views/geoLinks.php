<?php if (!empty($stationLinks)) {?>
	<div>
		<p><b>Диагностические центры рядом с метро</b></p>
		<ul>
			<?php foreach ($stationLinks as $link) {?>
				<li><a href="<?=$link['href']?>"><?=$link['name']?></a></li>
			<?php }?>
		</ul>
		<?php if (!empty($districtLinks)) {?>
			<p>в районе</p>
			<ul>
				<?php foreach ($districtLinks as $link) {?>
					<li><a href="<?=$link['href']?>"><?=$link['name']?></a></li>
				<?php }?>
			</ul>
		<?php }?>
		<br />

	</div>
<?php } else if (!empty($districtLinks)) { ?>
	<div>
		<p><b>Диагностические центры рядом в районе</b></p>
		<ul>
			<?php foreach ($districtLinks as $link) {?>
				<li><a href="<?=$link['href']?>"><?=$link['name']?></a></li>
			<?php }?>
		</ul>
		<br />
	</div>
<?php } ?>