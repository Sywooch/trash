<?php
/**
 * @var MastersController   $this
 * @var LfMaster $data
 * @var CActiveDataProvider $dataProvider
 * @var int $page
 * @var int $pageSize
 */
?>
<?php foreach ($dataProvider->getData() as $data) { ?>
	<div class="left-item" data-id="<?php echo $data->id; ?>">
		<div class="name"><a href="<?php echo $data->getProfileUrl(); ?>"><?php echo $data->getFullName(); ?></a></div>
		<?php if ($data->city_id == 1): ?>
			<p><?php if ($data->undergroundStation): ?><span
					class="icon-metro png metro-l_<?php echo $data->undergroundStation->undergroundLine->id; ?>"></span><?php endif; ?><?php echo $data->getFullAddress(
				); ?></p>
		<?php else: ?>
			<p><?php echo $data->getFullAddress()
					?
					'г. ' .
					$data->city->name .
					', ' .
					$data->getFullAddress()
					:
					'г. ' .
					$data->city->name; ?></p>
		<?php endif; ?>
		<?php $i = 0;
		$prices = !$service ? array() : array($data->getPriceForService($service->id)); ?>
		<?php if ($prices): ?>
			<table width="100%" class="tbl-price">
				<?php while ($price = array_pop($prices)): ?>
					<tr>
						<td><span><?php echo $price->service->name; ?></span></td>
						<td class="td-cost">
							<?php if ($price->price): ?>
								<span>
									<?php echo $price->getPriceFormatted() ?> <i>р.</i>
									<?php if(!empty($price->service->unit)): ?>
										<i>/ <?php echo $price->service->unit ?><i>
									<?php endif; ?>
								</span>
							<?php endif ?>
						</td>
					</tr>
					<?php $i++; ?>
				<?php endwhile; ?>
			</table>
		<?php endif; ?>
	</div>
<?php } ?>

<?php if($dataProvider->itemCount == $pageSize): ?>
	<div class="left-item next-page-item">
		<a class="lazy-load-next" href="<?php echo $this->getAsidePagerLink($page); ?>"></a>
	</div>
<?php endif; ?>