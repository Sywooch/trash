<div class="content-wrap content-pad-bottom">
	<div class="page-profile">
		<div class="prof-wrap">
			<?php $this->widget('application.components.likefifa.widgets.LfSalonLkTabsWidget', array(
				'currentTab' => 'profile',		
			)); ?>
			<div class="prof-cont">
				<?php $form=$this->beginWidget('CActiveForm', array(
					'id'=>'master-form-personal',
					'enableAjaxValidation'=>false,
					'htmlOptions' => array(	),
				)); ?>
				<?php $this->widget('application.components.likefifa.widgets.LfSalonLkMenuWidget', array(
					'actions' => $this->actions,
					'currentAction' => $this->action->id,
					'model' => $model,
				)); ?>

<!-- Верстка идентична ЛК мастера -->

				<div class="prof-rht">
					<div class="prof-note-important">
						<p style="padding-top:0;" class="ico png"><strong>Обращаем Ваше внимание!</strong> Данный прайс-лист будет отображаться в Вашей анкете на сайте, поэтому:</p>
						<div>
							<strong>1)</strong> В графе "цена" укажите актуальную цену на процедуру<br>
							<strong>2)</strong> В случае если Вы не можете указать точную цену, выберите графу "от", тогда данная цена в Вашем прейскуранте будет обозначена как "от 1200 руб"<br>
							<strong>3)</strong> Старайтесь максимально точно указывать цены на процедуры и не использовать графу "от". Данная графа значительно снижает колличестно новых клиентов.	
						</div>
					</div>
					<div class="prof-price_edit_wrap">
						<?php 
						$tree = LfSpecialization::model()->getSalonTree($model);
						$serviceIds = $model->getRelationIds('services');
						
						foreach ($tree as $specId => $spec) {
						?>
						<?php if(!empty($spec['services'])):?>
						<div class="prof-inp_marg">
							<div class="form-inp prof-price-select"><div class="form-select"><?php echo $spec['name']; ?></div><div class="form-select-arr png"></div></div>
							<div class="prof-price_edit">
								<?php
								foreach ($spec['services'] as $serviceId => $serviceParams) {
									$id = 'service-'.$serviceId;
									$price = $model->getPriceForService($serviceId);
								?>
								<div class="prof-price_edit_i">
									<div class="prof-price_edit_cost<?php if (in_array($serviceId, $serviceIds)){?> price-inp-show<?php } ?>">
										<?php if($serviceParams['price_from'] == 1): ?>
											<?php echo LfHtml::checkBox('LfSalon[prices][isFrom]['.$serviceId.']', $price && $price->price_from, array('value' => 1, 'id' =>$id.'_fr', 'label' =>'от'));?>
										<?php endif; ?>
										<div class="form-inp"><?php echo CHtml::textField('LfSalon[prices][values]['.$serviceId.']', $price ? $price->price : null);?></div>р.
									</div>
									<div class="prof-price_edit_val">
										<?php echo LfHtml::checkBox('LfSalon[prices][serviceIds]['.$serviceId.']', in_array($serviceId, $serviceIds), array('value' => $serviceId, 'id' =>$id, 'label' =>$serviceParams['name'] . (!empty($serviceParams['unit']) ? ' ('.$serviceParams['unit'].')' : ''))); ?>
									</div>
								</div>
																
								<?php } ?>
							</div>
						</div>
						<?php endif;?>
						<?php } ?>
					</div>

				</div>
				<input type='hidden' name='redirect_link' value='' class='redirect_link' />
				<div class="clearfix"></div>
				<div class="prof-btn_next">

					<div class="button button-blue"><span>Сохранить</span></div>
					<?php echo CHtml::submitButton('Сохранить'); ?>

				</div>
				<?php $this->endWidget(); ?>
			</div>
		</div>
	</div>
</div>
