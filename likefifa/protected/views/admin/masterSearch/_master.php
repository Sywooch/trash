<?php
/**
 * @var MasterSearchController $this
 * @var LfMaster               $data
 * @var array                  $params
 */

/**
 * @var LfPrice[] $prices
 */
$prices = !$params['service'] ? array() : array($data->getPriceForService($params['service']->id));


?>

<div class="item">
	<h4>
		<?php echo CHtml::link(
			$data->getFullName(),
			$data->getProfileUrl(true),
			[
				'target' => '_blank',
			]
		) ?>
	</h4>

	<p>ID: <?php echo $data->id ?></p>

	<p>Рейтинг: <?php echo $data->rating ?></p>

	<p>Баланс: <?php echo $data->getBalance() ?></p>


	<?php if ($data->undergroundStation): ?>
		<p>
			Метро:
			<?php echo
				$data->undergroundStation->name . ' (' . $data->undergroundStation->undergroundLine->name . ')' ?>
		</p>
	<?php else: ?>
		<p>&nbsp;</p>
	<?php endif; ?>


	<?php if (!empty($data->add_info)): ?>
		<p>Комментарий: <?php echo $data->add_info ?></p>
	<?php else: ?>
		<p>&nbsp;</p>
	<?php endif; ?>

	<?php if ($prices): ?>
		<ul class="unstyled">
			<?php foreach ($prices as $price): ?>
				<li>
					<?php echo $price->service->name; ?>
					<?php if ($price->price): ?>
						<em>
							<?php echo $price->getPriceFormatted() ?> <i>р.</i>
						</em>
					<?php endif ?>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</div>
