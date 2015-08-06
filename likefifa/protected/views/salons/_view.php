<?php
/**
 * @var SalonsController $this
 * @var LfSalon          $data
 */
?>

<div class="search-res_item search-res_item_salon" id="salon<?php echo $data->id; ?>">
	<div class="search-res_left" style="position:relative;">

		<?php if ($data->isUploaded()): ?>
			<?php echo CHtml::link(
				CHtml::image($data->avatar(), $data->getFullName(), ['class' => 'search-res_ph', 'width' => '97']),
				$data->getProfileUrl()
			); ?>
		<?php else: ?>
			<div class="det-left_noph"></div>
		<?php endif; ?>

		<a href="<?php echo $data->getProfileUrl(); ?>#opinion" class="search-res_op">
			<span class="link">отзывы</span>
			<span class="count png"><?php echo count($data->opinions); ?></span>
		</a>
		<span class="show-rating-popup">?</span>

		<div class="popup-note popup-rating popup-opinions-card">
			<div class="popup-note_cont">
				<p>Отзывы собираются нами в ходе телефонного и электронного опроса клиентов, посетивших
					мастеров портала LikeFifa.</p>
			</div>
			<div class="popup-arr"></div>
		</div>
	</div>
	<div class="search-res_rht">
		<div
			class="button button-blue btn-appointment"
			data-salon-id="<?php echo $data->id; ?>"
			data-spec-id="<?php echo $specialization ? $specialization->id : ''; ?>"
			data-service-id="<?php echo $service ? $service->id : ''; ?>"
			data-gatype="salon-click_on_short"
			><span>Записаться</span></div>
		<div class="time">
			<p>
			<span>
				<?php if ($data->hrs_wd_from) { ?>с <?php echo $data->hrs_wd_from;
				}
				if ($data->hrs_wd_to) {
					?> до <?php echo $data->hrs_wd_to;
				}
				if ($data->hrs_wd_to || $data->hrs_wd_from){
				?>
			</span>
				будни:
			</p><?php } ?>
			<p>
			<span><?php if ($data->hrs_we_from) { ?>с <?php echo $data->hrs_we_from;
				}
				if ($data->hrs_we_to) {
					?> до <?php echo $data->hrs_we_to;
				}
				if ($data->hrs_we_to || $data->hrs_we_from){
				?>
			</span>выходные:
			</p><?php } ?>
		</div>
	</div>
	<div class="search-res_centr">
		<div class="rating">
		<span class="stars png">
			<span style="width: <?php echo $data->getRating() / 5 * 100; ?>%" class="png"></span>
		</span>
			<?php echo $data->getRating(); ?>
			<span class="show-rating-popup">?</span>

			<div class="popup-note popup-rating popup-rating-card">
				<div class="popup-note_cont">
					<p>Рейтинг формируется на основе отзывов клиентов о качестве работы салона.</p>
				</div>
				<div class="popup-arr"></div>
			</div>
		</div>
		<div class="search-res_name"><a href="<?php echo $data->getProfileUrl(); ?>"><?php echo $data->name; ?></a>
		</div>
		<div class="search-res_name__masters">
			<a href="<?php echo $data->getProfileUrl(); ?>#masters">мастера салона
				(<?php echo count($data->masters); ?>)</a>
		</div>

		<?php if ($data->getFullAddress()): ?>
			<p class="metro">
				<?php if ($data->undergroundStation): ?>
					<a href="<?php echo $this->createSearchUrl(
						$specialization,
						$service,
						$hasDeparture,
						array($data->undergroundStation)
					); ?>"><i
							class="icon-metro png metro-l_<?php echo $data->undergroundStation->undergroundLine->id; ?>"></i><?php echo $data->undergroundStation->name; ?>
					</a>,
				<?php endif; ?>
				<?php echo $data->getShortAddress(); ?>
			</p>
		<?php endif; ?>
		<?php if ($data->prices): ?>

			<?php
			$prices = LfPrice::model()->getPrices(null, $data, $specialization, $service, false);
			$this->renderPartial(
				'//partials/_prices',
				[
					'data'           => $data,
					'prices'         => $prices,
					'all'            => false,
					'specialization' => $specialization,
					'service'        => $specialization
				]
			)
			?>
		<?php endif; ?>
		<?php
		if ($data->works): ?>
			<div class="search-res_works not-all-works">
				<?php $works = $data->getFilteredWorks($specialization, $service); ?>
				<?php $this->renderPartial(
					'_view_works',
					['data'           => $data,
					 'works'          => $works,
					 'specialization' => $specialization,
					 'service'        => $service,
					 'all'            => false
					]
				) ?>
			</div>
			<div class="search-res_works_a">
				<a href="<?php echo $data->getProfileUrl(); ?>#photo">все фотографии салона
					(<?php echo count($data->works); ?>)
				</a>
			</div>
		<?php endif; ?>
	</div>
	<div class="clearfix"></div>
	<div class="search-res_complain">
		<div class="popup-note popup-abuse"></div>
		<a class="abuse-link" href="#" data-salon-id="<?php echo $data->id; ?>">пожаловаться</a></div>
</div>