				<script>
					$("a[rel^='prettyPhoto']").prettyPhoto({theme:'dark_rounded', social_tools: false});
				</script>
				<div onclick="map.closeBalloon();" class="map-popup_close"></div>
				<div class="map-popup">
					<div class="search-res_item search-res_item_salon">
						<div class="search-res_left">
							<?php if($model->isUploaded()){?>
								<a href="<?php echo $model->getProfileUrl(); ?>"><img width="97" class="search-res_ph" src="<?php echo $model->avatar(); ?>" /></a>
							<?php }else{ ?>
								<div class="det-left_noph"></div>
							<?php } ?>
							<a href="<?php echo $model->getProfileUrl(); ?>#opinion" class="search-res_op"><span class="link">отзывы</span> <span class="count png"><?php echo count($model->opinions); ?></span></a>
							<div class="time">
								<p>Будни</p>
								<p><span>с <?php echo $model->hrs_wd_from; ?> до <?php echo $model->hrs_wd_to; ?></span></p>
								<p>Выходные</p>
								<p><span>с <?php echo $model->hrs_we_from; ?> до <?php echo $model->hrs_we_to; ?></span></p>
							</div>
						</div>
						<div class="search-res_centr">
							<div class="rating"><span class="stars png"><span style="width: <?php echo $model->getRating() / 5 * 100; ?>%" class="png"></span></span><?php echo $model->getRating(); ?></div>
							<div class="search-res_name"><a href="<?php echo $model->getProfileUrl(); ?>"><?php echo $model->name; ?></a></div>
							<?php if ($model->specializations): ?><p><strong>Услуги:</strong> <?php echo $model->getSpecsConcatenated(); ?></p><?php endif; ?>
							<?php if($address = $model->getFullAddress()):?>
								<p class="metro"><?php if ($model->undergroundStation): ?><i class="icon-metro png metro-l_<?php echo $model->undergroundStation->undergroundLine->id; ?>"></i><?php endif; ?><?php echo $address; ?></p>
							<?php endif; ?>
							<div
								class="button button-blue btn-appointment"
								data-gatype="salon-click_on_map"
								data-salon-id="<?php echo $model->id; ?>"
							><span>Записаться</span></div>
							<?php $i=0; if ($model->works):?>
								<div class="search-res_works">
									<?php $works = $model->works; if (count($works) > 3) { $works = array_slice($works, 0, 3); } ?>
									<?php foreach ($works as $work):  $i++;?><a href="<?php echo $work->preview('full'); ?>" rel="prettyPhoto[gallery<?php echo $model->id; ?>]"<?php if ($i == 1) { ?> class="search-res_works_f"<?php } ?>><img src="<?php echo $work->preview('big'); ?>" width="110" /></a><?php if ($i == 3) {$i=0;} ?><?php endforeach; ?>
								</div>
								<div class="search-res_works_a"><a href="<?php echo $model->getProfileUrl(); ?>">все работы салона (<?php echo count($model->works); ?>)</a></div>
							<?php else:?>
								<br/>
								<br/>
							<?php endif; ?>
							<div class="popup-note popup-abuse"></div>
							<a class="abuse-link" href="#" data-salon-id="<?php echo $model->id; ?>">пожаловаться</a>
						</div>
						<div class="clearfix"></div>
						<!--a href="" class="search-res_map png">как добраться</a-->
					</div>
				</div>
				<div class="popup-arr png"></div>