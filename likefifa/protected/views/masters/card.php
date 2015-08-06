				<script>
					$("a[rel^='prettyPhoto']").prettyPhoto({theme:'dark_rounded', social_tools: false});
				</script>
				<div onclick="map.closeBalloon();" class="map-popup_close"></div>
				<div class="map-popup">
					<div class="search-res_item">
						<div class="search-res_left">
							<?php if($model->isUploaded()){?>
								<a href="<?php echo $model->getProfileUrl(); ?>"><img width="97" class="search-res_ph" src="<?php echo $model->avatar(); ?>" /></a>
							<?php }else{ ?>
								<?php if($model->gender == LfMaster::GENDER_FEMALE){?>
									<div class="det-left_noph"></div>
								<?php }else{ ?>
									<div class="det-left_noph det-left_noph_male"></div>
								<?php } ?>
							<?php } ?>
							<?php if($model->getExperienceName()):?><p>Опыт <?php echo $model->getExperienceName(); ?></p><?php endif;?>
							<a href="<?php echo $model->getProfileUrl(); ?>#opinion" class="search-res_op"><span class="link">отзывы</span> <span class="count png"><?php echo count($model->opinions); ?></span></a>
							<div class="time">
								<?php if($model->hrs_wd_to||$model->hrs_wd_from){?><p>Будни:</p><?php }?>
								<p><span><?php if($model->hrs_wd_from) {?>с <?php echo $model->hrs_wd_from;} if($model->hrs_wd_to){?> до <?php echo $model->hrs_wd_to;} ?></span></p>
								<?php if($model->hrs_we_to||$model->hrs_we_from){?></span>Выходные:</p><?php }?>
								<p><span><?php if($model->hrs_we_from) {?>с <?php echo $model->hrs_we_from;} if($model->hrs_we_to){?> до <?php echo $model->hrs_we_to;} ?></span></p>
							</div>
						</div>
						<div class="search-res_centr">
							<div class="rating"><span class="stars png"><span style="width: <?php echo $model->getRating() / 5 * 100; ?>%" class="png"></span></span><?php echo $model->getRating(); ?></div>
							<div class="search-res_name"><a href="<?php echo $model->getProfileUrl(); ?>"><?php echo $model->getFullName(); ?></a></div>
							<?php if ($model->salon): ?>
								<div class="det-left_place">мастер салона <a href="<?php echo $model->salon->getProfileUrl(); ?>">&laquo;<?php echo $model->salon->name; ?>&raquo;</a></div>
							<?php endif; ?>
							<?php if ($model->specializations): ?><p><strong>Услуги:</strong> <?php echo $model->getSpecsConcatenated(); ?></p><?php endif; ?>
							<?php if($address = $model->getFullAddress()):?>
								<p class="metro"><?php if ($model->undergroundStation): ?><i class="icon-metro png metro-l_<?php echo $model->undergroundStation->undergroundLine->id; ?>"></i><?php endif; ?><?php echo $address; ?></p>
							<?php endif;?>
							<?php if ($model->has_departure): ?><p><strong>Выезд:</strong> возможен</p><?php endif; ?>

							<div
								class="button button-blue btn-appointment"
								data-gatype="click-click_on_map"
								 <?php if ($model->salon) { ?>
									 data-salon-id="<?php echo $model->salon->id; ?>"
								 <?php } ?>
								 data-id="<?php echo $model->id; ?>"><span>Записаться</span></div>
							<?php $i=0; if ($model->works):?>
								<div class="search-res_works">
									<?php $works = $model->works; if (count($works) > 3) { $works = array_slice($works, 0, 3); } ?>
									<?php foreach ($works as $work):  $i++;?><a href="<?php echo $work->preview('small'); ?>" rel="prettyPhoto[gallery<?php echo $model->id; ?>]"<?php if ($i == 1) { ?> class="search-res_works_f"<?php } ?>><img src="<?php echo $work->preview('small'); ?>" width="110" /></a><?php if ($i == 3) {$i=0;} ?><?php endforeach; ?>
								</div>
								<div class="search-res_works_a"><a href="<?php echo $model->getProfileUrl(); ?>">все работы мастера (<?php echo count($model->works); ?>)</a></div>
							<?php else:?>
								<br/>
								<br/>
							<?php endif; ?>
							<div class="popup-note popup-abuse"></div>
							<a class="abuse-link" href="#" data-id="<?php echo $model->id; ?>">пожаловаться</a>
						</div>
						<div class="clearfix"></div>
						<!--a href="" class="search-res_map png">как добраться</a-->
					</div>
				</div>
				<div class="popup-arr png"></div>