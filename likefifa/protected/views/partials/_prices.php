<?php
/**
 * @var FrontendController $this
 * @var LfMaster|LfSalon   $data
 * @var LfPrice[]          $prices
 * @var boolean            $all
 * @var LfSpecialization   $specialization
 * @var LfService          $service
 */

$showMoreLink = false;
if (count($prices) > LfPrice::CARD_COUNT_PRICE) {
	$showMoreLink = true;
}
$showToggleButton = false;

if (count($prices) > 0): ?>
	<div class="card-prices-container">
		<?php foreach ($prices as $i => $price): ?>
	<?php if ($i == LfPrice::CARD_COUNT_PRICE):
	$showToggleButton = true; ?>
		<div id="tbl-price-<?= $data->id ?>" class="tbl-price_full">
			<?php endif; ?>

			<div class="price-list-container">
				<div
					title="Нажмите, чтобы записаться на эту услугу"
					class="btn-appointment price-list-container<?= $data instanceof LfMaster
						? ' price-list-container-master' : '' ?>"
					data-gatype="<?= $data instanceof LfSalon ? 'salon-' : 'click' ?>-click_on_price"
					data-service-id="<?= $price->service->id ?>"
					data-spec-id="<?= $price->service->specialization->id ?>"
					data-<?= $data instanceof LfSalon ? 'salon-' : '' ?>id="<?= $data->id ?>"
					>
					<div class="price-list-name">
						<span><?= $price->service->name; ?></span>
					</div>
					<?php if ($price->price): ?>
						<div class="price-list-value">
							<?= $price->getPriceFormatted() ?> <i>р.</i>
							<?php if (!empty($price->service->unit)): ?>
								<i>/ <?= $price->service->unit ?></i>
							<?php endif; ?>
						</div>
					<?php endif ?>
					<div class="price-list-dotted"></div>
				</div>
				<div class="popup-note popup-rating popup-price-card">
					<div class="popup-note_cont">
						<p>Нажмите, чтобы записаться на эту услугу</p>
					</div>
					<div class="popup-arr"></div>
				</div>
			</div>
			<?php endforeach; ?>
			<?php if ($showToggleButton): ?>
		</div>
	<?php endif; ?>
	</div>
	<?php if ($all == false && $showToggleButton): ?>
		<div class="tbl-cost_btn_all">
			<span data-price-id="<?= $data->id ?>" data-type="<?= $data instanceof LfMaster ? 'master' : 'salon' ?>"
				  data-spec="<?= $specialization ? $specialization->id : null ?>"
				  data-service="<?= $service ? $service->id : null ?>">
				весь прайс-лист<i class="arr"></i>
			</span>
		</div>
	<?php endif; ?>
<?php endif; ?>