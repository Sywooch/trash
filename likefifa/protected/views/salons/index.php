<?php
Yii::app()->clientScript->registerScriptFile(
	Yii::app()->assetManager->publish(
		Yii::getPathOfAlias('application.vendors.malsup.form') . '/jquery.form.js',
		false,
		-1,
		YII_DEBUG
	),
	CClientScript::POS_HEAD
);
?>

<div class="content-wrap content-pad-bottom">
			<?php if (isset($_GET["cv"])) { ?><div class="back-to-lk"><a href="/salonlk" class="button button-pink"><span>Вернуться в личный кабинет</span></a></div><?php } ?>
			<div class="det-back">
			<?php if ($searchUrl): ?>
				<a href="<?php echo $searchUrl; ?>"> вернуться к результатам поиска</a>
			<?php else: ?>
				<a href="<?php echo $this->createUrl('site/index'); ?>"> на главную</a>
			<?php endif; ?>
			</div>
			<div class="det-left det-left_c__salon">
				<div class="det-left_ph search-res_item_salon">
					<?php if($model->isUploaded()) { ?>
						<img
							width="147"
							src="<?php echo $model->avatar(); ?>?<?php echo rand(); ?>"
							alt="<?php echo $model->name; ?>"
						/>
					<?php } else { ?>
						<div class="det-left_noph"></div>
					<?php } ?>
	
					<div class="det-left_rating">
						<div class="stars png"><span style="width:<?php echo $model->getRatingPercent(); ?>%" class="png"></span></div>
						Рейтинг: <span><?php echo $model->getRating(); ?></span>
						<span class="show-rating-popup">?</span>
						<div class="popup-note popup-rating popup-rating-card">
							<div class="popup-note_cont">
								<p>Рейтинг формируется на основе отзывов клиентов о качестве работы салона.</p>
							</div>
							<div class="popup-arr"></div>
						</div>
					</div>
				</div>
				<div class="det-left_c">
					<h1>Салон красоты "<?php echo $model->name; ?>"</h1>
					<div class="det-left_spec">
						<?php $specs = $model->getSpecsSplitted(); ?>
						<?php if ($specs[1]) echo $specs[0].','; else echo $specs[0];?>
						<?php if ($specs[1]): ?>
							<div class="det-left_txt_s" style="display: none;">
							<?php echo $specs[1]; ?>
							</div>
							<a class="det-left_txt_s_l" href="#">больше</a>
						<?php endif;?>
					</div>
					<div class="salon-det_phone">
						<div
							class="button button-blue btn-appointment"
							data-salon-id="<?php echo $model->id; ?>"
							data-full="1"
							data-gatype="salon-click_on_large"
							><span>Записаться</span></div>
					</div>
					<div class="det-left_txt">
						<h3>Описание салона</h3>
						<?php $description = $model->getSplittedDescription(); ?>
						<p><?php echo $description[0]; ?></p>
						<?php if ($description[1]): ?>
							<div class="det-left_txt_f" style="display: none;">
								<p><?php echo $description[1]; ?></p>
							</div>
							<a class="det-left_txt_f_l" href="#">подробнее</a>
						<?php endif; ?>
					</div>	
					
				</div>
			</div>
			<div class="det-right">
				<div class="det-right_map">
					<script src="<?php echo Yii::app()->homeUrl; ?>js/map-card.js?<?php echo RELEASE_MEDIA; ?>" type="text/javascript"></script>
					<script type="text/javascript">
						<?php $center = json_encode($model->map_lat && $model->map_lng ? array($model->map_lat, $model->map_lng) : array(55.75150546844201, 37.616654052733395)); ?>
						<?php $zoom = json_encode($model->map_lat && $model->map_lng ? 15 : 11); ?>

						var map = null;
						$(document).ready(function() {
							map = new CardMap();
							map.center = <?php echo $center ?>;
							map.zoom = <?php echo $zoom ?>;
							map.balloonContent = <?php echo CJavaScript::encode('м.' . $model->getFullAddress())?>;
							map.completeCallback = function() {
								initCardLikes();
							};
							map.init();
						});
					</script>
					<div id="ya-map" style="height: 200px;"></div>
				</div>
				<?php if ($address = $model->getFullAddress()) { ?>
					<p>
						<strong>Адрес:</strong>
						<?php if ($model->undergroundStation) { ?>
							<span class="icon-metro png metro-l_<?php
								echo $model->undergroundStation->undergroundLine->id;
								?>"></span>
						<?php } echo $address; ?>
					</p>
				<?php } ?>
				<?php if ($model->hrs_wd_to || $model->hrs_wd_from || $model->hrs_wd_to || $model->hrs_wd_from) { ?>
					<div class="time">
						<?php if ($model->hrs_wd_to || $model->hrs_wd_from) { ?><p>будни:
							<span><?php if ($model->hrs_wd_from) { ?>с <?php echo $model->hrs_wd_from;
							}
							if ($model->hrs_wd_to) { ?> до <?php echo $model->hrs_wd_to;
							} ?></span></p><?php } ?>

						<?php if ($model->hrs_wd_to || $model->hrs_wd_from) { ?><p>выходные:
							<span><?php if ($model->hrs_we_from) { ?>с <?php echo $model->hrs_we_from;
							}
							if ($model->hrs_we_to) { ?> до <?php echo $model->hrs_we_to;
							} ?></span></p><?php } ?>
					</div>
				<?php } ?>
				<div class="soc-btn">
					<div class="soc-btn_item" style="width:130px;">
						<div id="fb-root"></div>
						<script>(function(d, s, id) {
						  var js, fjs = d.getElementsByTagName(s)[0];
						  if (d.getElementById(id)) return;
						  js = d.createElement(s); js.id = id;
						  js.src = "//connect.facebook.net/ru_RU/all.js#xfbml=1&appId=192869677440481";
						  fjs.parentNode.insertBefore(js, fjs);
						}(document, 'script', 'facebook-jssdk'));</script>
						<div class="fb-like" data-send="false" data-layout="button_count" data-width="450" data-show-faces="true"></div>
					</div>
					<div class="soc-btn_item" style="width:145px;">
						<div id="vk_like"></div>
					</div>
					<div class="soc-btn_item" style="width:105px;">
						<a href="https://twitter.com/share" class="twitter-share-button" data-lang="ru">Твитнуть</a>
						<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="det-right_complain">
					<div class="popup-note popup-abuse"></div>
					<a class="abuse-link" href="#" data-salon-id="<?php echo $model->id; ?>">пожаловаться</a>
				</div>
			</div>
			<div class="clearfix"></div>
			
			<script>
				var viewUrlHash = true;
			</script>
			<div class="salon-tabs">
				<a href="#price" class="act" id="salon-tabs_price"><span>Прайс</span></a>
				<a href="#masters" id="salon-tabs_masters"><span>Мастера (<?php echo count($model->masters); ?>)</span></a>
				<a href="#photo" id="salon-tabs_photo"><span>Фотогалерея (<?php echo count($model->photo); ?>)</span></a>
				<a href="#opinion" id="salon-tabs_opinion"><span>Отзывы (<?php echo count($model->opinions); ?>)</span></a>
			</div>	
			<div class="salon-tabs__wrap">
				<div class="salon-tabs_cont__item act" id="salon-tabs_cont__price">
					<?php $i = 0; $prices = $model->filledPrices('filledPrices:ordered'); ?>
					<?php if ($prices): ?>
						<?php $oldSpecId = null; ?>
						<div style="padding:0 20px 30px 20px;">
							<table width="100%" cellspacing="0" cellpadding="0">
								<tr>
									<td width="45%" class="td-vert-top">
										<?php $prices_pre = $prices; ?>
										<?php $j = 0; $k = 0; ?>
										<?php while ($price = array_shift($prices_pre)): ?>
											<?php if ($oldSpecId !== $price->service->specialization->id): 
												$oldSpecId = $price->service->specialization->id;
												$j++; ?>
											<?php endif; ?>
										<?php endwhile;?>
										<?php $oldSpecId = null; ?>
										<?php while ($price = array_shift($prices)): ?>
											<?php if ($oldSpecId !== $price->service->specialization->id): $oldSpecId = $price->service->specialization->id; ?>
												<?php if (ceil($j/2) == $k): ?>
														</table>
													</td>
													<td width="10%"></td>
													<td width="45%" class="td-vert-top">
												<?php else: ?>	
													<?php if ($i > 0): ?>
														</table>
													<?php endif; ?>
												<?php endif; ?>
												<?php $k++; ?>
												<table width="100%" class="tbl-price">
													<tr>
														<td class="tbl-price_salon__head">
															<?php echo $price->service->specialization->name; ?>
														</td>
													</tr>
											<?php endif ?>
											<tr>
												<td style='border: 0;'>
													<div class="price-card-container">
														<div 
															class='btn-appointment price-list-container'
															data-gatype="salon-click_on_price"
															data-service-id="<?php echo $price->service->id; ?>" 
															data-spec-id="<?php echo $price->service->specialization->id; ?>" 
															data-salon-id="<?php echo $model->id; ?>"
															data-full="1"
														>
															<div class='price-list-name'>
																<span><?php echo $price->service->name; ?></span>
															</div>
															<?php if ($price->price): ?>
																<div class='price-list-value'>
																	<?php echo $price->getPriceFormatted() ?> <i>р.</i>
																	<?php if(!empty($price->service->unit)): ?>
																		<i>/ <?php echo $price->service->unit ?></i>
																	<?php endif; ?>
																</div>
															<?php endif?>
															<div class='price-list-dotted'></div>
														</div>
														<div class="popup-note popup-rating popup-price-card">
															<div class="popup-note_cont">
																<p>Нажмите, чтобы записаться на эту услугу</p>
															</div>
															<div class="popup-arr"></div>
														</div>
													</div>
												</td>
											</tr>
											<?php $i++; ?>
										<?php endwhile;?>
										</table>
									</td>
								</tr>
							</table>
						</div>
					<?php endif; ?>
				</div>

				<div class="salon-tabs_cont__item" id="salon-tabs_cont__photo">
					<?php $photos = $model->photo; ?>
					<?php $countPhotos = count($photos); ?>
					<?php if($photos){ ?>
						<div id="works" class="det-works">
							<div class="det-line_sep"><span>Работы салона (<?php echo $countPhotos; ?>)</span></div>
							<?php $i=0; while ($photo = array_pop($photos)): $i++; ?>
								<div class="item<?php if(!(($i - 1) % 5)){ ?> first<?php } ?>">
									<div class="det-works_wrap">
										<a class="det-works_img" href="<?php echo $photo->preview('full'); ?>" rel="prettyPhoto[gallery1]"><img width="183" src="<?php echo $photo->preview('big'); ?>" /></a>
										<div class="gal-like png" data-work-id="<?php echo $photo->id; ?>"></div>
									</div>
								</div>
								<?php if (!($i % 5)){ ?><div class="clearfix"></div><?php } ?>
								<?php if ($i > 14) break; ?>
							<?php endwhile; ?>
							<div class="clearfix"></div>
							
						</div>
					<?php } ?>
				</div>

				<div class="salon-tabs_cont__item" id="salon-tabs_cont__opinion">
					<?php
					$this->widget(
						'application.components.likefifa.widgets.LfOpinionsWidget',
						array(
							'model' => $model,
							'opinion' => $opinion,
							'where' => 'в салон',
						)
					);
					?>
					<div class="clearfix"></div>
				</div>
				
				<div class="salon-tabs_cont__item" id="salon-tabs_cont__masters">
					<div class="det-line_sep"><span>Мастера <?php if ($model->masters): ?>(<?php echo count($model->masters); ?>)<?php endif; ?></span></div>
					
					<?php $k = 0; ?>
					<?php foreach ($model->masters as $data): $k++; ?>
						<?php $this->renderPartial('//masters/partials/_view', ['data' => $data, 'specialization' => null, 'service' => null, 'hasDeparture' => false, 'all' => 5]); ?>
					<?php endforeach; ?>
				</div>
			</div>
				
			<div class="clearfix"></div>
		</div>
	</div>

		<?php if ($opinionSent): ?>
			<script type="text/javascript">
				$(function() {
					$('.det-com_form_success_window_container').show();
				});
			</script>
		<?php endif; ?>
