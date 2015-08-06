<?php
/**
 * @var SiteController $this
 * @var LfWork[]       $bestWorks
 * @var Sector[]       $sectors
 */
?>
<div class="content-wrap" style="z-index:3;">
	<form action="<?php echo $this->createRedirectUrl(); ?>" method="GET" class="quick-search-main" id="search-main">
		<input type="hidden" id="stations" name="stations" value=""/>
		<input type="hidden" id="specialization" name="specialization" value=""/>
		<input type="hidden" id="service" name="service" value=""/>

		<span class="label">Я ищу</span>

		<div class="form-inp">
			<div class="form-placeholder">Укажите услугу</div>
			<input type="text" value="" name="query" id="search-suggest" />
		</div>

		<?php if (Yii::app()->activeRegion->isMoscow()) { ?>
			<span class="label">возле</span>

			<?php if(Yii::app()->mobileDetect->isMobile()) { ?>
				<div class="form-inp">
					<div class="form-placeholder">Введите название станции</div>
					<input type="text" value="" name="metro-suggest" id="metro-suggest"/>
				</div>
			<?php } else {  ?>
				<div class="form-inp" id="select-metro">
					<div class="form-select">Любого метро</div>
					<div class="form-select-arr form-select-icon png"></div>
					<div id="selected-metro_popup" class="metro-no-value">
						<i class="arr"></i>
						<div></div>
					</div>
				</div>
			<?php } ?>
		<?php } else { ?>
			<span class="label">в</span>

			<div class="form-inp city-selector">
				<div class="form-placeholder-city">Городе</div>
				<input type="hidden" id="inp-select-popup-city" name="city" value=""/>

				<div class="form-select-over" data-select-popup-id="select-popup-city"></div>
				<div class="form-select form-select" id="cur-select-popup-city">

				</div>
				<div class="form-select-arr png"></div>
				<div class="form-select-popup" id="select-popup-city">
					<div class="form-select-popup-long">
						<?php foreach (Yii::app()->activeRegion->getModel()->activeCities as $city) { ?>
							<span class="item" data-value="<?php echo $city->id; ?>"><?php echo $city->name; ?></span>
						<?php } ?>
					</div>
				</div>
			</div>
		<?php } ?>

		<input type="submit" class="main-search-submit" value="Искать"/>

		<div class="button button-pink" id="main-search-sbmt"><img
				src="<?php echo Yii::app()->homeUrl; ?>i/icon-search.png" class="png" style="margin-top:3px;"/></div>
	</form>

	<?php foreach ($sectors as $sector) { ?>
		<div class="cat-m-item">
			<div class="cat-m-pad">
				<div class="cat-m-head"><a href="<?php echo $sector->group->getLinkForMain(); ?>"
										   class="spec-main"><?php echo $sector->name; ?></a></div>
				<div class="cat-m-links">
					<?php foreach ($sector->specializations as $specialization): ?>
						<div><a href="<?php echo $specialization->getSearchUrl(
							); ?>"><?php echo $specialization->name; ?></a></div>
					<?php endforeach ?>
				</div>
			</div>
		</div>
	<?php } ?>

	<div class="clearfix"></div>
</div>
<div class="sep-map png">

<?php if (Yii::app()->activeRegion->isMoscow()) { ?>
	<a href="<?php echo $this->createMapUrl(); ?>" id="main-map-angle" class="png"><img
			src="<?php echo Yii::app()->homeUrl; ?>i/main-corner-angle.png" class="png"/>

		<div class="txt png"></div>
		<div class="map png"></div>
	</a>
	<div class="cut png"></div>
	<?php } ?>

	<span>Работы наших мастеров</span></div>
<div class="content-wrap content-pad-bottom gal-big">
	<div class="gal-list det-works">
		<?php $i = 0;
		foreach ($bestWorks as $work): ?>
			<div class="gal-item<?php if ((($i++) % 3) === 0) { ?> first<?php } ?>">
				<div class="gal-bl">
					<a
						master-id="<?php echo $work->master->id; ?>"
						data-service-id="<?php echo $work->service->id; ?>"
						data-spec-id="<?php echo $work->specialization->id; ?>"
						data-master-pic="<?php echo $work->master->avatar(); ?>"
						data-master-link="<?php echo $work->master->getProfileUrl(); ?>"
						data-master-name="<?php echo $work->master->getFullName(); ?>"
						<?php if ($work->price): ?>data-service-price="<?php echo $work->price->getPriceFormatted(); ?>"<?php endif; ?>
						class="gal-photo"
						rel="prettyPhoto[gallery1]"
						href="<?php echo $work->preview('full'); ?>"
						title="<?php $alt = $work->alt ? : $work->service->name;
						echo $alt; ?>"
						>
						<img
							width="309"
							src="<?php echo $work->preview('big'); ?>"
							alt="<?php echo $work->service->name; ?>"
							/>
					</a>

					<div class="gal-pr">
						<div><span><?php echo $work->service->name; ?></span></div>
						<span>
							<?php echo $work->price->getPriceFormatted(); ?> <span>р.</span>
							<?php if(!empty($work->price->service->unit)): ?>
								<span>/ <?php echo $work->price->service->unit ?></span>
							<?php endif; ?>
						</span>
						<i class="gal-pr_shw_t"></i><i class="gal-pr_corn png"></i>
					</div>
					<div class="gal-author">
						<div class="gal-author_i">
							<a href="<?php echo $work->master->getProfileUrl(); ?>">
								<?php if($work->master->isUploaded()):?>
									<img
										width="97"
										alt="<?php echo $work->master->getFullName(); ?>"
										src="<?php echo $work->master->avatar(); ?>"/>
								<?php else: ?>
									<?php if($work->master->gender == LfMaster::GENDER_FEMALE):?>
										<div class="det-left_noph"></div>
									<?php else: ?>
										<div class="det-left_noph det-left_noph_male"></div>
									<?php endif; ?>
								<?php endif; ?>
							</a>

							<div class="gal-author_rating">Рейтинг: <span><?php echo $work->master->getRating(
									); ?></span></div>
						</div>
						<div class="gal-author_txt">
							<h5><a href="<?php echo $work->master->getProfileUrl(
								); ?>"><?php echo $work->master->getFullName(); ?></a></h5>

							<p><strong>Услуги:</strong> <?php echo $work->master->getSpecsConcatenated(); ?></p>

							<div class="gal-author_pad">
								<p class="gal-author_metro"><?php if ($work->master->undergroundStation &&
										$work->master->undergroundStation->undergroundLine
									): ?><i
										class="icon-metro metro-l_<?php
											echo $work->master->undergroundStation->undergroundLine->id; ?>"></i><?php
											endif; ?> <?php echo $work->master->getFullAddress(); ?></p>
								<?php if ($work->master->has_departure): ?>
									<p><strong>Выезд:</strong> возможен</p>
								<?php endif ?>
							</div>
						</div>
						<div class="clearfix"></div>
					</div>
				</div>
			</div>
		<?php endforeach ?>

		<div class="clearfix"></div>
		<div class="gal-link-more"><a href="<?php echo $this->createGalleryUrl(); ?>">больше фотографий</a></div>
	</div>
</div>

<script type="text/javascript" src="<?php echo Yii::app()->homeUrl; ?>js/jquery.jsonSuggest.js?<?php echo RELEASE_MEDIA; ?>"></script>
<script type="text/javascript">
	var suggest;
	$(function () {
		suggest = new SearchSuggest();
		suggest.formId = 'search-main';
		suggest.initSpec('search-suggest');
		<?php if(Yii::app()->mobileDetect->isMobile()): ?>
		suggest.initMetro('metro-suggest');
		<?php endif; ?>

		initCardLikes();
	});
</script>