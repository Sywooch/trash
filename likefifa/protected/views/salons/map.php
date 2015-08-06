<?php
/**
 * @var MastersController $this
 */
?>

	<script type="text/javascript">
		var map = null;
		$(function () {
			map = new CatalogMap();
			map.init();
		});
	</script>

	<div id="content">
		<div id="left-col-list">
			<?php if ($service): ?>
				<div class="sort-wrap">
					<div class="sort">
						<?php
						$d = $sorting === 'price' ? $reverseDirection : 'asc';
						$class = $sorting === 'price' ? ($d === 'desc' ? '' : 'desc') : '';
						?>

						<?php if ($sorting === 'price'): ?><b><?php endif; ?>
							<noindex><a href="<?php echo $this->createMapUrl(
									$specialization,
									$service,
									$hasDeparture,
									'price',
									$d
								); ?>" class="<?php echo $class; ?>" rel="nofollow"><span>по цене</span></a></noindex>
							<?php if ($sorting === 'price'): ?></b><?php endif; ?>

					</div>
					упорядочить по:
				</div>
			<?php endif; ?>
			<div class="search-res_head">
			<span class="txt">
				<em></em>
				<span class="current-entity-count" data-url="<?php echo $this->forSalons()->createCountUrl(
					$specialization,
					$service,
					$hasDeparture,
					$stations,
					$area,
					$districts,
					$city,
					'map'
				); ?>"></span>
				<a
					href="<?php echo $this->forMasters()->createMapUrl(
						$specialization,
						$service,
						$hasDeparture,
						$stations,
						$area,
						$districts,
						$city
					); ?>"
					data-url="<?php echo $this->forMasters()->createCountUrl(
						$specialization,
						$service,
						$hasDeparture,
						$stations,
						$area,
						$districts,
						$city,
						'map'
					); ?>"
					class="another-entity-count"
					></a>
			</span>
			</div>
			<div class="left-col-cont">
				<a class="lazy-load-next" href="<?php echo $this->getAsidePagerLink(-1); ?>"></a>
			</div>
		</div>
		<div id="col-right-map">
			<div class="shw-top png"></div>
			<div class="shw-left png"></div>
			<a href="<?php echo $this->createSearchUrl($this->specialization, $this->service, $this->hasDeparture); ?>"
			   class="link-back"><span>вернуться к списку</span></a>

			<div id="YMapsID" style="height:100%;">
			</div>
		</div>
	</div>
<?php $this->forDefault(); ?>