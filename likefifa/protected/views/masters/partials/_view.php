<?php
/**
 * @var MastersController $this
 * @var LfMaster          $data
 * @var LfService         $service
 * @var LfSpecialization  $specialization
 */
?>

<?php $this->forMasters(); ?>
<div class="search-res_item" id="master<?php echo $data->id; ?>">
	<div class="search-res_left">

		<?php if ($data->isUploaded()) { ?>
			<a href="<?php echo $data->getProfileUrl(); ?>"><img width="97" class="search-res_ph"
																 src="<?php echo $data->avatar(); ?>"
																 alt="<?php echo $data->getFullName(); ?>"/></a>
		<?php } else { ?>
			<?php if ($data->gender == LfMaster::GENDER_FEMALE) { ?>
				<div class="det-left_noph"></div>
			<?php } else { ?>
				<div class="det-left_noph det-left_noph_male"></div>
			<?php } ?>
		<?php } ?>
		<?php if ($data->getExperienceName()): ?><p>Опыт <?php echo $data->getExperienceName(); ?></p><?php endif; ?>
		<div class="opinions-card">
			<a href="<?php echo $data->getProfileUrl(); ?>#opinion" class="search-res_op">
				<span class="link">отзывы</span>
				<span class="count png"><?php echo $data->opinionsCount; ?></span>
			</a>
			<span class="show-rating-popup">?</span>

			<div class="popup-note popup-rating popup-opinions-card">
				<div class="popup-note_cont">
					<p>Отзывы собираются нами в ходе телефонного и электронного опроса клиентов, посетивших мастеров
						портала LikeFifa.</p>
				</div>
				<div class="popup-arr"></div>
			</div>
		</div>
	</div>
	<div class="search-res_rht">
		<div
			class="button button-blue btn-appointment"
			<?php if ($data->salon): ?>data-salon-id="<?php echo $data->salon->id; ?>"<?php endif; ?>
			data-id="<?php echo $data->id; ?>"
			data-spec-id="<?php echo $specialization ? $specialization->id : ''; ?>"
			data-service-id="<?php echo $service ? $service->id : ''; ?>"
			data-gatype="click-click_on_short"
			><span>Записаться</span></div>
		<div class="time">
			<p><span><?php if ($data->hrs_wd_from) { ?>с <?php echo $data->hrs_wd_from;
					}
					if ($data->hrs_wd_to) {
						?> до <?php echo $data->hrs_wd_to;
					}
					if ($data->hrs_wd_to || $data->hrs_wd_from){
					?></span>будни:</p><?php } ?>
			<p><span><?php if ($data->hrs_we_from) { ?>с <?php echo $data->hrs_we_from;
					}
					if ($data->hrs_we_to) {
						?> до <?php echo $data->hrs_we_to;
					}
					if ($data->hrs_we_to || $data->hrs_we_from){
					?></span>выходные:</p><?php } ?>
		</div>
		<!--a href="" class="map-link png">показать на карте</a-->
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
					<p>Рейтинг специалистов формируется на основе отзывов клиентов о качестве работы мастера.</p>
				</div>
				<div class="popup-arr"></div>
			</div>
		</div>
		<div class="search-res_name">
			<?php echo CHtml::link($data->getFullName(), $data->getProfileUrl()); ?>
		</div>
		<?php if ($data->salon): ?>
			<div class="search-res_salon">
				<p style="color:#989898;">
					мастер салона
					<i>
						<b>
							<?php echo CHtml::link(
								'&laquo;' . $data->salon->name . '&raquo;',
								$data->salon->getProfileUrl()
							) ?>
						</b>
					</i>
				</p>
			</div>
		<?php endif; ?>

		<?php if ($data->getFullAddress()): ?>
			<p class="metro">
				<?php $address = $data->getShortAddress();
				if ($data->undergroundStation): ?>
					<a
						href="<?php echo $this->createSearchUrl(
							$specialization,
							$service,
							$hasDeparture,
							array($data->undergroundStation)
						); ?>">
						<i class="icon-metro png metro-l_<?php echo $data->undergroundStation->undergroundLine->id; ?>"></i>
						<?php echo $data->undergroundStation->name; ?>
					</a>
					<?php if ($address) {
						echo ',';
					} ?>
				<?php endif; ?>
				<?php echo $address; ?>
			</p>
		<?php endif; ?>

		<?php if ($data->has_departure) { ?>
			<div class="depart-card">
				<strong>Выезд:</strong> возможен
				<span class="show-rating-popup">?</span>

				<div class="popup-note popup-rating popup-depart-card">
					<div class="popup-note_cont">
						<p>Обратите внимание, что стоимость услуги с выездом может отличаться от указанной в анкете.
							Уточняйте конечную стоимость у мастера.</p>
					</div>
					<div class="popup-arr"></div>
				</div>
			</div>
		<?php } ?>
		<?php
		if ($data->pricesCount || $data->salon) {
			if ($this->top10 == $this::TOP10_SECOND_LEVEL) {
				$prices = LfPrice::model()->getPrices($data, null, $specialization, null, false);
			} else {
				$prices = LfPrice::model()->getPrices($data, null, $specialization, $service, false);
			}

			$this->renderPartial(
				"//partials/_prices",
				[
					'data'           => $data,
					'prices'         => $prices,
					'all'            => false,
					'specialization' => $specialization,
					'service'        => $service
				]
			);
		}
		?>
		<?php
		if ($data->worksCount > 0 && ($specialization == null || $specialization->isAllowPhoto())): ?>
			<div class="search-res_works det-works not-all-works">
				<?php $works =
					$data->getFilteredWorks(
						$specialization,
						$service,
						false,
						// увеличиваем количество видимых фото в карточке, если мастер внутри просмотра салона
						$this instanceof MastersController ? 3 : 5
					); ?>
				<?php $this->renderPartial(
					'//masters/partials/_view_works',
					[
						'data'           => $data,
						'works'          => $works,
						'specialization' => $specialization,
						'service'        => $service,
						'all'            => false,
						'count'          => $this instanceof MastersController ? 3 : 5,
					]
				) ?>
			</div>
			<?php if ($data->worksCount > 0) { ?>
				<div class="search-res_works_a">
					<a href="<?php echo $data->getProfileUrl(); ?>#works">
						все работы мастера (<?php echo $data->worksCount; ?>)
					</a>
				</div>
			<?php } ?>
		<?php endif; ?>
	</div>
	<div class="clearfix"></div>
	<div class="search-res_complain">
		<div class="popup-note popup-abuse"></div>
		<a class="abuse-link" href="#" data-id="<?php echo $data->id; ?>">пожаловаться</a></div>
</div>