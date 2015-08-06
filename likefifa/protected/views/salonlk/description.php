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
				<div class="prof-rht">
					<div class="prof-note-important">
						<div class="ico png" style="margin:6px 0 0;">В данном разделе Вы можете разместить краткое описание Вашего салона.</div>
					</div>
					<div class="prof-head-inp">Описание (не более 512 символов):</div>
					<?php echo LfHtml::activeTextArea($model,'description', array("rows" => "5")); ?>
				</div>
				<input type='hidden' name='redirect_link' value='' class='redirect_link' />
				<div class="prof-btn_next">
					<div class="button button-blue"><span>Сохранить</span></div>
					<?php echo CHtml::submitButton('Сохранить'); ?>
					<?php $this->endWidget(); ?>
				</div>	
			</div>
			
		</div>
		<div class="clearfix"></div>

	</div>
</div>