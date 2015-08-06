<h1>Карта сайта</h1>

<ul class="sitemap">
	<li>
		<a href="<?php echo $diagnostic->getUrl();?>/">
			<strong><?php echo $diagnostic->getFullName(); ?></strong>
		</a>
		<ul>
			<li>
				<?php if (Yii::app()->city->isMoscow()) {?>
					<strong>В округе Москвы:</strong>
					<ul>
						<?php foreach($areas as $area){?>
							<li>&ndash; <a href="<?php echo $diagnostic->getUrl();?>/area/<?php echo $area->rewrite_name;?>/"><?php echo $area->name;?></a>
								<ul>
								<?php foreach($districts as $district){?>
									<?php if($district->id_area == $area->id){?>
									<li>&ndash; <a href="<?php echo $diagnostic->getUrl();?>/area/<?php echo $area->rewrite_name . '/' . $district->rewrite_name;?>/"><?php echo $district->name;?></a></li>
									<?php }?>
								<?php }?>
								</ul>
							</li>
						<?php }?>
					</ul>
				<?php } else {?>
					<strong>В районе <?php echo Yii::app()->city->getCity()->title_genitive;?>:</strong>
					<ul>
						<?php foreach($districts as $district){?>
							<li>&ndash; <a href="<?php echo $diagnostic->getUrl();?>/district/<?php echo $district->rewrite_name;?>/"><?php echo $district->name;?></a></li>
						<?php }?>
					</ul>
				<?php }?>
				<?php if(!empty($stations)) {?>
					<strong>На станции метро:</strong>
					<ul>
						<?php foreach ($stations as $station) { ?>
							<li>&ndash; <a href="<?php echo $diagnostic->getUrl();?>/station/<?php echo $station['rewrite_name'];?>/"><?php echo $station['name']; ?></a></li>
						<?php } ?>
					</ul>
				<?php }?>
			</li>
		</ul>
	</li>

</ul>