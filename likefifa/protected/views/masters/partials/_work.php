<?php
/**
 * @var MastersController $this
 * @var LfWork            $data
 * @var LfMaster          $model
 * @var int               $index
 */
?>
	<div class="item<?php echo $index == 0 || $index % 5 == 0 ? ' first' : '' ?>">
		<div class="det-works_wrap">
			<a
				class="det-works_img"
				rel="prettyPhoto[gallery1]"
				title="<?php echo CHtml::encode($data->service->name); ?>"
				href="<?php echo $data->preview('full'); ?>"
				master-id="<?php echo $model->id; ?>"
				data-service-id="<?php echo $data->service->id; ?>"
				data-spec-id="<?php echo $data->specialization->id; ?>"
				data-work-id="<?php echo $data->id ?>"
				data-master-pic="<?php echo $data->master->avatar(); ?>"
				data-master-link="<?php echo $data->master->getProfileUrl(); ?>"
				data-master-name="<?php echo $data->master->getFullName(); ?>"
				<?php if ($data->price): ?>data-service-price="<?php echo $data->price->getPriceFormatted(); ?>"<?php endif; ?>
				>
				<img
					width="183"
					alt="<?php $alt = $data->alt ? : $data->service->name;
					echo CHtml::encode($alt); ?>"
					src="<?php echo $data->preview('big'); ?>"
					/>
			</a>

			<?php $this->widget(
				'likefifa\components\likefifa\widgets\WorkVkWidget',
				[
					'master' => $model,
					'work'   => $data,
				]
			) ?>
		</div>
		<div class="det-price_wrap">
			<table class="tbl-price price-no-border">
				<tr>
					<td><span><?php echo $data->service->name; ?></span></td>
				</tr>
				<tr>
					<td class="td-cost">
					<span>
						<?php
						if ($data->price) {
							echo
							$data->price && $data->price->price ?
								$data->price->getPriceFormatted() . ' <i>р.</i>' : '';
							if (!empty($data->price->service->unit)) {
								echo '<i>/ ' . $data->price->service->unit . '</i>';
							}
						} else {
							if ($model->salon) {
								if ($salon_price =
									$model->salon->getPriceSalonFormatted(
										$data->service->id,
										$model->salon->id
									)
								) {
									echo $salon_price . ' <i>р.</i>';
								}
							}
						}
						?>
					</span>
					</td>
				</tr>
			</table>
		</div>
	</div>

<?php if (($index + 1) % 5 == 0 || $index + 1 == count($model->works)): ?>
	<div class="clearfix"></div>
<?php endif; ?>