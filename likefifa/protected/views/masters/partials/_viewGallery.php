<?php
/**
 * @var LfWork $data
 * @var integer $index
 */
?>
<div class="in-bl gallery-list_adaptive__item<?php if (($index % 4) ===
	0
) { ?> gallery-list_adaptive__item-first<?php } ?>">
	<?php $this->widget(
		'likefifa\components\likefifa\widgets\WorkVkWidget',
		[
			'master' => $data->master,
			'work'   => $data,
		]
	) ?>
	<a
		master-id="<?php echo $data->master->id; ?>"
		data-service-id="<?php echo $data->service->id; ?>"
		data-spec-id="<?php echo $data->specialization->id; ?>"
		data-master-pic="<?php echo $data->master->avatar(); ?>"
		data-master-link="<?php echo $data->master->getProfileUrl(); ?>"
		data-master-name="<?php echo $data->master->getFullName(); ?>"
		<?php if ($data->price): ?>data-service-price="<?php echo $data->price->getPriceFormatted(); ?>"<?php endif; ?>
		data-work-id="<?php echo $data->id ?>"
		rel="prettyPhoto[gallery1]"
		href="<?php echo $data->preview('full'); ?>"
		class="gallery-list_adaptive__pic"
		title="<?php $alt = $data->alt ? : $data->service->name;
		echo htmlspecialchars($alt); ?>"
		>
		<img src="<?php echo $data->preview('thumbFull'); ?>" alt="<?php echo htmlspecialchars($data->service->name); ?>"/>
	</a>
	<a href="<?php echo $data->master->getProfileUrl(); ?>" class="gallery-list_adaptive__author-wrap">
		<span class="gallery-list_adaptive__author-pic"><img width="97"
															 src="<?php echo $data->master->avatar(); ?>"
															 alt="<?php echo $data->master->getFullName(); ?>"/></span>
		<span class="gallery-list_adaptive__author"><?php echo $data->master->getFullName(); ?></span>
		<?php echo $data->service->name; ?>
		<?php if ($data->price): ?><span
			class="gallery-list_adaptive__price"><?php echo $data->price->getPriceFormatted(); ?>
			руб.</span><?php endif; ?>
	</a>
</div>