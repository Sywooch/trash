<div class="content-wrap content-pad-bottom">
	<div class="page-profile">
		<div class="prof-wrap">
			<?php $this->widget('application.components.likefifa.widgets.LfSalonLkTabsWidget', array(
				'currentTab' => 'masters',		
			)); ?>
			<div class="prof-cont">
				<?php $form=$this->beginWidget('CActiveForm', array(
					'id'=>'master-form-edit-photo',
					'enableAjaxValidation'=>false,
					'htmlOptions' => array(
						'enctype' => 'multipart/form-data',
					),
				)); ?>
				<?php $this->widget('application.components.likefifa.widgets.LfSalonLkMenuWidget', array(
					'actions' => array('masters' => 'Мастера салона', 'salonlk/masters/add' => 'Добавить мастера'),
					'currentAction' => $this->action->id,
					'model' => $model,
				)); ?>
				<div class="prof-rht">
					<table>
						<tr>
							<td style="padding-right:36px;">
								<div class="prof-head-inp">Фотография:</div>
								<div class="prof-photo_add">
									<?php if ($work->isUploaded('image')):?>
										<div id="load-img_wrap"><img src="<?php echo $work->preview('full'); ?>" alt="<?php echo $work->image; ?>" title="<?php echo $work->image; ?>" /></div>
									<?php else:?>
										<div class="prof-photo_add_cover" id="load-img_wrap"></div>
									<?php endif?>
									<?php if ($work->isUploaded('image')):?><div class="prof-photo_add_over"><span>Изменить фотографию</span></div><?php endif?>
									<?php echo $form->fileField($work,'image'); ?>
								</div>
								<div id="load-img_name"></div>
								<a href="<?php echo $this->createUrl('salonlk/masters/edit/'.$master->id); ?>" class="det-back png">вернуться в  галерею</a>
							</td>
							<td width="290">
								<div class="prof-inp_marg spec-selector">
									<div class="prof-head-inp">Раздел:</div>
									<?php echo LfHtml::activeDropDownList($work,'specialization_id', $master->getSpecListItems());?>
									<div class="prof-note-inp" style="padding:2px 0 0 10px;">например, маникюр</div>
								</div>
								<div class="prof-inp_marg service-selector">
									<div class="prof-head-inp">Подраздел:</div>
									<?php echo LfHtml::activeDropDownList($work,'service_id', LfService::model()->getListItems());?>
									<div class="prof-note-inp" style="padding:2px 0 0 10px;">например, французский маникюр</div>
								</div>
								<input type='hidden' name='redirect_link' value='' class='redirect_link' />
								<div class="prof-btn_next" style="text-align:left; padding:0;">
									<div class="button button-blue"><span>Сохранить</span></div>
									<?php echo CHtml::submitButton('Сохранить'); ?>
								</div>
							</td>
						</tr>
					</table>
				</div>
				<div class="clearfix"></div>
				<?php $this->endWidget(); ?>
			</div>
		</div>
	</div>
</div>