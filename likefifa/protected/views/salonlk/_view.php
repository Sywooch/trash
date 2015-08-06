<?php
/**
 * @var LfMaster $data
 */
?>

<div class="search-res_item" id="master<?php echo $data->id; ?>">
					<div class="search-res_left">
					
						<?php if($data->isUploaded()){?>
							<img width="97" class="search-res_ph" src="<?php echo $data->avatar().'?123'; ?>" alt="<?php echo $data->getFullName(); ?>" />
						<?php }else{ ?>
							<?php if($data->gender == LfMaster::GENDER_FEMALE){?>
								<div class="det-left_noph"></div>
							<?php }else{ ?>
								<div class="det-left_noph det-left_noph_male"></div>
							<?php } ?>
						<?php } ?>
						<a href="<?php echo $data->getProfileUrl(); ?>#opinion" class="search-res_op"><span class="link">отзывы</span> <span class="count png"><?php echo count($data->opinions); ?></span></a>
					</div>
					<div class="search-res_rht">
						<p><strong>Время работы:</strong></p>
						<div class="time">
							<p><span>с <?php echo $data->hrs_wd_from; ?> до <?php echo $data->hrs_wd_to; ?></span>будни:</p>
							<p><span>с <?php echo $data->hrs_we_from; ?> до <?php echo $data->hrs_we_to; ?></span>выходные:</p>
						</div>
					</div>
					<div class="search-res_centr">
						<div class="rating"><span class="stars png"><span style="width: <?php echo $data->getRating() / 5 * 100; ?>%" class="png"></span></span><?php echo $data->getRating(); ?></div>
						<div class="search-res_name"><a href="./edit/<?php echo $data->id ?>"><?php echo $data->getFullName(); ?></a></div>
						<?php if ($data->specializations): ?><p><strong>Услуги:</strong> <?php echo $data->getSpecsConcatenated(); ?></p><?php endif; ?>


						
						
						<?php if ($prices = $data->getSalonPrices()): ?>
							<table width="100%" class="tbl-price">
								<?php $i = 0; while ($price = array_shift($prices)): ?>
									<tr>
										<td><span><?php echo $price->service->name;  ?></span></td>
										<td class="td-cost">
											<?php if ($price->price): ?>
												<span>
													<?php if ($price->price_from): ?>от <?php endif; ?><?php echo $price->getPriceFormatted() ?> <i>р.</i>
													<?php if(!empty($price->service->unit)): ?>
														<i>/ <?php echo $price->service->unit ?></i>
													<?php endif; ?>
												</span>
											<?php endif?>
										</td>
									</tr>
								<?php $i++; if ($i > 4) break; ?>
								<?php endwhile;?>
							</table>
							<?php if ($prices): ?>
							<div id="tbl-price-<?php echo $data->id; ?>" class="tbl-price_full">
								<table width="100%" class="tbl-price">
									<?php while ($price = array_shift($prices)): ?>
										<tr>
											<td>
												<span><?php echo $price->service->name; ?></span>
											</td>
											<td class="td-cost">
												<?php if ($price->price): ?>
														<span>
															<?php if ($price->price_from): ?>от <?php endif; ?>
															<?php echo $price->getPriceFormatted() ?> <i>р.</i>
															<?php if(!empty($price->service->unit)): ?>
																<i>/ <?php echo $price->service->unit ?></i>
															<?php endif; ?>
														</span>
												<?php endif?>
											</td>
										</tr>
									<?php endwhile;?>
								</table>
							</div>
							<div class="tbl-cost_btn_all"><span data-price-id="<?php echo $data->id; ?>">весь прайс-лист<i class="arr"></i></span></div>
							<?php endif; ?>
						<?php endif; ?>
				


						<?php $i=0; if ($data->works):?>
							<div class="search-res_works">
								<?php $works = $data->works; if (count($works) > 3) { $works = array_slice($works, 0, 3); } ?>
								<?php foreach ($works as $work):  $i++;?><a href="<?php echo $work->preview('full'); ?>" title="<?php echo $work->service->name; ?>" rel="prettyPhoto[gallery<?php echo $data->id; ?>]"<?php if ($i == 1) { ?> class="search-res_works_f"<?php } ?>><img src="<?php echo $work->preview('small'); ?>" alt="<?php echo $work->service->name; ?>" width="110" /></a><?php if ($i == 3) {$i=0;} ?><?php endforeach; ?>
							</div>
							<div class="search-res_works_a"><a href="<?php echo $data->getProfileUrl(); ?>#works">все работы мастера (<?php echo count($data->works); ?>)</a></div>
						<?php endif; ?>
					</div>
					<div class="clearfix"></div>
					<div class="salon-profile-res__btn_edit">
						<a class="button button-blue" href="./edit/<?php echo $data->id ?>/"><span>Редактировать</span></a>
						<a class="button button-pink" href="./delete/<?php echo $data->id ?>/"><span>Удалить</span></a>
					</div>
				</div>