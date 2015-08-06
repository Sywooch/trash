<div class="gal-item<?php if(($index % 3) === 0){ ?> first<?php } ?>">
	<div class="gal-bl">
		<a class="gal-photo" href="<?php echo $data->master->getProfileUrl(); ?>"><img width="309" src="<?php echo $data->preview('small'); ?>" alt="<?php echo $data->service->name; ?>" /></a>
		<div class="gal-like png" data-work-id="<?php echo $data->id; ?>"><span class="gal-like_link">Мне нравится</span><span class="gal-like_num png"><?php echo $data->likes; ?></span></div>
		<div class="gal-pr">
			<div><span><?php echo $data->service->name; ?></span></div>
			<?php if ($data->price): ?>
				<span>
				<?php echo $data->price->getPriceFormatted(); ?> <span>р.</span>
					<?php if(!empty($data->price->service->unit)): ?>
						<span>/ <?php echo $data->price->service->unit ?></span>
					<?php endif; ?>
				</span>
			<?php endif; ?>
			<i class="gal-pr_shw_t"></i><i class="gal-pr_corn png"></i>
		</div>
		<div class="gal-author">
			<div class="gal-author_i">
				<a href="<?php echo $data->master->getProfileUrl(); ?>"><img width="97" src="<?php echo $data->master->avatar(); ?>" alt="<?php echo $data->master->getFullName(); ?>" /></a>
				<div class="gal-author_rating">Рейтинг: <span><?php echo $data->master->getRating(); ?></span></div>
			</div>
			<div class="gal-author_txt">
				<h5><a href="<?php echo $data->master->getProfileUrl(); ?>"><?php echo $data->master->getFullName(); ?></a></h5>
				<p><strong>Услуги:</strong> <?php echo $data->master->getSpecsConcatenated(); ?></p>
				<div class="gal-author_pad">
					<?php if ($address = $this->getFullAddress()): ?>
						<p class="gal-author_metro"><?php if(isset($data->master->undergroundStation->undergroundLine->id)): ?><i class="icon-metro metro-l_<?php echo $data->master->undergroundStation->undergroundLine->id; ?>"></i><?php endif;?> <?php echo $address; ?></p>
					<?php endif; ?>
					<?php if ($data->master->has_departure): ?>
						<p><strong>Выезд:</strong> возможен</p>
					<?php endif ?>
				</div>
			</div>
			<div class="clearfix"></div>
		</div>
	</div>
</div>