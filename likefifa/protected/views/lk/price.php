<?php
/**
 * @var LkController $this
 * @var LfMaster     $model
 */

$this->renderPartial('_header', compact('model'));

$existsSpec = [];
?>

<div class="content-wrap content-pad-bottom">
	<div class="page-profile">
		<div class="prof-wrap">
			<?php $this->widget(
				'application.components.likefifa.widgets.LfLkTabsWidget',
				array(
					'currentTab' => 'profile',
				)
			); ?>
			<div class="prof-cont">
				<?php $form = $this->beginWidget(
					'CActiveForm',
					array(
						'id'                   => 'master-form-personal',
						'enableAjaxValidation' => false,
						'htmlOptions'          => array(),
					)
				); ?>
				<?php $this->widget(
					'application.components.likefifa.widgets.LfLkMenuWidget',
					array(
						'actions'       => $this->actions,
						'currentAction' => $this->action->id,
						'model'         => $model,
					)
				); ?>
				<div class="prof-rht">
					<?php if (count($model->groups)): ?>
						<div class="prof-note-important">
							<p class="ico png" style="padding-top:0;"><strong>Обращаем Ваше внимание!</strong> Данный
								прайс-лист будет отображаться в Вашей анкете на сайте, поэтому:</p>

							<div>
								<strong>1)</strong> В графе "цена" укажите актуальную цену на процедуру.<br/>
								<strong>2)</strong> Если стоимость услуги рассчитывается по количеству выполненных
								единиц (например, роспись ногтей), то стоимость указывается за 1 единицу (роспись 1
								ногтя).<br/>
								<strong>3)</strong> В случае если итоговая стоимость услуги рассчитывается только после
								проведения услуги (например, тату) , выберите галочку "от", тогда данная цена в Вашем
								прейскуранте будет обозначена как "от 1200 руб".
							</div>
						</div>

						<div class="prof-price_edit_wrap">
							<?php
							$tree = LfSpecialization::model()->getTree($model);
							$serviceIds = $model->getRelationIds('services');
							foreach ($tree as $treeGroup) {
								?>
								<div class="price-group-title-lk">Услуги <?php echo $treeGroup["genitive_one"]; ?>:
								</div>
								<?php foreach ($treeGroup["spec"] as $specId => $spec) { ?>
									<?php if (!empty($spec['services']) && !array_key_exists($specId, $existsSpec)) { ?>
										<?php $existsSpec[$specId] = true; ?>
										<div class="prof-inp_marg">
											<div class="form-inp prof-price-select">
												<div class="form-select"><?php echo $spec['name']; ?></div>
												<div class="form-select-arr png"></div>
											</div>
											<div class="prof-price_edit">
												<?php
												foreach ($spec['services'] as $serviceId => $serviceParams) {
													$id = 'service-' . $serviceId;
													$price = $model->getPriceForService($serviceId);
													?>
													<div class="prof-price_edit_i">
														<div class="prof-price_edit_cost<?php if (in_array(
															$serviceId,
															$serviceIds
														)
														) { ?> price-inp-show<?php } ?>">
															<?php if ($serviceParams['price_from'] == 1): ?>
																<?php echo LfHtml::checkBox(
																	'LfMaster[prices][isFrom][' .
																	$serviceId .
																	']',
																	$price &&
																	$price->price_from,
																	array(
																		'value' => 1,
																		'id'    => $id . '_fr',
																		'label' => 'от'
																	)
																); ?>
															<?php endif; ?>
															<div class="form-inp"><?php echo CHtml::textField(
																	'LfMaster[prices][values][' . $serviceId . ']',
																	$price ? $price->price : null
																); ?></div>
															р.
														</div>
														<div class="prof-price_edit_val">
															<?php echo LfHtml::checkBox(
																'LfMaster[prices][serviceIds][' .
																$serviceId .
																']',
																in_array($serviceId, $serviceIds),
																array(
																	'value' => $serviceId,
																	'id'    => $id,
																	'label' =>
																		$serviceParams['name'] .
																		(!empty($serviceParams['unit']) ?
																			' (' . $serviceParams['unit'] . ')' : '')
																)
															); ?>
														</div>
													</div>

												<?php } ?>
											</div>
										</div>
									<?php } ?>
								<?php } ?>
							<?php } ?>
						</div>
					<?php else: ?>
						<div class="prof-note-important" style="padding-bottom:11px;">
							<p class="ico png" style="padding-top:8px; margin:0;">Для того, чтобы заполнить прайс-лист,
								выберите свою специализацию <a
									href="<?php echo $this->createUrl('lk/index'); ?>">здесь</a>.</p>
						</div>
					<?php endif; ?>
				</div>
				<input type='hidden' name='redirect_link' value='' class='redirect_link'/>

				<div class="clearfix"></div>
				<div class="prof-btn_next">
					<?php if (count($model->groups)): ?>
						<div class="button button-blue"><span>Сохранить</span></div>
						<?php echo CHtml::submitButton('Сохранить'); ?>
					<?php endif; ?>
				</div>
				<?php $this->endWidget(); ?>
			</div>
		</div>
	</div>
</div>
