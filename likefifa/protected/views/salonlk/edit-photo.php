<div class="content-wrap content-pad-bottom">
	<div class="page-profile">
		<div class="prof-wrap">
			<?php $this->widget('application.components.likefifa.widgets.LfSalonLkTabsWidget', array(
				'currentTab' => 'profile',		
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
					'actions' => $this->actions,
					'currentAction' => 'photo',	
					'model' => $salon,
				)); ?>
				<div class="prof-rht">
					<table>
						<tr>
							<td style="padding-right:36px;">
								<div class="prof-head-inp">Фотография:</div>
								<div class="prof-photo_add">
									<?php if (!$photo->isNewRecord):?>
										<div id="load-img_wrap"><img src="<?php echo $photo->preview('big'); ?>" alt="<?php echo $photo->image; ?>" title="<?php echo $photo->image; ?>" /></div>
									<?php else:?>
										<div class="prof-photo_add_cover" id="load-img_wrap"></div>
									<?php endif?>
									<?php if (!$photo->isNewRecord):?><div class="prof-photo_add_over"><span>Изменить фотографию</span></div><?php endif?>
									<?php echo $form->fileField($photo,'image'); ?>
								</div>
								<div id="load-img_name"></div>
								<a href="<?php echo $this->createUrl('photo'); ?>" class="det-back png">вернуться в  галерею</a>
							</td>
							<td width="290">
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